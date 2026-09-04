<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    protected function getUsuarioLogado(): ?int
    {
        return session()->get('usuario_id');
    }

    public function cadastrar()
    {
        $usuarioModel = new UsuarioModel();

        if (!$this->validate([
            'nome'      => 'required|min_length[2]|max_length[150]',
            'email'     => 'required|valid_email|is_unique[usuarios.email]',
            'senha'     => 'required|min_length[8]|regex_match[/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).+$/]',
            'telefone'  => 'permit_empty|min_length[8]|max_length[20]',
            'tipo'      => 'required|in_list[cliente,colaborador]',
        ], [
            'senha' => [
                'min_length'  => 'A senha precisa ter no mínimo 8 caracteres.',
                'regex_match' => 'A senha precisa ter 1 letra maiúscula, 1 número e 1 caractere especial.',
            ],
            'tipo' => [
                'required' => 'Selecione se você quer ler ou vender/doar livros.',
                'in_list'  => 'Opção de cadastro inválida.',
            ],
            'email' => [
                'is_unique' => 'Este e-mail já está cadastrado.',
            ],
        ])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $nomeFoto = null;
        $foto = $this->request->getFile('foto_perfil');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if (!in_array($foto->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'errors'  => ['foto_perfil' => 'A foto precisa ser JPG, PNG ou WEBP.'],
                ]);
            }
            if ($foto->getSize() > 2048 * 1024) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'errors'  => ['foto_perfil' => 'A foto deve ter no máximo 2MB.'],
                ]);
            }

            $nomeFoto = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/perfil', $nomeFoto);
        }

        $id = $usuarioModel->insert([
            'nome'        => $this->request->getPost('nome'),
            'email'       => $this->request->getPost('email'),
            'senha'       => password_hash($this->request->getPost('senha'), PASSWORD_DEFAULT),
            'telefone' => preg_replace('/\D/', '', $this->request->getPost('telefone')),
            'tipo'        => $this->request->getPost('tipo'),
            'foto_perfil' => $nomeFoto,
            'ativo'       => 1,
        ]);

        session()->set([
            'usuario_id'     => $id,
            'usuario_nome'   => $this->request->getPost('nome'),
            'usuario_tipo'   => $this->request->getPost('tipo'),
            'usuario_logado' => true,
        ]);

        return $this->response->setJSON([
            'success'  => true,
            'redirect' => base_url('livro/'),
            'usuario'  => [
                'id'    => $id,
                'nome'  => $this->request->getPost('nome'),
                'tipo'  => $this->request->getPost('tipo'),
                'foto'  => $nomeFoto ? base_url('uploads/perfil/' . $nomeFoto) : null,
            ],
        ]);
    }

    public function login()
    {
        $usuarioModel = new UsuarioModel();

        if(!$this->validate([
            'email' => 'required|valid_email',
            'senha' => 'required'
        ])) {
            return $this->response->setStatusCode(422)->setJSON([
                'sucess' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        $usuario = $usuarioModel->where('email', $email)->where('ativo', 1)->first();

        if(!$usuario || !password_verify($senha, $usuario['senha'])){
            return $this->response->setStatusCode(422)->setJSON([
                'sucess' => false,
                'errors' => ['login' => 'E-mail ou senha inválidos.'],
            ]);
        }

        session()->set([
            'usuario_id'     => $usuario['id_usuario'],
            'usuario_nome'   => $usuario['nome'],
            'usuario_tipo'   => $usuario['tipo'],
            'usuario_logado' => true,
        ]);

        return $this->response->setJSON([
            'sucess'   => true,
            'redirect' => base_url('livro/'),
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/'));
    }

    public function perfil()
    {
        if (!session()->get('usuario_logado')) {
            return redirect()->to(base_url('/'))->with('erro', 'Faça login para acessar sua carteirinha.');
        }
 
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find(session()->get('usuario_id'));
 
        if (!$usuario) {
            session()->destroy();
            return redirect()->to(base_url('/'));
        }
 
        return view('usuarios/carteirinha', ['usuario' => $usuario]);
    }
 
    public function dadosPerfil()
    {
        if (!session()->get('usuario_logado')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false]);
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find(session()->get('usuario_id'));

        if (!$usuario) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false]);
        }

        unset($usuario['senha']);
        $usuario['foto_url'] = $usuario['foto_perfil']
            ? base_url('uploads/perfil/' . $usuario['foto_perfil'])
            : null;

        return $this->response->setJSON(['success' => true, 'usuario' => $usuario]);
    }
 
    public function atualizarPerfil()
    {
        if (!session()->get('usuario_logado')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false]);
        }

        $id = session()->get('usuario_id');
        $usuarioModel = new UsuarioModel();

        $usuarioAtual = $usuarioModel->find($id);
        if (!$usuarioAtual) {
            session()->destroy();
            return $this->response->setStatusCode(401)->setJSON(['success' => false]);
        }

        $regras = [
            'nome'     => 'required|min_length[2]|max_length[150]',
            'email'    => "required|valid_email|is_unique[usuarios.email,id_usuario,{$id}]",
            'telefone' => 'permit_empty|min_length[8]|max_length[20]',
            'tipo'     => 'required|in_list[cliente,colaborador]',
        ];

        $senha = $this->request->getPost('senha');
        if (!empty($senha)) {
            $regras['senha'] = 'min_length[8]|regex_match[/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).+$/]';
        }

        if (!$this->validate($regras, [
            'senha' => [
                'min_length'  => 'A senha precisa ter no mínimo 8 caracteres.',
                'regex_match' => 'A senha precisa ter 1 letra maiúscula, 1 número e 1 caractere especial.',
            ],
            'tipo' => [
                'required' => 'Selecione se você quer ler ou vender/trocar livros.',
                'in_list'  => 'Opção inválida.',
            ],
        ])) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $dados = [
            'nome'     => $this->request->getPost('nome'),
            'email'    => $this->request->getPost('email'),
            'telefone' => preg_replace('/\D/', '', (string) $this->request->getPost('telefone')),
            'tipo'     => $this->request->getPost('tipo'),
            'foto_perfil' => $usuarioAtual['foto_perfil'], 
        ];

        if (!empty($senha)) {
            $dados['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        }

        $foto = $this->request->getFile('foto_perfil');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if (!in_array($foto->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'errors'  => ['foto_perfil' => 'A foto precisa ser JPG, PNG ou WEBP.'],
                ]);
            }
            if ($foto->getSize() > 2048 * 1024) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'errors'  => ['foto_perfil' => 'A foto deve ter no máximo 2MB.'],
                ]);
            }

            $nomeFoto = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/perfil', $nomeFoto);
            $dados['foto_perfil'] = $nomeFoto;
        }

        $sucesso = $usuarioModel->skipValidation(true)->update($id, $dados);

        if (!$sucesso) {
            log_message('error', 'Falha ao atualizar usuario ' . $id . ': ' . json_encode($usuarioModel->errors()));
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'errors'  => ['geral' => 'Não foi possível salvar as alterações.'],
            ]);
        }

        session()->set([
            'usuario_nome' => $dados['nome'],
            'usuario_tipo' => $dados['tipo'],
        ]);

        $usuarioAtualizado = $usuarioModel->find($id);
        unset($usuarioAtualizado['senha']);
        $usuarioAtualizado['foto_url'] = $usuarioAtualizado['foto_perfil']
            ? base_url('uploads/perfil/' . $usuarioAtualizado['foto_perfil'])
            : null;

        return $this->response->setJSON([
            'success' => true,
            'usuario' => $usuarioAtualizado,
        ]);
    }
 
     public function historico()
    {
        $idUsuario = $this->getUsuarioLogado();
        if (!$idUsuario) {
            return redirect()->to(base_url('login'));
        }

        $historicoModel = new \App\Models\HistoricoModel();

        return view('usuarios/historico', [
            'historico' => $historicoModel->listarDoUsuario($idUsuario),
        ]);
    }

    public function favoritos()
    {
        $idUsuario = $this->getUsuarioLogado();
        if (!$idUsuario) {
            return redirect()->to(base_url('login'));
        }

        $favoritoModel = new \App\Models\FavoritosModel();

        return view('usuarios/favoritos', [
            'favoritos' => $favoritoModel->listarDoUsuario($idUsuario),
        ]);
    }
}