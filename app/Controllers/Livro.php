<?php

namespace App\Controllers;

use App\Models\FavoritosModel;
use App\Models\LivroModel;
use App\Models\GeneroModel;
use App\Models\UsuarioModel;

class Livro extends BaseController
{
    protected LivroModel $livroModel;
    protected GeneroModel $generoModel;
    protected UsuarioModel $usuarioModel;
    protected FavoritosModel $favoritosModel;

    protected string $pastaUploads = 'assets/uploads/livros';

    public function __construct()
    {
        $this->livroModel  = new LivroModel();
        $this->generoModel = new GeneroModel();
        $this->usuarioModel = new UsuarioModel();
        $this->favoritosModel = new FavoritosModel();
    }

    protected function getUsuarioLogado(): ?int
    {
        return session()->get('usuario_id');
    }

    public function index()
    {
        $tipoTransacao = $this->request->getGet('tipo_transacao');
        $idGenero      = $this->request->getGet('genero');
        $localizacao   = $this->request->getGet('localizacao');
        $busca         = trim((string) $this->request->getGet('busca'));

        $localizacoes = (new LivroModel())
            ->distinct()
            ->select('localizacao')
            ->where('localizacao IS NOT NULL')
            ->where('localizacao !=', '')
            ->orderBy('localizacao', 'ASC')
            ->findAll();

        $livroModel = $this->livroModel;
        $livroModel->select('livros.*, generos.nome AS genero')
            ->join('generos', 'generos.id_genero = livros.id_genero')
            ->orderBy('livros.id_livro', 'DESC');

        if ($tipoTransacao) {
            $livroModel->where('livros.tipo_transacao', $tipoTransacao);
        }

        if ($idGenero) {
            $livroModel->where('livros.id_genero', (int) $idGenero);
        }

        if ($localizacao) {
            $livroModel->where('livros.localizacao', $localizacao);
        }

        if ($busca !== '') {
            $livroModel->groupStart()
                ->like('livros.titulo', $busca)
                ->orLike('livros.autor', $busca)
                ->groupEnd();
        }
        
        $livros = $livroModel->paginate(8, 'livros');
        $pager  = $livroModel->pager;

        return view('livro/index', [
            'livros'                 => $livros,
            'pager'                  => $pager,
            'generos'                => $this->generoModel->findAll(),
            'localizacoes'           => $localizacoes,
            'tipoSelecionado'        => $tipoTransacao,
            'generoSelecionado'      => $idGenero,
            'localizacaoSelecionada' => $localizacao,
            'buscaSelecionada'       => $busca,
        ]);
    }

    public function detalhes($id = null)
    {
        if ($id === null) {
            return redirect()->to(base_url('livro/nao-encontrado'));
        }

        $idUsuario = $this->getUsuarioLogado();
        $usuarioInteressado = $this->usuarioModel->find($idUsuario);
        $favoritado = $idUsuario ? $this->favoritosModel->estaFavoritado($idUsuario, (int) $id) : false;
        $livro = $this->livroModel->buscarComGenero((int) $id);

        if (!$livro) {
            return redirect()->to(base_url('livro'))
                ->with('erro', 'Livro não encontrado.');
        }

        $recomendados = $this->livroModel
            ->select('livros.*, generos.nome AS genero')
            ->join('generos', 'generos.id_genero = livros.id_genero')
            ->where('livros.id_genero', $livro['id_genero'])
            ->where('livros.id_livro !=', $livro['id_livro'])
            ->where('livros.status', 'disponivel')
            ->orderBy('livros.data_cadastro', 'DESC')
            ->findAll(4);

        if (empty($recomendados)) {
            $recomendados = $this->livroModel
                ->select('livros.*, generos.nome AS genero')
                ->join('generos', 'generos.id_genero = livros.id_genero')
                ->where('livros.id_livro !=', $livro['id_livro'])
                ->where('livros.status', 'disponivel')
                ->orderBy('livros.data_cadastro', 'DESC')
                ->findAll(4);
        }

        $recomendados = array_map(fn ($item) => [
            'id_livro' => $item['id_livro'],
            'titulo'   => $item['titulo'],
            'autor'    => $item['autor'],
            'genero'   => $item['genero'],
            'capa'     => $item['imagem_capa'] ?? base_url('assets/img/capa-padrao.png'),
        ], $recomendados);

        return view('livro/detalhes', [
            'livro'        => $livro,
            'recomendados' => $recomendados,
            'interessado'  => $usuarioInteressado,
            'favoritado'   => $favoritado,
        ]);
    }

    public function meusLivros()
    {
        helper('form_helper');

        $idUsuario = $this->getUsuarioLogado();

        if (!$idUsuario) {
            return redirect()->to(base_url('login'));
        }

        $livros = $this->livroModel->listarDoUsuario($idUsuario);

        return view('livro_meus', ['livros' => $livros]);
    }

    public function novo()
    {
        helper('form_helper');

        if (!$this->getUsuarioLogado()) {
            return redirect()->to(base_url('login'));
        }

        $generos = $this->generoModel->orderBy('nome', 'ASC')->findAll();

        return view('livro_form', [
            'livro'   => null,
            'generos' => $generos,
        ]);
    }

    public function salvar()
    {
        $idUsuario = $this->getUsuarioLogado();

        if (!$idUsuario) {
            return redirect()->to(base_url('login'));
        }

        $dados = $this->dadosDoFormulario();
        $dados['id_usuario'] = $idUsuario;
        $dados['status']     = 'disponivel';

        if (!$this->livroModel->validate($dados)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->livroModel->errors());
        }

        $errosNegocio = $this->livroModel->validarRegrasNegocio($dados);
        if (!empty($errosNegocio)) {
            return redirect()->back()->withInput()->with('errors', $errosNegocio);
        }

        $arquivoCapa = $this->processarUploadCapa();
        $dados['imagem_capa'] = $arquivoCapa !== false
            ? $arquivoCapa
            : base_url('assets/img/capa-padrao.png');

        $idLivro = $this->livroModel->insert($dados);

        if (!$idLivro) {
            return redirect()->back()->withInput()
                ->with('erro', 'Não foi possível cadastrar o livro.');
        }

        return redirect()->to(base_url('livro/detalhes/' . $idLivro))
            ->with('sucesso', 'Livro cadastrado com sucesso!');
    }

    public function editar($id = null)
    {
        helper('form_helper');
        
        $idUsuario = $this->getUsuarioLogado();

        if (!$idUsuario) {
            return redirect()->to(base_url('login'));
        }

        $livro = $this->livroModel->find((int) $id);

        if (!$livro) {
            return redirect()->to(base_url('livro/meus-livros'))
                ->with('erro', 'Livro não encontrado.');
        }

        if ((int) $livro['id_usuario'] !== $idUsuario) {
            return redirect()->to(base_url('livro/meus-livros'))
                ->with('erro', 'Você não tem permissão para editar este livro.');
        }

        $generos = $this->generoModel->orderBy('nome', 'ASC')->findAll();

        return view('livro_form', [
            'livro'   => $livro,
            'generos' => $generos,
        ]);
    }

    public function atualizar($id = null)
    {
        $idUsuario = $this->getUsuarioLogado();

        if (!$idUsuario) {
            return redirect()->to(base_url('login'));
        }

        $livro = $this->livroModel->find((int) $id);

        if (!$livro || (int) $livro['id_usuario'] !== $idUsuario) {
            return redirect()->to(base_url('livro/meus-livros'))
                ->with('erro', 'Você não tem permissão para editar este livro.');
        }

        if ($livro['status'] === 'reservado') {
            return redirect()->to(base_url('livro/detalhes/' . $id))
                ->with('erro', 'Não é possível editar um livro reservado.');
        }

        $dados = $this->dadosDoFormulario();

        if (!$this->livroModel->validate(array_merge($dados, ['id_usuario' => $idUsuario]))) {
            return redirect()->back()->withInput()
                ->with('errors', $this->livroModel->errors());
        }

        $errosNegocio = $this->livroModel->validarRegrasNegocio($dados);
        if (!empty($errosNegocio)) {
            return redirect()->back()->withInput()->with('errors', $errosNegocio);
        }

        $arquivoCapa = $this->processarUploadCapa();
        if ($arquivoCapa !== false) {
            $dados['imagem_capa'] = $arquivoCapa;

            if (!empty($livro['imagem_capa'])) {
                $this->removerArquivoCapa($livro['imagem_capa']);
            }
        } elseif (empty($livro['imagem_capa'])) {
            $dados['imagem_capa'] = base_url('assets/img/capa-padrao.png');
        }

        $this->livroModel->update((int) $id, $dados);

        return redirect()->to(base_url('livro/detalhes/' . $id))
            ->with('sucesso', 'Livro atualizado com sucesso!');
    }

    public function excluir($id = null)
    {
        $idUsuario = $this->getUsuarioLogado();

        if (!$idUsuario) {
            return redirect()->to(base_url('login'));
        }

        $livro = $this->livroModel->buscarComGenero((int) $id);

        $recomendados = $this->livroModel
            ->select('livros.*, generos.nome AS genero')
            ->join('generos', 'generos.id_genero = livros.id_genero')
            ->where('livros.id_genero', $livro['id_genero'])
            ->where('livros.id_livro !=', $livro['id_livro'])
            ->where('livros.status', 'disponivel')
            ->orderBy('livros.data_cadastro', 'DESC')
            ->findAll(4);

        if (empty($recomendados)) {
            $recomendados = $this->livroModel
                ->select('livros.*, generos.nome AS genero')
                ->join('generos', 'generos.id_genero = livros.id_genero')
                ->where('livros.id_livro !=', $livro['id_livro'])
                ->where('livros.status', 'disponivel')
                ->orderBy('livros.data_cadastro', 'DESC')
                ->findAll(4);
        }

        $recomendados = array_map(fn ($item) => [
            'id_livro' => $item['id_livro'],
            'titulo'   => $item['titulo'],
            'autor'    => $item['autor'],
            'genero'   => $item['genero'],
            'capa'     => $item['imagem_capa'] ?? base_url('assets/img/capa-padrao.png'),
        ], $recomendados);

        if (!$livro || (int) $livro['id_usuario'] !== $idUsuario) {
            return redirect()->to(base_url('livro_meus'))
                ->with('erro', 'Você não tem permissão para excluir este livro.');
        }

        if ($livro['status'] === 'reservado') {
            return redirect()->to(base_url('livro/detalhes/' . $id))
                ->with('erro', 'Não é possível excluir um livro reservado — cancele a proposta primeiro.');
        }

        if (strtolower($this->request->getMethod()) === 'get') {
            return view('livro/detalhes', [
                'livro'             => $livro,
                'recomendados'      => $recomendados,
                'confirmarExclusao' => true,
            ]);
        }

        if (!empty($livro['imagem_capa'])) {
            $this->removerArquivoCapa($livro['imagem_capa']);
        }

        $this->livroModel->delete((int) $id);

        return redirect()->to(base_url('livro_meus'))
            ->with('sucesso', 'Livro removido.');
    }

    protected function dadosDoFormulario(): array
    {
        return [
            'id_genero'          => (int) $this->request->getPost('id_genero'),
            'titulo'             => trim((string) $this->request->getPost('titulo')),
            'autor'              => trim((string) $this->request->getPost('autor')),
            'editora'            => trim((string) $this->request->getPost('editora')) ?: null,
            'ano_publicacao'     => $this->request->getPost('ano_publicacao') ?: null,
            'descricao'          => trim((string) $this->request->getPost('descricao')) ?: null,
            'estado_conservacao' => $this->request->getPost('estado_conservacao'),
            'tipo_transacao'     => $this->request->getPost('tipo_transacao'),
            'preco'              => $this->request->getPost('preco') !== '' ? $this->request->getPost('preco') : null,
            'cep'                => trim((string) $this->request->getPost('cep')),
            'localizacao'        => trim((string) $this->request->getPost('localizacao')),
        ];
    }

    protected function processarUploadCapa()
    {
        $arquivo = $this->request->getFile('imagem_capa');

        if (!$arquivo || !$arquivo->isValid() || $arquivo->hasMoved()) {
            return false;
        }

        if (!in_array($arquivo->getClientMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return false;
        }

        $nomeNovo = $arquivo->getRandomName();
        $arquivo->move(FCPATH . $this->pastaUploads, $nomeNovo);

        return base_url($this->pastaUploads . '/' . $nomeNovo);
    }

    protected function removerArquivoCapa(string $urlCapa): void
    {
        if (strpos($urlCapa, base_url($this->pastaUploads)) !== 0) {
            return;
        }

        $caminho = FCPATH . str_replace(base_url(), '', $urlCapa);

        if (is_file($caminho)) {
            unlink($caminho);
        }
    }

    public function enviarInteresse($id = null)
    {
        $idUsuario = $this->getUsuarioLogado();

        if (!$idUsuario) {
            return redirect()->to(base_url('/'))
                ->with('erro', 'Faça login para demonstrar interesse.')
                ->with('abrirModalLogin', true);
        }

        $livro = $this->livroModel->buscarComGenero((int) $id);

        if (!$livro || empty($livro['dono_email'])) {
            return redirect()->to(base_url('livro/detalhes/' . $id))
                ->with('erro', 'Não foi possível enviar o interesse.');
        }

        if ((int) $livro['id_usuario'] === $idUsuario) {
            return redirect()->to(base_url('livro/detalhes/' . $id))
                ->with('erro', 'Você não pode demonstrar interesse no seu próprio livro.');
        }

        if (strtolower($this->request->getMethod()) === 'get') {
            return view('livro/formulario_interesse', [
                'livro' => $livro,
            ]);
        }

        $usuario = $this->usuarioModel->find($idUsuario);
        $mensagemPersonalizada = trim((string) $this->request->getPost('mensagem'));

        $corpo = view('emails/interesse_livro', [
            'nomeDono'        => $livro['dono_nome'] ?? '',
            'nomeInteressado' => $usuario['nome'] ?? 'um leitor',
            'tituloLivro'     => $livro['titulo'],
            'mensagem'        => $mensagemPersonalizada,
            'link'            => base_url('livro/detalhes/' . $livro['id_livro']),
        ], ['debug' => false]);

        $email = \Config\Services::email();
        $email->setTo($livro['dono_email']);
        $email->setSubject('Interesse no livro: ' . $livro['titulo']);
        $email->setMailType('html');
        $email->setMessage($corpo);

        if ($email->send()) {
            return redirect()->to(base_url('livro/detalhes/' . $id))
                ->with('sucesso', 'Seu interesse foi enviado ao dono do livro!');
        }

        return redirect()->to(base_url('livro/detalhes/' . $id))
            ->with('erro', 'Não foi possível enviar o interesse. Tente novamente.');
    }
}