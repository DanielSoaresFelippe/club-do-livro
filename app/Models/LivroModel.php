<?php

namespace App\Models;

use CodeIgniter\Model;

class LivroModel extends Model
{
    protected $table            = 'livros';
    protected $primaryKey       = 'id_livro';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false; 

    protected $allowedFields = [
        'id_usuario',
        'id_genero',
        'titulo',
        'autor',
        'editora',
        'ano_publicacao',
        'descricao',
        'imagem_capa',
        'estado_conservacao',
        'tipo_transacao',
        'preco',
        'status',
    ];

    protected $validationRules = [
        'id_usuario'         => 'required|integer',
        'id_genero'          => 'required|integer|is_not_unique[generos.id_genero]',
        'titulo'             => 'required|min_length[2]|max_length[200]',
        'autor'              => 'required|min_length[2]|max_length[150]',
        'editora'            => 'permit_empty|max_length[150]',
        'ano_publicacao'     => 'permit_empty|integer',
        'descricao'          => 'permit_empty',
        'estado_conservacao' => 'required|in_list[novo,seminovo,usado,desgastado]',
        'tipo_transacao'     => 'required|in_list[venda,troca,ambos]',
        'preco'              => 'permit_empty|decimal',
        'status'             => 'permit_empty|in_list[disponivel,reservado,indisponivel]',
    ];

    protected $validationMessages = [
        'id_genero' => [
            'is_not_unique' => 'O gênero selecionado não existe.',
        ],
        'preco' => [
            'decimal' => 'Informe um preço válido, ex: 25.90',
        ],
    ];

    protected $skipValidation = false;


    public function validarRegrasNegocio(array $dados): array
    {
        $erros = [];

        if (in_array($dados['tipo_transacao'] ?? null, ['venda', 'ambos'], true)) {
            if (empty($dados['preco']) || (float) $dados['preco'] <= 0) {
                $erros['preco'] = 'Informe um preço para livros à venda.';
            }
        }

        return $erros;
    }

    public function buscarComGenero(int $idLivro): ?array
    {
        return $this->select('livros.*, generos.nome AS genero')
            ->join('generos', 'generos.id_genero = livros.id_genero')
            ->where('livros.id_livro', $idLivro)
            ->first();
    }

    public function listarDoUsuario(int $idUsuario): array
    {
        return $this->select('livros.*, generos.nome AS genero')
            ->join('generos', 'generos.id_genero = livros.id_genero')
            ->where('livros.id_usuario', $idUsuario)
            ->orderBy('livros.data_cadastro', 'DESC')
            ->findAll();
    }

    public function listarDisponiveis(?int $idGenero = null, ?string $tipoTransacao = null): array
    {
        $builder = $this->select('livros.*, generos.nome AS genero')
            ->join('generos', 'generos.id_genero = livros.id_genero')
            ->where('livros.status', 'disponivel');

        if ($idGenero !== null) {
            $builder->where('livros.id_genero', $idGenero);
        }

        if ($tipoTransacao !== null && in_array($tipoTransacao, ['venda', 'troca', 'ambos'], true)) {
            if ($tipoTransacao === 'ambos') {
                $builder->where('livros.tipo_transacao', 'ambos');
            } else {
                $builder->groupStart()
                    ->where('livros.tipo_transacao', $tipoTransacao)
                    ->orWhere('livros.tipo_transacao', 'ambos')
                    ->groupEnd();
            }
        }

        return $builder->orderBy('livros.data_cadastro', 'DESC')->findAll();
    }

    public function pertenceAoUsuario(int $idLivro, int $idUsuario): bool
    {
        return (bool) $this->where('id_livro', $idLivro)
            ->where('id_usuario', $idUsuario)
            ->first();
    }
}