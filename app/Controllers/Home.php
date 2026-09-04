<?php

namespace App\Controllers;

use App\Models\LivroModel;

class Home extends BaseController
{
    public function index(): string
    {
        $livroModel = new LivroModel();

        $livros = $livroModel->orderBy('id_livro', 'DESC')->findAll(8);

        return view('home', [
            'livros' => $livros,
        ]);
    }
}