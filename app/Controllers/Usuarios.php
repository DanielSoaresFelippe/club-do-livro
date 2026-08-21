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

        return $this->response->setJSON([
            'success' => true,
            'usuario' => [
                'id'    => $id,
                'nome'  => $this->request->getPost('nome'),
                'tipo'  => $this->request->getPost('tipo'),
                'foto'  => $nomeFoto ? base_url('uploads/perfil/' . $nomeFoto) : null,
            ],
        ]);
    }
}