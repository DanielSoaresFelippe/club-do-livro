<?php

namespace App\Models;

use CodeIgniter\Model;

class FavoritosModel extends Model
{
    protected $table         = 'favoritos';
    protected $primaryKey    = 'id_favorito';
    protected $allowedFields = ['id_usuario', 'id_livro'];
    protected $useTimestamps = false;

    public function listarDoUsuario(int $idUsuario): array
    {
        $favoritos = $this->select('livros.id_livro, livros.titulo, livros.autor, livros.imagem_capa')
            ->join('livros', 'livros.id_livro = favoritos.id_livro')
            ->where('favoritos.id_usuario', $idUsuario)
            ->orderBy('favoritos.data_favoritado', 'DESC')
            ->findAll();

        return array_map(function ($item) {
            $item['capa'] = $item['imagem_capa'] ?? base_url('assets/img/capa-padrao.png');
            unset($item['imagem_capa']);
            return $item;
        }, $favoritos); 
    }

    public function estaFavoritado(int $idUsuario, int $idLivro): bool
    {
        return (bool) $this->where('id_usuario', $idUsuario)
            ->where('id_livro', $idLivro)
            ->first();
    }

    public function alternar(int $idUsuario, int $idLivro): bool
    {
        $jaFavoritado = $this->estaFavoritado($idUsuario, $idLivro);

        if ($jaFavoritado) {
            $this->db->query('CALL sp_remover_favorito(?, ?)', [$idUsuario, $idLivro]);
            return false;
        }

        $this->db->query('CALL sp_adicionar_favorito(?, ?)', [$idUsuario, $idLivro]);
        return true;
    }
}