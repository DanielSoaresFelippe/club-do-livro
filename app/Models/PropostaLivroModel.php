<?php

namespace App\Models;

use CodeIgniter\Model;

class PropostaLivroModel extends Model
{
    protected $table            = 'proposta_livros';
    protected $primaryKey       = 'id_proposta_livro';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'id_proposta',
        'id_livro',
    ];

    protected $validationRules = [
        'id_proposta' => 'required|integer',
        'id_livro'    => 'required|integer|is_not_unique[livros.id_livro]',
    ];

    public function listarDaProposta(int $idProposta): array
    {
        return $this->select('livros.id_livro, livros.titulo, livros.autor, livros.imagem_capa, livros.status')
            ->join('livros', 'livros.id_livro = proposta_livros.id_livro')
            ->where('proposta_livros.id_proposta', $idProposta)
            ->orderBy('livros.titulo', 'ASC')
            ->findAll();
    }
}
