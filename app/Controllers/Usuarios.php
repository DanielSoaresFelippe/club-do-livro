<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
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
            'redirect' => base_url('usuarios/perfil'),
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
            'redirect' => base_url('usuarios/perfil'),
        ]);
    }

    public function solicitarRecuperacao()
    {
        $email = trim((string) $this->request->getPost('email'));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors'  => ['email' => 'Informe um e-mail válido.'],
            ]);
        }

        $usuario = (new UsuarioModel())->where('email', $email)->where('ativo', 1)->first();
        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $db = db_connect();
            $db->table('usuariorecuperasenha')->where('usuario_id', $usuario['id_usuario'])->update(['statusRegistro' => 0]);
            $db->table('usuariorecuperasenha')->insert([
                'usuario_id' => $usuario['id_usuario'],
                'chave' => hash('sha256', $token),
                'statusRegistro' => 1,
            ]);

            $emailService = service('email');
            $emailService->setFrom((string) env('email.fromEmail'), (string) env('email.fromName', 'Clube do Livro'));
            $emailService->setTo($usuario['email']);
            $emailService->setSubject('Redefinição de senha - Clube do Livro');
            $emailService->setMessage("Olá, {$usuario['nome']}!\n\nAcesse o link abaixo para criar uma nova senha. Ele expira em 30 minutos e só pode ser usado uma vez:\n\n" . base_url('usuarios/redefinir?token=' . urlencode($token)) . "\n\nSe você não solicitou essa alteração, ignore este e-mail.");

            if (! $emailService->send()) {
                log_message('error', 'Falha ao enviar recuperação de senha: ' . $emailService->printDebugger(['headers']));
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'errors' => ['geral' => 'Não foi possível enviar o e-mail agora.'],
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Se o e-mail estiver cadastrado, você receberá um link para redefinir sua senha.',
        ]);
    }

    public function redefinirSenha()
    {
        $token = trim((string) $this->request->getGet('token'));
        return view('usuarios/redefinir_senha', [
            'token' => $token,
            'erro' => $token === '' ? 'Link de recuperação inválido.' : null,
        ]);
    }

    public function salvarNovaSenha()
    {
        $token = trim((string) $this->request->getPost('token'));
        $senha = (string) $this->request->getPost('senha');
        $confirmacao = (string) $this->request->getPost('confirmacao');

        if ($token === '' || strlen($senha) < 8 || ! preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).+$/', $senha)) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => ['senha' => 'A senha precisa ter no mínimo 8 caracteres, uma letra maiúscula, um número e um caractere especial.']]);
        }
        if ($senha !== $confirmacao) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => ['confirmacao' => 'As senhas não conferem.']]);
        }

        $registros = db_connect()->table('usuariorecuperasenha')
            ->where('statusRegistro', 1)
            ->where('created_at >=', date('Y-m-d H:i:s', time() - 1800))
            ->get()->getResultArray();
        $hashToken = hash('sha256', $token);
        $recuperacao = null;
        foreach ($registros as $registro) {
            if (hash_equals($registro['chave'], $hashToken)) {
                $recuperacao = $registro;
                break;
            }
        }

        if (! $recuperacao) {
            return $this->response->setStatusCode(422)->setJSON(['success' => false, 'errors' => ['token' => 'Este link expirou ou já foi utilizado.']]);
        }

        $db = db_connect();
        $db->transStart();
        (new UsuarioModel())->skipValidation(true)->update($recuperacao['usuario_id'], ['senha' => password_hash($senha, PASSWORD_DEFAULT)]);
        $db->table('usuariorecuperasenha')->where('id', $recuperacao['id'])->update(['statusRegistro' => 0]);
        $db->transComplete();
        if (! $db->transStatus()) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'errors' => ['geral' => 'Não foi possível redefinir a senha.']]);
        }

        return $this->response->setJSON(['success' => true, 'redirect' => base_url('/')]);
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
        if (!session()->get('usuario_logado')) {
            return redirect()->to(base_url('/'));
        }
 
        return view('usuarios/historico');
    }
 
    public function favoritos()
    {
        if (!session()->get('usuario_logado')) {
            return redirect()->to(base_url('/'));
        }
 
        return view('usuarios/favoritos');
    }
}