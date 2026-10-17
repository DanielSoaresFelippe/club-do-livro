<?php

namespace App\Controllers;

use App\Models\ChatModel;
use App\Models\UsuarioModel;
use App\Models\MensagemModel;

class Chat extends BaseController
{
    protected ChatModel $chatModel;
    protected UsuarioModel $usuarioModel;
    protected MensagemModel $mensagemModel;

    public function __construct()
    {
        $this->chatModel     = new ChatModel();
        $this->usuarioModel = new UsuarioModel();
        $this->mensagemModel = new MensagemModel();
    }

    protected function getUsuarioLogado(): ?int
    {
        return session()->get('usuario_id');
    }

    public function index($idConversaSelecionada = null)
    {
        $meuId = $this->getUsuarioLogado();

        if (!$meuId) {
            return redirect()->to(base_url('login'));
        }

        $conversas = $this->chatModel->listarConversasDoUsuario($meuId);

        $mensagens = [];
        $conversaAtiva = null;

        if ($idConversaSelecionada !== null) {
            $conversaAtiva = $this->chatModel->find($idConversaSelecionada);

            if ($conversaAtiva) {
                $u1 = $conversaAtiva['id_usuario_dono'];
                $u2 = $conversaAtiva['id_usuario_interessado'];

                $mensagens = $this->mensagemModel
                    ->groupStart()
                        ->where('remetente_id', $u1)->where('destinatario_id', $u2)
                    ->groupEnd()
                    ->orGroupStart()
                        ->where('remetente_id', $u2)->where('destinatario_id', $u1)
                    ->groupEnd()
                    ->orderBy('criado_em', 'ASC')
                    ->findAll();
            }
        }

        $destinatario_id = null;
        $nomeOutroUsuario = 'Contato';
        $fotoPerfilOutro = null;

        if ($conversaAtiva) {
            $destinatario_id = ($conversaAtiva['id_usuario_dono'] == $meuId) 
                ? $conversaAtiva['id_usuario_interessado'] 
                : $conversaAtiva['id_usuario_dono'];

            $outroUsuario = $this->usuarioModel->find($destinatario_id);
            if ($outroUsuario) {
                $nomeOutroUsuario = $outroUsuario['nome'] ?? $outroUsuario['nome_usuario'] ?? 'Contato';
                $fotoPerfilOutro = $outroUsuario['foto_perfil'] ?? $outroUsuario['foto'] ?? null;
            }
        }

        $data = [
            'conversas'             => $conversas,
            'conversaAtiva'         => $conversaAtiva,
            'mensagens'             => $mensagens,
            'idConversaSelecionada' => $idConversaSelecionada,
            'meu_id'                => $meuId,
            'destinatario_id'       => $destinatario_id,
            'nomeOutroUsuario'      => $nomeOutroUsuario,
            'fotoPerfilOutro'       => $fotoPerfilOutro
        ];
        
        $data['usuarioLogado'] = $this->usuarioModel->find($meuId);
        return view('chat_server/chat', $data);
    }

    public function carregarConversa(int $destinatarioId)
    {
        $meuId = $this->getUsuarioLogado();

        if (!$meuId) {
            return $this->response->setStatusCode(401)->setJSON(['erro' => 'Não autorizado']);
        }

        $destinatario = $this->usuarioModel->find($destinatarioId);
        
        $idConversa = $this->request->getGet('id_conversa');

        if (!$idConversa) {
            $conversa = $this->chatModel->groupStart()
                                            ->where('id_usuario_dono', $meuId)
                                            ->where('id_usuario_interessado', $destinatarioId)
                                        ->groupEnd()
                                        ->orGroupStart()
                                            ->where('id_usuario_dono', $destinatarioId)
                                            ->where('id_usuario_interessado', $meuId)
                                        ->groupEnd()
                                        ->first();
            $idConversa = $conversa ? $conversa['id_conversa'] : null;
        }

        $mensagens = [];
        if ($idConversa) {
            $mensagens = $this->mensagemModel
                ->where('id_conversa', $idConversa)
                ->orderBy('criado_em', 'ASC')
                ->findAll();
        }

        return $this->response->setJSON([
            'destinatario' => $destinatario,
            'id_conversa'  => $idConversa,
            'mensagens'    => $mensagens
        ]);
    }

    public function iniciar(int $idLivro)
    {
        $meuId = $this->getUsuarioLogado();

        if (!$meuId) {
            return redirect()->to(base_url('login'))->with('erro', 'Faça login para conversar.');
        }

        $livroModel = new \App\Models\LivroModel();
        $livro = $livroModel->find($idLivro);

        if (!$livro) {
            return redirect()->back()->with('erro', 'Livro não encontrado.');
        }

        if ((int)$livro['id_usuario'] === (int)$meuId) {
            return redirect()->back()->with('erro', 'Você é o dono deste livro.');
        }

        $idDono = $livro['id_usuario'];

        $conversa = $this->chatModel->where('id_livro', $idLivro)
                                    ->where('id_usuario_dono', $idDono)
                                    ->where('id_usuario_interessado', $meuId)
                                    ->first();

        if (!$conversa) {
            $idConversa = $this->chatModel->insert([
                'id_livro'               => $idLivro,
                'id_usuario_dono'        => $idDono,
                'id_usuario_interessado' => $meuId
            ]);
        } else {
            $idConversa = $conversa['id_conversa'];
        }

        return redirect()->to(base_url('chat/' . $idConversa));
    }

    public function enviarMensagem()
    {
        $meuId = $this->getUsuarioLogado();

        if (!$meuId) {
            return $this->response->setStatusCode(401)->setJSON(['erro' => 'Não autorizado']);
        }

        $destinatarioId = $this->request->getPost('destinatario_id');
        $idConversa     = $this->request->getPost('id_conversa');
        $conteudo       = trim($this->request->getPost('conteudo') ?? '');

        if (empty($destinatarioId) || empty($conteudo)) {
            return $this->response->setStatusCode(400)->setJSON(['erro' => 'Mensagem ou destinatário inválidos']);
        }

        if (empty($idConversa)) {
            $chatModel = new ChatModel();
            
            $conversa = $chatModel->where('id_usuario_dono', $meuId)
                                ->where('id_usuario_interessado', $destinatarioId)
                                ->orWhere('id_usuario_dono', $destinatarioId)
                                ->where('id_usuario_interessado', $meuId)
                                ->first();

            if ($conversa) {
                $idConversa = $conversa['id_conversa'];
            }
        }

        $dados = [
            'id_conversa'     => $idConversa,
            'remetente_id'    => $meuId,
            'destinatario_id' => $destinatarioId,
            'conteudo'        => $conteudo,
            'criado_em'       => date('Y-m-d H:i:s')
        ];

        $idMensagem = $this->mensagemModel->insert($dados);

        if ($idMensagem) {
            $dados['id'] = $idMensagem;
            return $this->response->setJSON([
                'status'   => 'sucesso',
                'mensagem' => $dados
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON(['erro' => 'Erro ao salvar mensagem']);
    }

    // public function deletarMensagem(int $id_mensagem)
    // {
    //     $usuarioLogado = session()->get('usuario_id');

    //     $db = \Config\Database::connect();
    //     $mensagem = $db->table('mensagens')->where('id_mensagem', $id_mensagem)->get()->getRowArray();

    //     if ($mensagem && (int)$mensagem['id_remetente'] === (int)$usuarioLogado) {
    //         $db->table('mensagens')->where('id_mensagem', $id_mensagem)->delete();
    //         return $this->response->setJSON(['sucesso' => true]);
    //     }

    //     return $this->response->setJSON(['sucesso' => false, 'erro' => 'Não autorizado'], 403);
    // }
}