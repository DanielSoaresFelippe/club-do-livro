<?php

namespace App\Controllers;

use App\Models\LivroModel;
use App\Models\PropostaModel;

class Propostas extends BaseController
{
    protected LivroModel $livroModel;
    protected PropostaModel $propostaModel;

    public function __construct()
    {
        helper('proposta');
        $this->livroModel    = new LivroModel();
        $this->propostaModel = new PropostaModel();
    }

    /**
     * Link "Fazer proposta" (catálogo / visitante): pede login ou abre o modal nos detalhes.
     */
    public function abrir($idLivro = null)
    {
        if (!session()->get('usuario_id')) {
            return redirect()->to(base_url('/'))
                ->with('erro', 'Faça login para fazer uma proposta.')
                ->with('abrirModalLogin', true);
        }

        return redirect()->to(base_url('livro/detalhes/' . (int) $idLivro))
            ->with('abrirModalProposta', true);
    }

    /**
     * Registra um lance (dinheiro ou troca) no livro.
     * O dono do livro nunca pode dar lance — regra checada aqui e no PropostaModel.
     */
    public function criar($idLivro = null)
    {
        $idUsuario = (int) session()->get('usuario_id');
        $urlLivro  = base_url('livro/detalhes/' . (int) $idLivro);

        if (!$idUsuario) {
            return redirect()->to(base_url('/'))
                ->with('erro', 'Faça login para fazer uma proposta.')
                ->with('abrirModalLogin', true);
        }

        $livro = $this->livroModel->find((int) $idLivro);

        if (!$livro) {
            return redirect()->to(base_url('livro'))
                ->with('erro', 'Livro não encontrado.');
        }

        if ((int) $livro['id_usuario'] === $idUsuario) {
            return redirect()->to($urlLivro)
                ->with('erro', 'Você não pode fazer uma proposta no seu próprio livro.');
        }

        $tipo = $this->request->getPost('tipo');
        $idsLivrosOferecidos = $tipo === 'troca'
            ? array_map('intval', (array) $this->request->getPost('livros_oferecidos'))
            : [];

        $dados = [
            'id_livro'               => (int) $livro['id_livro'],
            'id_usuario_interessado' => $idUsuario,
            'tipo'                   => $tipo,
            'valor_oferecido'        => $tipo === 'dinheiro' ? $this->request->getPost('valor_oferecido') : null,
            'mensagem'               => trim((string) $this->request->getPost('mensagem')) ?: null,
        ];

        $erros = $this->propostaModel->validate($dados) ? [] : $this->propostaModel->errors();
        $erros = array_merge($erros, $this->propostaModel->validarLance($dados, $idsLivrosOferecidos));

        // Erros sobre o próprio livro (indisponível, proposta já pendente) vão para o aviso da página,
        // já que nesses casos o modal de proposta nem é exibido.
        if (isset($erros['id_livro'])) {
            return redirect()->to($urlLivro)->with('erro', $erros['id_livro']);
        }

        if (!empty($erros)) {
            return redirect()->to($urlLivro)->withInput()
                ->with('errosProposta', $erros)
                ->with('abrirModalProposta', true);
        }

        $idProposta = $this->propostaModel->registrarLance($dados, $idsLivrosOferecidos);

        if (!$idProposta) {
            return redirect()->to($urlLivro)->withInput()
                ->with('erro', 'Não foi possível registrar sua proposta. Tente novamente.');
        }

        service('notificacaoNegociacao')->novaProposta($idProposta);

        return redirect()->to($urlLivro)
            ->with('sucesso', 'Proposta enviada! O dono do livro tem ' . PropostaModel::DIAS_PRAZO . ' dias para responder.');
    }

    // ---------------------------------------------------------------------
    // Painel "Minhas propostas"
    // ---------------------------------------------------------------------

    public function index()
    {
        $idUsuario = (int) session()->get('usuario_id');
        $aba = $this->request->getGet('aba') === 'enviadas' ? 'enviadas' : 'recebidas';

        $this->propostaModel->expirarVencidas();

        $negociacoes = $this->propostaModel->listarNegociacoes($idUsuario, $aba);

        // Recebidas: agrupadas por livro ("meus livros que receberam propostas")
        $porLivro = [];
        foreach ($negociacoes as $negociacao) {
            $idLivro = (int) $negociacao['id_livro'];
            $porLivro[$idLivro] ??= [
                'id_livro'    => $idLivro,
                'titulo'      => $negociacao['livro_titulo'],
                'capa'        => $negociacao['livro_capa'] ?: base_url('assets/img/capa-padrao.png'),
                'status'      => $negociacao['livro_status'],
                'aguardando'  => 0,
                'negociacoes' => [],
            ];
            $porLivro[$idLivro]['negociacoes'][] = $negociacao;
            $porLivro[$idLivro]['aguardando'] += $negociacao['aguardando_voce'] ? 1 : 0;
        }

        return view('propostas/index', [
            'aba'         => $aba,
            'negociacoes' => $negociacoes,
            'porLivro'    => array_values($porLivro),
            'contagem'    => [
                'recebidas' => $aba === 'recebidas'
                    ? array_sum(array_column($porLivro, 'aguardando'))
                    : $this->contarAguardando($idUsuario, 'recebidas'),
                'enviadas'  => $aba === 'enviadas'
                    ? count(array_filter($negociacoes, fn ($n) => $n['aguardando_voce']))
                    : $this->contarAguardando($idUsuario, 'enviadas'),
            ],
        ]);
    }

    public function ver($idProposta = null)
    {
        $idUsuario = (int) session()->get('usuario_id');

        $this->propostaModel->expirarVencidas();

        $proposta = $this->propostaModel->buscarComDetalhes((int) $idProposta);

        if (!$proposta || !$this->propostaModel->participa($proposta, $idUsuario)) {
            return redirect()->to(base_url('propostas'))->with('erro', 'Proposta não encontrada.');
        }

        // Link antigo de uma oferta já respondida: mostra sempre a mais recente da negociação
        $idUltima = $this->propostaModel->idUltimaDaCadeia((int) $idProposta);
        if ($idUltima !== (int) $idProposta) {
            return redirect()->to(base_url('propostas/' . $idUltima));
        }

        $ehDono = (int) $proposta['id_dono'] === $idUsuario;
        $podeResponder = $this->propostaModel->podeResponder($proposta, $idUsuario)
            && $proposta['livro_status'] === 'disponivel';

        return view('propostas/ver', [
            'proposta'      => $proposta,
            'cadeia'        => $this->propostaModel->cadeia((int) $idProposta),
            'ehDono'        => $ehDono,
            'podeResponder' => $podeResponder,
            'podeCancelar'  => $proposta['status'] === 'pendente' && (int) $proposta['id_usuario_autor'] === $idUsuario,
            // Para a contraproposta de troca: os livros do interessado (a "outra pessoa", se quem responde é o dono)
            'livrosDoInteressado' => $podeResponder && $proposta['livro_tipo_transacao'] !== 'venda'
                ? $this->livroModel->listarDisponiveisParaTroca((int) $proposta['id_usuario_interessado'])
                : [],
        ]);
    }

    public function aceitar($idProposta = null)
    {
        // Quem tinha proposta pendente no mesmo livro é recusado automaticamente ao aceitar esta
        $concorrentes = $this->propostaModel->idsPendentesConcorrentes((int) $idProposta);

        $erro = $this->propostaModel->aceitar((int) $idProposta, (int) session()->get('usuario_id'));

        if ($erro === null) {
            foreach ($concorrentes as $idConcorrente) {
                service('notificacaoNegociacao')->propostaRecusada($idConcorrente, true);
            }
        }

        return redirect()->to(base_url('propostas/' . (int) $idProposta))
            ->with($erro ? 'erro' : 'sucesso', $erro ?? 'Proposta aceita! O livro foi reservado — combinem a entrega pelos contatos abaixo.');
    }

    public function recusar($idProposta = null)
    {
        $erro = $this->propostaModel->recusar((int) $idProposta, (int) session()->get('usuario_id'));

        if ($erro === null) {
            service('notificacaoNegociacao')->propostaRecusada((int) $idProposta);
        }

        return redirect()->to(base_url('propostas/' . (int) $idProposta))
            ->with($erro ? 'erro' : 'sucesso', $erro ?? 'Proposta recusada.');
    }

    public function cancelar($idProposta = null)
    {
        $erro = $this->propostaModel->cancelar((int) $idProposta, (int) session()->get('usuario_id'));

        if ($erro === null) {
            service('notificacaoNegociacao')->propostaCancelada((int) $idProposta);
        }

        return redirect()->to(base_url('propostas/' . (int) $idProposta))
            ->with($erro ? 'erro' : 'sucesso', $erro ?? 'Proposta cancelada.');
    }

    public function contrapropor($idProposta = null)
    {
        $tipo = $this->request->getPost('tipo');

        $dados = [
            'tipo'            => $tipo,
            'valor_oferecido' => $tipo === 'dinheiro' ? $this->request->getPost('valor_oferecido') : null,
            'mensagem'        => trim((string) $this->request->getPost('mensagem')) ?: null,
        ];
        $idsLivros = $tipo === 'troca'
            ? array_map('intval', (array) $this->request->getPost('livros_oferecidos'))
            : [];

        [$erros, $idNova] = $this->propostaModel->contrapropor(
            (int) $idProposta,
            (int) session()->get('usuario_id'),
            $dados,
            $idsLivros
        );

        if (isset($erros['geral'])) {
            return redirect()->to(base_url('propostas/' . (int) $idProposta))->withInput()
                ->with('erro', $erros['geral']);
        }

        if ($erros !== []) {
            return redirect()->to(base_url('propostas/' . (int) $idProposta))->withInput()
                ->with('errosContraproposta', $erros)
                ->with('abrirContraproposta', true);
        }

        return redirect()->to(base_url('propostas/' . $idNova))
            ->with('sucesso', 'Contraproposta enviada! Agora é a vez da outra pessoa responder.');
    }

    protected function contarAguardando(int $idUsuario, string $aba): int
    {
        return count(array_filter(
            $this->propostaModel->listarNegociacoes($idUsuario, $aba),
            fn ($n) => $n['aguardando_voce']
        ));
    }
}
