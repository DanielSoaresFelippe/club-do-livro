<?php

namespace App\Services;

use App\Models\PropostaModel;
use CodeIgniter\Email\Email;
use Throwable;

/**
 * E-mails transacionais da negociação (views em app/Views/emails/).
 *
 *   service('notificacaoNegociacao')->novaProposta($idProposta);
 *
 * Regras:
 * - Uma falha de envio NUNCA interrompe a ação do usuário: o erro só vai para o log.
 * - Fora de produção, endereços *.test (domínio reservado, usado pelos usuários de teste)
 *   não são enviados de verdade: o e-mail é só registrado no log.
 */
class NotificacaoNegociacao
{
    protected PropostaModel $propostaModel;
    protected Email $email;

    public function __construct(?Email $email = null, ?PropostaModel $propostaModel = null)
    {
        $this->email         = $email ?? service('email', null, false);
        $this->propostaModel = $propostaModel ?? new PropostaModel();
        helper('proposta');
    }

    /**
     * Gatilho 1: alguém fez uma proposta no livro -> avisa o dono.
     */
    public function novaProposta(int $idProposta): bool
    {
        $p = $this->propostaModel->buscarComDetalhes($idProposta);

        if (!$p) {
            return false;
        }

        return $this->enviar(
            $p['dono_email'],
            'Nova proposta pelo seu livro: ' . $p['livro_titulo'],
            'emails/proposta_nova',
            [
                'nomeDestinatario' => $p['dono_nome'],
                'nomeAutor'        => $p['interessado_nome'],
                'tituloLivro'      => $p['livro_titulo'],
                'oferta'           => resumoOferta($p),
                'mensagem'         => $p['mensagem'],
                'prazo'            => date('d/m/Y \à\s H:i', strtotime($p['prazo_limite'])),
                'link'             => base_url('propostas/' . $idProposta),
            ]
        );
    }

    /**
     * Gatilho 2: quem fez a oferta desistiu -> avisa a outra parte.
     */
    public function propostaCancelada(int $idProposta): bool
    {
        $p = $this->propostaModel->buscarComDetalhes($idProposta);

        if (!$p) {
            return false;
        }

        $destinatario = $this->outraParte($p, (int) $p['id_usuario_autor']);

        return $this->enviar(
            $destinatario['email'],
            'Proposta cancelada: ' . $p['livro_titulo'],
            'emails/proposta_cancelada',
            [
                'nomeDestinatario' => $destinatario['nome'],
                'nomeAutor'        => $this->nomeDoAutor($p),
                'tituloLivro'      => $p['livro_titulo'],
                'oferta'           => resumoOferta($p),
                'link'             => base_url('propostas' . ($destinatario['ehDono'] ? '' : '?aba=enviadas')),
            ]
        );
    }

    /**
     * Gatilho 3: a oferta foi recusada -> avisa quem fez a oferta.
     *
     * @param bool $porOutraNegociacao true quando a recusa foi automática
     *                                 (o dono aceitou outra proposta para o mesmo livro)
     */
    public function propostaRecusada(int $idProposta, bool $porOutraNegociacao = false): bool
    {
        $p = $this->propostaModel->buscarComDetalhes($idProposta);

        if (!$p) {
            return false;
        }

        $idAutor      = (int) $p['id_usuario_autor'];
        $autor        = $this->dadosDoUsuario($p, $idAutor);
        $quemRecusou  = $this->outraParte($p, $idAutor);

        return $this->enviar(
            $autor['email'],
            'Proposta recusada: ' . $p['livro_titulo'],
            'emails/proposta_recusada',
            [
                'nomeDestinatario'   => $autor['nome'],
                'nomeQuemRecusou'    => $quemRecusou['nome'],
                'tituloLivro'        => $p['livro_titulo'],
                'oferta'             => resumoOferta($p),
                'porOutraNegociacao' => $porOutraNegociacao,
                'link'               => base_url('livro'),
            ]
        );
    }

    // ---------------------------------------------------------------------

    protected function enviar(?string $para, string $assunto, string $view, array $dados): bool
    {
        if (empty($para)) {
            return false;
        }

        if ($this->ehEnderecoDeTeste($para)) {
            log_message('info', "[e-mail não enviado: endereço de teste] Para: {$para} | Assunto: {$assunto}");

            return true;
        }

        try {
            $this->email->clear();
            $this->email->setTo($para);
            $this->email->setSubject($assunto);
            $this->email->setMailType('html');
            $this->email->setMessage(view($view, $dados));

            if ($this->email->send(false)) {
                return true;
            }

            log_message('error', "Falha ao enviar e-mail '{$assunto}' para {$para}: "
                . strip_tags($this->email->printDebugger(['headers'])));
        } catch (Throwable $e) {
            log_message('error', "Erro ao enviar e-mail '{$assunto}' para {$para}: " . $e->getMessage());
        }

        return false;
    }

    /**
     * .test é um domínio reservado (RFC 2606) que nunca recebe e-mail: usado pelos usuários
     * de teste. Fora de produção, não tenta enviar (evita devoluções na caixa do remetente).
     */
    protected function ehEnderecoDeTeste(string $para): bool
    {
        return ENVIRONMENT !== 'production' && str_ends_with(strtolower($para), '.test');
    }

    /**
     * @return array{nome: string, email: string, ehDono: bool}
     */
    protected function dadosDoUsuario(array $p, int $idUsuario): array
    {
        $ehDono = $idUsuario === (int) $p['id_dono'];

        return [
            'nome'   => $ehDono ? $p['dono_nome'] : $p['interessado_nome'],
            'email'  => $ehDono ? $p['dono_email'] : $p['interessado_email'],
            'ehDono' => $ehDono,
        ];
    }

    protected function outraParte(array $p, int $idUsuario): array
    {
        $idOutra = $idUsuario === (int) $p['id_dono'] ? (int) $p['id_usuario_interessado'] : (int) $p['id_dono'];

        return $this->dadosDoUsuario($p, $idOutra);
    }

    protected function nomeDoAutor(array $p): string
    {
        return $this->dadosDoUsuario($p, (int) $p['id_usuario_autor'])['nome'];
    }
}
