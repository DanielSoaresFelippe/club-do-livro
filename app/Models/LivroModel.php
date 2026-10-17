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
        'cep',
        'bairro',
        'cidade',
        'estado',
        'localizacao',
    ];

    protected $beforeInsert = ['montarLocalizacao'];
    protected $beforeUpdate = ['montarLocalizacao'];

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
        'cep'                => 'required|regex_match[/^\d{5}-?\d{3}$/]',
        'bairro'             => 'permit_empty|max_length[100]',
        'cidade'             => 'required|max_length[100]',
        'estado'             => 'required|exact_length[2]|alpha',
        'localizacao'        => 'permit_empty|max_length[150]',
    ];

    protected $validationMessages = [
        'id_genero' => [
            'is_not_unique' => 'O gênero selecionado não existe.',
        ],
        'preco' => [
            'decimal' => 'Informe um preço válido, ex: 25.90',
        ],
        'cep' => [
            'regex_match' => 'Informe um CEP válido, ex: 36880-000',
        ],
        'cidade' => [
            'required' => 'Informe a cidade onde o livro será vendido/trocado.',
        ],
        'estado' => [
            'required'     => 'Informe a UF onde o livro será vendido/trocado.',
            'exact_length' => 'Informe a UF com 2 letras, ex: MG',
            'alpha'        => 'Informe a UF com 2 letras, ex: MG',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Mantém `localizacao` ("Cidade - UF") em sincronia com cidade/estado,
     * já que o filtro do catálogo continua usando essa coluna.
     */
    protected function montarLocalizacao(array $data): array
    {
        if (!empty($data['data']['estado'])) {
            $data['data']['estado'] = strtoupper($data['data']['estado']);
        }

        if (!empty($data['data']['cidade']) && !empty($data['data']['estado'])) {
            $data['data']['localizacao'] = $data['data']['cidade'] . ' - ' . $data['data']['estado'];
        }

        return $data;
    }


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

    public function buscarComGenero(int $id)
    {
        return $this->select('
                livros.*,
                generos.nome AS genero,
                usuarios.nome     AS dono_nome,
                usuarios.email    AS dono_email,
                usuarios.telefone AS dono_telefone
            ')
            ->join('generos', 'generos.id_genero = livros.id_genero')
            ->join('usuarios', 'usuarios.id_usuario = livros.id_usuario')
            ->where('livros.id_livro', $id)
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

    /**
     * Palavras que o FULLTEXT do InnoDB ignora: menores que innodb_ft_min_token_size (3)
     * e a lista padrão de stopwords (INFORMATION_SCHEMA.INNODB_FT_DEFAULT_STOPWORD).
     */
    protected const STOPWORDS_FULLTEXT = [
        'about', 'are', 'com', 'for', 'from', 'how', 'that', 'the', 'this',
        'was', 'what', 'when', 'where', 'who', 'will', 'with', 'und', 'www',
    ];

    /**
     * Catálogo com filtros e busca. Devolve o próprio Model (para encadear paginate()).
     *
     * A busca usa o índice FULLTEXT de título/autor (MATCH ... AGAINST em modo booleano,
     * com prefixo: "hob" encontra "Hobbit") e também procura no nome do gênero.
     * Termos que o FULLTEXT ignora (curtos ou stopwords) caem num LIKE.
     */
    public function buscarCatalogo(array $filtros): self
    {
        $this->select('livros.*, generos.nome AS genero')
            ->join('generos', 'generos.id_genero = livros.id_genero');

        if (!empty($filtros['tipo_transacao']) && in_array($filtros['tipo_transacao'], ['venda', 'troca', 'ambos'], true)) {
            $this->where('livros.tipo_transacao', $filtros['tipo_transacao']);
        }

        if (!empty($filtros['genero'])) {
            $this->where('livros.id_genero', (int) $filtros['genero']);
        }

        if (!empty($filtros['localizacao'])) {
            $this->where('livros.localizacao', $filtros['localizacao']);
        }

        $busca = trim((string) ($filtros['busca'] ?? ''));

        if ($busca !== '') {
            $consultaFullText = $this->montarConsultaFullText($busca);
            $match = 'MATCH(livros.titulo, livros.autor) AGAINST (' . $this->db->escape($consultaFullText) . ' IN BOOLEAN MODE)';

            // Gêneros cujo nome bate com a busca (tabela pequena: LIKE é barato aqui)
            $idsGenero = array_column(
                $this->db->table('generos')->select('id_genero')->like('nome', $busca)->get()->getResultArray(),
                'id_genero'
            );

            if ($consultaFullText === '') {
                // Só termos curtos / stopwords: o FULLTEXT não ajuda, fica o LIKE
                $this->groupStart()
                    ->like('livros.titulo', $busca)
                    ->orLike('livros.autor', $busca);
                if ($idsGenero !== []) {
                    $this->orWhereIn('livros.id_genero', $idsGenero);
                }
                $this->groupEnd();
            } elseif ($idsGenero === []) {
                // Caso comum: só o MATCH, que usa o índice FULLTEXT diretamente
                $this->where($match, null, false);
            } else {
                // Um OR entre MATCH e outra coluna faz o MySQL abandonar o índice FULLTEXT.
                // Por isso os ids do texto vêm de uma consulta própria (indexada) e o OR
                // fica entre duas colunas indexadas (PK e id_genero).
                $idsTexto = array_column(
                    $this->db->table('livros')->select('id_livro')->where($match, null, false)->get()->getResultArray(),
                    'id_livro'
                );

                $this->groupStart()
                    ->whereIn('livros.id_genero', $idsGenero);
                if ($idsTexto !== []) {
                    $this->orWhereIn('livros.id_livro', $idsTexto);
                }
                $this->groupEnd();
            }

            if ($consultaFullText !== '') {
                $this->orderBy($match, 'DESC', false);
            }
        }

        return $this->orderBy('livros.id_livro', 'DESC');
    }

    /**
     * "senhor dos anéis" -> "+senhor* +anéis*" (todas as palavras, aceitando prefixo).
     * Remove operadores do modo booleano digitados pelo usuário.
     */
    protected function montarConsultaFullText(string $busca): string
    {
        $limpa = preg_replace('/[+\-<>()~*"@]+/u', ' ', mb_strtolower($busca));
        $palavras = preg_split('/\s+/u', trim($limpa), -1, PREG_SPLIT_NO_EMPTY);

        $palavras = array_filter(
            $palavras,
            fn ($p) => mb_strlen($p) >= 3 && !in_array($p, self::STOPWORDS_FULLTEXT, true)
        );

        return implode(' ', array_map(fn ($p) => '+' . $p . '*', $palavras));
    }

    public function pertenceAoUsuario(int $idLivro, int $idUsuario): bool
    {
        return (bool) $this->where('id_livro', $idLivro)
            ->where('id_usuario', $idUsuario)
            ->first();
    }

    /**
     * Livros do usuário que ele pode oferecer numa troca.
     */
    public function listarDisponiveisParaTroca(int $idUsuario, array $exceto = []): array
    {
        $builder = $this->select('livros.id_livro, livros.titulo, livros.autor, livros.imagem_capa, generos.nome AS genero')
            ->join('generos', 'generos.id_genero = livros.id_genero')
            ->where('livros.id_usuario', $idUsuario)
            ->where('livros.status', 'disponivel');

        if ($exceto !== []) {
            $builder->whereNotIn('livros.id_livro', $exceto);
        }

        return $builder->orderBy('livros.titulo', 'ASC')->findAll();
    }
}
