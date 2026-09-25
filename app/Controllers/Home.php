<?php

namespace App\Controllers;

use App\Models\LivroModel;

class Home extends BaseController
{
    public function index(): string
    {
        $livroModel = new LivroModel();

        $livros = $livroModel->orderBy('id_livro', 'DESC')->findAll(8);

        $usuario = session()->get('usuario_id');

        return view('home', [
            'livros' => $livros,
            'usuario' => $usuario,
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url());
    }
}