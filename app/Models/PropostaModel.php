<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Lances / propostas feitas em um livro.
 *
 * tipo = 'dinheiro' -> valor_oferecido
 * tipo = 'troca'    -> livros oferecidos em `proposta_livros`
 */
class PropostaModel extends Model
{
    protected $table            = 'propostas';
    protected $primaryKey       = 'id_proposta';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    /** Dias que o dono tem para responder antes de a proposta expirar. */
    public const DIAS_PRAZO = 7;

    protected $allowedFields = [
        'id_proposta_pai',
        'id_livro',
        'id_usuario_interessado',
        'id_usuario_autor',
        'tipo',
        'valor_oferecido',
        'mensagem',
        'status',
        'prazo_limite',
        'data_resposta',
    ];

    protected $validationRules = [
        'id_livro'               => 'required|integer|is_not_unique[livros.id_livro]',
        'id_usuario_interessado' => 'required|integer|is_not_unique[usuarios.id_usuario]',
        'id_usuario_autor'       => 'permit_empty|integer|is_not_unique[usuarios.id_usuario]',
        'id_proposta_pai'        => 'permit_empty|integer|is_not_unique[propostas.id_proposta]',
        'tipo'                   => 'required|in_list[dinheiro,troca]',
        'valor_oferecido'        => 'permit_empty|decimal|greater_than[0]',
        'mensagem'               => 'permit_empty|max_length[1000]',
        'status'                 => 'permit_empty|in_list[pendente,aceita,recusada,contraproposta,cancelada,expirada,concluida]',
    ];

    protected $validationMessages = [
        'tipo' => [
            'in_list' => 'Escolha se a oferta é em dinheiro ou troca.',
        ],
        'valor_oferecido' => [
            'decimal'      => 'Informe um valor válido, ex: 25.90',
            'greater_than' => 'O valor oferecido deve ser maior que zero.',
        ],
    ];

    protected $skipValidation = false;

    protected $beforeInsert = ['preencherPadroes'];

    protected function preencherPadroes(array $data): array
    {
        $data['data']['status']           ??= 'pendente';
        $data['data']['id_usuario_autor'] ??= $data['data']['id_usuario_interessado'] ?? null;
        $data['data']['prazo_limite']     ??= date('Y-m-d H:i:s', strtotime('+' . self::DIAS_PRAZO . ' days'));

        return $data;
    }

    /**
     * Regras de negócio do lance (além da validação de formato).
     *
     * @param int[] $idsLivrosOferecidos
     *
     * @return array<string, string> erros por campo; vazio = lance válido
     */
    public function validarLance(array $dados, array $idsLivrosOferecidos = []): array
    {
        $erros = [];
        $idInteressado = (int) ($dados['id_usuario_interessado'] ?? 0);
        $livro = (new LivroModel())->find((int) ($dados['id_livro'] ?? 0));

        if (!$livro) {
            return ['id_livro' => 'Livro não encontrado.'];
        }

        if ((int) $livro['id_usuario'] === $idInteressado) {
            return ['id_livro' => 'Você não pode fazer um lance no seu próprio livro.'];
        }

        if ($livro['status'] !== 'disponivel') {
            return ['id_livro' => 'Este livro não está mais disponível para negociação.'];
        }

        if ($this->existePendente((int) $livro['id_livro'], $idInteressado)) {
            return ['id_livro' => 'Você já tem uma proposta pendente para este livro.'];
        }

        return $this->validarOferta($livro, $dados, $idsLivrosOferecidos, $idInteressado);
    }

    /**
     * Valida o conteúdo de uma oferta (lance ou contraproposta) contra o livro negociado.
     * Na troca, os livros oferecidos são sempre do interessado.
     *
     * @param int[] $idsLivrosOferecidos
     */
    protected function validarOferta(array $livro, array $dados, array $idsLivrosOferecidos, int $idInteressado, bool $autorEhInteressado = true): array
    {
        $erros = [];
        $tipo = $dados['tipo'] ?? null;
        $aceitaVenda = in_array($livro['tipo_transacao'], ['venda', 'ambos'], true);
        $aceitaTroca = in_array($livro['tipo_transacao'], ['troca', 'ambos'], true);

        if (!in_array($tipo, ['dinheiro', 'troca'], true)) {
            return ['tipo' => 'Escolha se a oferta é em dinheiro ou troca.'];
        }

        if ($tipo === 'dinheiro') {
            if (!$aceitaVenda) {
                $erros['tipo'] = 'Este livro está disponível apenas para troca.';
            } elseif (empty($dados['valor_oferecido']) || !is_numeric($dados['valor_oferecido']) || (float) $dados['valor_oferecido'] <= 0) {
                $erros['valor_oferecido'] = 'Informe um valor maior que zero.';
            }
        }

        if ($tipo === 'troca') {
            $idsLivrosOferecidos = array_values(array_unique(array_map('intval', $idsLivrosOferecidos)));
            $deQuem = $autorEhInteressado ? 'seus' : 'do interessado';

            if (!$aceitaTroca) {
                $erros['tipo'] = 'Este livro está disponível apenas para venda.';
            } elseif ($idsLivrosOferecidos === []) {
                $erros['livros_oferecidos'] = "Selecione ao menos um livro {$deQuem} para a troca.";
            } else {
                $validos = (new LivroModel())
                    ->whereIn('id_livro', $idsLivrosOferecidos)
                    ->where('id_usuario', $idInteressado)
                    ->where('status', 'disponivel')
                    ->countAllResults();

                if ($validos !== count($idsLivrosOferecidos)) {
                    $erros['livros_oferecidos'] = "Só é possível escolher livros {$deQuem} que estejam disponíveis.";
                }
            }
        }

        return $erros;
    }

    /**
     * Grava o lance e os livros oferecidos numa única transação.
     * Chame validarLance() antes.
     *
     * @param int[] $idsLivrosOferecidos
     *
     * @return int|false id da proposta criada
     */
    public function registrarLance(array $dados, array $idsLivrosOferecidos = [])
    {
        if (($dados['tipo'] ?? null) === 'troca') {
            $dados['valor_oferecido'] = null;
        } else {
            $idsLivrosOferecidos = [];
        }

        $this->db->transBegin();

        $idProposta = $this->insert($dados, true);
        $ok = (bool) $idProposta;

        if ($ok) {
            $propostaLivros = new PropostaLivroModel();

            foreach (array_unique(array_map('intval', $idsLivrosOferecidos)) as $idLivro) {
                $ok = (bool) $propostaLivros->insert([
                    'id_proposta' => $idProposta,
                    'id_livro'    => $idLivro,
                ]);

                if (!$ok) {
                    break;
                }
            }
        }

        if (!$ok || $this->db->transStatus() === false) {
            $this->db->transRollback();

            return false;
        }

        $this->db->transCommit();

        return (int) $idProposta;
    }

    public function existePendente(int $idLivro, int $idInteressado): bool
    {
        return $this->where('id_livro', $idLivro)
            ->where('id_usuario_interessado', $idInteressado)
            ->where('status', 'pendente')
            ->countAllResults() > 0;
    }

    public function buscarComDetalhes(int $idProposta): ?array
    {
        $proposta = $this->select('
                propostas.*,
                livros.titulo          AS livro_titulo,
                livros.autor           AS livro_autor,
                livros.imagem_capa     AS livro_capa,
                livros.tipo_transacao  AS livro_tipo_transacao,
                livros.preco           AS livro_preco,
                livros.status          AS livro_status,
                livros.id_usuario      AS id_dono,
                dono.nome              AS dono_nome,
                dono.email             AS dono_email,
                dono.telefone          AS dono_telefone,
                interessado.nome       AS interessado_nome,
                interessado.email      AS interessado_email,
                interessado.telefone   AS interessado_telefone
            ')
            ->join('livros', 'livros.id_livro = propostas.id_livro')
            ->join('usuarios dono', 'dono.id_usuario = livros.id_usuario')
            ->join('usuarios interessado', 'interessado.id_usuario = propostas.id_usuario_interessado')
            ->where('propostas.id_proposta', $idProposta)
            ->first();

        if (!$proposta) {
            return null;
        }

        $proposta['livros_oferecidos'] = (new PropostaLivroModel())->listarDaProposta($idProposta);

        return $proposta;
    }

    // ---------------------------------------------------------------------
    // Negociação: aceitar / recusar / contraproposta / cancelar
    // ---------------------------------------------------------------------

    /**
     * Quem precisa responder a oferta: sempre a parte que NÃO fez a última oferta.
     */
    public function idResponsavel(array $proposta): int
    {
        return (int) $proposta['id_usuario_autor'] === (int) $proposta['id_usuario_interessado']
            ? (int) $proposta['id_dono']
            : (int) $proposta['id_usuario_interessado'];
    }

    public function podeResponder(array $proposta, int $idUsuario): bool
    {
        return $proposta['status'] === 'pendente' && $this->idResponsavel($proposta) === $idUsuario;
    }

    public function participa(array $proposta, int $idUsuario): bool
    {
        return in_array($idUsuario, [(int) $proposta['id_dono'], (int) $proposta['id_usuario_interessado']], true);
    }

    /**
     * Marca como expiradas as propostas pendentes cujo prazo já passou.
     */
    public function expirarVencidas(): void
    {
        $this->builder()
            ->where('status', 'pendente')
            ->where('prazo_limite <', date('Y-m-d H:i:s'))
            ->update(['status' => 'expirada']);
    }

    /**
     * Ofertas da negociação, da primeira até a informada (para montar a linha do tempo).
     */
    public function cadeia(int $idProposta): array
    {
        $cadeia = [];

        while ($idProposta && ($proposta = $this->buscarComDetalhes($idProposta))) {
            array_unshift($cadeia, $proposta);
            $idProposta = (int) $proposta['id_proposta_pai'];
        }

        return $cadeia;
    }

    /**
     * Id da oferta mais recente da negociação a que esta proposta pertence.
     */
    public function idUltimaDaCadeia(int $idProposta): int
    {
        while ($filha = $this->select('id_proposta')->where('id_proposta_pai', $idProposta)->first()) {
            $idProposta = (int) $filha['id_proposta'];
        }

        return $idProposta;
    }

    /**
     * Negociações do usuário (só a oferta mais recente de cada uma).
     *
     * @param string $aba 'recebidas' (nos meus livros) ou 'enviadas' (feitas por mim)
     */
    public function listarNegociacoes(int $idUsuario, string $aba): array
    {
        $builder = $this->select('
                propostas.*,
                livros.titulo         AS livro_titulo,
                livros.imagem_capa    AS livro_capa,
                livros.status         AS livro_status,
                livros.id_usuario     AS id_dono,
                dono.nome             AS dono_nome,
                interessado.nome      AS interessado_nome
            ')
            ->join('livros', 'livros.id_livro = propostas.id_livro')
            ->join('usuarios dono', 'dono.id_usuario = livros.id_usuario')
            ->join('usuarios interessado', 'interessado.id_usuario = propostas.id_usuario_interessado')
            ->where('propostas.status !=', 'contraproposta');

        if ($aba === 'enviadas') {
            $builder->where('propostas.id_usuario_interessado', $idUsuario);
        } else {
            $builder->where('livros.id_usuario', $idUsuario);
        }

        $negociacoes = $builder
            ->orderBy("propostas.status = 'pendente'", 'DESC', false)
            ->orderBy('propostas.data_criacao', 'DESC')
            ->findAll();

        $propostaLivros = new PropostaLivroModel();

        foreach ($negociacoes as &$negociacao) {
            $negociacao['aguardando_voce']   = $this->podeResponder($negociacao, $idUsuario);
            $negociacao['livros_oferecidos'] = $negociacao['tipo'] === 'troca'
                ? $propostaLivros->listarDaProposta((int) $negociacao['id_proposta'])
                : [];
        }

        return $negociacoes;
    }

    /**
     * Quantas ofertas estão esperando resposta do usuário (badge do menu).
     */
    public function contarAguardandoResposta(int $idUsuario): int
    {
        return $this->join('livros', 'livros.id_livro = propostas.id_livro')
            ->where('propostas.status', 'pendente')
            ->where('propostas.id_usuario_autor !=', $idUsuario)
            ->groupStart()
                ->where('livros.id_usuario', $idUsuario)
                ->orWhere('propostas.id_usuario_interessado', $idUsuario)
            ->groupEnd()
            ->countAllResults();
    }

    /**
     * Aceita a oferta: o livro negociado (e os livros da troca) ficam reservados
     * e as demais propostas pendentes para esse livro são recusadas.
     *
     * @return string|null mensagem de erro, ou null se deu certo
     */
    public function aceitar(int $idProposta, int $idUsuario): ?string
    {
        $proposta = $this->buscarComDetalhes($idProposta);

        if ($erro = $this->erroAoResponder($proposta, $idUsuario)) {
            return $erro;
        }

        $idsTroca = array_map('intval', array_column($proposta['livros_oferecidos'], 'id_livro'));

        if ($proposta['tipo'] === 'troca') {
            $indisponiveis = array_filter($proposta['livros_oferecidos'], fn ($l) => $l['status'] !== 'disponivel');

            if ($idsTroca === [] || $indisponiveis !== []) {
                return 'Algum livro oferecido nesta troca não está mais disponível.';
            }
        }

        $agora = date('Y-m-d H:i:s');
        $livros = new LivroModel();

        $this->db->transBegin();

        $this->builder()->where('id_proposta', $idProposta)
            ->update(['status' => 'aceita', 'data_resposta' => $agora]);

        $livros->builder()->whereIn('id_livro', array_merge([(int) $proposta['id_livro']], $idsTroca))
            ->update(['status' => 'reservado']);

        $this->builder()
            ->where('id_livro', (int) $proposta['id_livro'])
            ->where('status', 'pendente')
            ->where('id_proposta !=', $idProposta)
            ->update(['status' => 'recusada', 'data_resposta' => $agora]);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();

            return 'Não foi possível aceitar a proposta. Tente novamente.';
        }

        $this->db->transCommit();

        return null;
    }

    /**
     * @return string|null mensagem de erro, ou null se deu certo
     */
    public function recusar(int $idProposta, int $idUsuario): ?string
    {
        $proposta = $this->buscarComDetalhes($idProposta);

        if ($erro = $this->erroAoResponder($proposta, $idUsuario, false)) {
            return $erro;
        }

        $this->builder()->where('id_proposta', $idProposta)
            ->update(['status' => 'recusada', 'data_resposta' => date('Y-m-d H:i:s')]);

        return null;
    }

    /**
     * Quem fez a oferta pode desistir enquanto ela estiver pendente.
     *
     * @return string|null mensagem de erro, ou null se deu certo
     */
    public function cancelar(int $idProposta, int $idUsuario): ?string
    {
        $proposta = $this->find($idProposta);

        if (!$proposta || (int) $proposta['id_usuario_autor'] !== $idUsuario) {
            return 'Proposta não encontrada.';
        }

        if ($proposta['status'] !== 'pendente') {
            return 'Esta proposta não está mais pendente.';
        }

        $this->builder()->where('id_proposta', $idProposta)
            ->update(['status' => 'cancelada', 'data_resposta' => date('Y-m-d H:i:s')]);

        return null;
    }

    /**
     * Responde uma oferta com outra. A oferta atual vira 'contraproposta' e a nova
     * fica pendente, aguardando a outra parte.
     *
     * @param int[] $idsLivrosOferecidos livros do interessado
     *
     * @return array{0: array<string,string>, 1: int|null} [erros, id da nova oferta]
     */
    public function contrapropor(int $idProposta, int $idUsuario, array $dados, array $idsLivrosOferecidos = []): array
    {
        $proposta = $this->buscarComDetalhes($idProposta);

        if ($erro = $this->erroAoResponder($proposta, $idUsuario)) {
            return [['geral' => $erro], null];
        }

        $idInteressado = (int) $proposta['id_usuario_interessado'];
        $livro = (new LivroModel())->find((int) $proposta['id_livro']);

        $erros = $this->validarOferta($livro, $dados, $idsLivrosOferecidos, $idInteressado, $idUsuario === $idInteressado);

        if (($dados['tipo'] ?? null) === 'troca') {
            $atuais = array_map('intval', array_column($proposta['livros_oferecidos'], 'id_livro'));
            $novos  = array_map('intval', $idsLivrosOferecidos);
            sort($atuais);
            sort($novos);
            $mesmaOferta = $proposta['tipo'] === 'troca' && $atuais === $novos;
        } else {
            $mesmaOferta = $proposta['tipo'] === 'dinheiro'
                && round((float) $proposta['valor_oferecido'], 2) === round((float) ($dados['valor_oferecido'] ?? 0), 2);
        }

        if ($erros === [] && $mesmaOferta) {
            $erros['geral'] = 'A contraproposta é igual à oferta atual — se concorda, aceite a proposta.';
        }

        if ($erros !== []) {
            return [$erros, null];
        }

        $this->db->transBegin();

        $this->builder()->where('id_proposta', $idProposta)
            ->update(['status' => 'contraproposta', 'data_resposta' => date('Y-m-d H:i:s')]);

        $idNova = $this->registrarLance([
            'id_proposta_pai'        => $idProposta,
            'id_livro'               => (int) $proposta['id_livro'],
            'id_usuario_interessado' => $idInteressado,
            'id_usuario_autor'       => $idUsuario,
            'tipo'                   => $dados['tipo'],
            'valor_oferecido'        => $dados['valor_oferecido'] ?? null,
            'mensagem'               => $dados['mensagem'] ?? null,
        ], $idsLivrosOferecidos);

        if (!$idNova || $this->db->transStatus() === false) {
            $this->db->transRollback();

            return [['geral' => 'Não foi possível enviar a contraproposta. Tente novamente.'], null];
        }

        $this->db->transCommit();

        return [[], $idNova];
    }

    protected function erroAoResponder(?array $proposta, int $idUsuario, bool $exigeLivroDisponivel = true): ?string
    {
        if (!$proposta || !$this->participa($proposta, $idUsuario)) {
            return 'Proposta não encontrada.';
        }

        if ($proposta['status'] !== 'pendente') {
            return 'Esta proposta não está mais pendente.';
        }

        if (strtotime($proposta['prazo_limite']) < time()) {
            return 'O prazo desta proposta já expirou.';
        }

        if ($this->idResponsavel($proposta) !== $idUsuario) {
            return 'Agora é a vez da outra pessoa responder.';
        }

        if ($exigeLivroDisponivel && $proposta['livro_status'] !== 'disponivel') {
            return 'O livro não está mais disponível para negociação.';
        }

        return null;
    }
}
