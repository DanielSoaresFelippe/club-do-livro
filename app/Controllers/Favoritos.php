<?php

namespace App\Controllers;

use App\Models\FavoritosModel;

class Favoritos extends BaseController
{
    protected FavoritosModel $favoritosModel;

    public function __construct()
    {
        $this->favoritosModel = new FavoritosModel();
    }

    public function index()
    {
        $idUsuario = session()->get('usuario_id');

        if (!$idUsuario) {
            return redirect()->to(base_url('login'));
        }

        $favoritos = $this->favoritosModel->listarDoUsuario($idUsuario);

        return view('favoritos', ['favoritos' => $favoritos]);
    }

    public function alternar($idLivro = null)
    {
        $idUsuario = session()->get('usuario_id');

        if (!$idUsuario) {
            return $this->response->setStatusCode(401)->setJSON([
                'sucesso' => false,
                'erro'    => 'Você precisa estar logado.',
            ]);
        }

        $favoritado = $this->favoritosModel->alternar($idUsuario, (int) $idLivro);

        return $this->response->setJSON([
            'sucesso'    => true,
            'favoritado' => $favoritado,
        ]);
    }
}