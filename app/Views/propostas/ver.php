<?php
$p = $proposta;
$idUsuario = (int) session()->get('usuario_id');

$outraParte = $ehDono
    ? ['nome' => $p['interessado_nome'], 'email' => $p['interessado_email'], 'telefone' => $p['interessado_telefone']]
    : ['nome' => $p['dono_nome'], 'email' => $p['dono_email'], 'telefone' => $p['dono_telefone']];
$primeiroNomeOutra = strtok($outraParte['nome'], ' ');

$aceitaDinheiro = $p['livro_tipo_transacao'] !== 'troca';
$aceitaTroca    = $p['livro_tipo_transacao'] !== 'venda';

// Na troca, quem escolhe livros escolhe sempre do interessado
$livrosDeQuem = $ehDono ? 'de ' . $primeiroNomeOutra : 'seus';

$errosContra    = session('errosContraproposta') ?? [];
$tipoContra     = old('tipo', $p['tipo']);
$livrosMarcados = array_map('intval', (array) old(
    'livros_oferecidos',
    array_column($p['livros_oferecidos'], 'id_livro')
));

$nomeAutor = fn (array $oferta) => (int) $oferta['id_usuario_autor'] === $idUsuario
    ? 'Você'
    : ((int) $oferta['id_usuario_autor'] === (int) $oferta['id_usuario_interessado'] ? $oferta['interessado_nome'] : $oferta['dono_nome']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Proposta: <?= esc($p['livro_titulo']) ?> - Clube do Livro</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="<?= assetUrl('assets/styles/carteirinha.css') ?>">
<link rel="stylesheet" href="<?= assetUrl('assets/styles/propostas.css') ?>">
</head>
<body class="pagina-propostas">

<?= $this->include('partials/navbar') ?>

<main class="propostas propostas--ver">
    <a href="<?= site_url('propostas') . ($ehDono ? '' : '?aba=enviadas') ?>" class="propostas__voltar">
        &larr; Voltar para minhas propostas
    </a>

    <?php if (session()->getFlashdata('sucesso')): ?>
        <p class="propostas__aviso propostas__aviso--sucesso"><?= esc(session()->getFlashdata('sucesso')) ?></p>
    <?php endif; ?>
    <?php if (session()->getFlashdata('erro')): ?>
        <p class="propostas__aviso propostas__aviso--erro"><?= esc(session()->getFlashdata('erro')) ?></p>
    <?php endif; ?>

    <section class="negociacao">
        <div class="negociacao__livro">
            <img src="<?= esc($p['livro_capa'] ?: base_url('assets/img/capa-padrao.png')) ?>" alt="" class="propostas__capa">
            <div>
                <p class="negociacao__rotulo"><?= $ehDono ? 'Seu livro' : 'Livro de ' . esc($p['dono_nome']) ?></p>
                <h1><a href="<?= site_url('livro/detalhes/' . $p['id_livro']) ?>"><?= esc($p['livro_titulo']) ?></a></h1>
                <p class="propostas__meta"><?= esc($p['livro_autor']) ?></p>
                <?php if ($aceitaDinheiro && !empty($p['livro_preco'])): ?>
                    <p class="propostas__meta">Preço pedido: <strong>R$ <?= number_format((float) $p['livro_preco'], 2, ',', '.') ?></strong></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Oferta atual -->
        <div class="negociacao__atual negociacao__atual--<?= esc($p['status'], 'attr') ?>">
            <div class="negociacao__atual-topo">
                <span class="negociacao__rotulo">
                    <?php $tipoOferta = count($cadeia) > 1 ? 'contraproposta' : 'proposta'; ?>
                    <?= (int) $p['id_usuario_autor'] === $idUsuario
                        ? 'Sua ' . $tipoOferta
                        : ucfirst($tipoOferta) . ' de ' . esc($nomeAutor($p)) ?>
                </span>
                <span class="propostas__tag propostas__tag--<?= esc($p['status'], 'attr') ?>"><?= rotuloStatusProposta($p['status']) ?></span>
            </div>

            <p class="negociacao__oferta">
                <i class="fa-solid <?= $p['tipo'] === 'troca' ? 'fa-right-left' : 'fa-coins' ?>"></i>
                <?= $p['tipo'] === 'troca' ? 'Troca' : 'Pagamento em dinheiro' ?>
            </p>

            <?php if ($p['tipo'] === 'dinheiro'): ?>
                <p class="negociacao__valor"><?= esc(resumoOferta($p)) ?></p>
            <?php else: ?>
                <ul class="negociacao__livros">
                    <?php foreach ($p['livros_oferecidos'] as $l): ?>
                        <li>
                            <img src="<?= esc($l['imagem_capa'] ?: base_url('assets/img/capa-padrao.png')) ?>" alt="">
                            <span><strong><?= esc($l['titulo']) ?></strong><small><?= esc($l['autor']) ?></small></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($p['mensagem'])): ?>
                <blockquote class="negociacao__mensagem"><?= nl2br(esc($p['mensagem'])) ?></blockquote>
            <?php endif; ?>

            <?php if ($p['status'] === 'pendente'): ?>
                <p class="propostas__meta">
                    <i class="fa-regular fa-clock"></i>
                    Responder até <?= date('d/m/Y \à\s H:i', strtotime($p['prazo_limite'])) ?>
                </p>
            <?php endif; ?>

            <?php if ($podeResponder): ?>
                <div class="negociacao__acoes">
                    <form action="<?= site_url('propostas/' . $p['id_proposta'] . '/aceitar') ?>" method="post"
                          onsubmit="return confirm('Aceitar esta proposta? O livro ficará reservado e as outras propostas pendentes serão recusadas.');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-negociacao btn-negociacao--aceitar">
                            <i class="fa-solid fa-check"></i> Aceitar
                        </button>
                    </form>
                    <form action="<?= site_url('propostas/' . $p['id_proposta'] . '/recusar') ?>" method="post"
                          onsubmit="return confirm('Recusar esta proposta?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-negociacao btn-negociacao--recusar">
                            <i class="fa-solid fa-xmark"></i> Recusar
                        </button>
                    </form>
                    <button type="button" class="btn-negociacao" id="btnContraproposta"
                            aria-expanded="<?= session('abrirContraproposta') ? 'true' : 'false' ?>" aria-controls="formContraproposta">
                        <i class="fa-solid fa-reply"></i> Contraproposta
                    </button>
                </div>
            <?php elseif ($p['status'] === 'pendente' && $p['livro_status'] !== 'disponivel'): ?>
                <p class="negociacao__espera">O livro não está mais disponível para negociação.</p>
            <?php elseif ($podeCancelar): ?>
                <p class="negociacao__espera">
                    <i class="fa-solid fa-hourglass-half"></i> Aguardando a resposta de <?= esc($primeiroNomeOutra) ?>.
                </p>
                <form action="<?= site_url('propostas/' . $p['id_proposta'] . '/cancelar') ?>" method="post"
                      onsubmit="return confirm('Cancelar sua oferta? A negociação será encerrada.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-negociacao btn-negociacao--recusar">Cancelar minha oferta</button>
                </form>
            <?php endif; ?>

            <?php if ($p['status'] === 'aceita'): ?>
                <div class="negociacao__contato">
                    <p><strong>Negócio fechado!</strong> Combine a entrega com <?= esc($outraParte['nome']) ?>:</p>
                    <ul>
                        <?php if (!empty($outraParte['email'])): ?>
                            <li><i class="fa-solid fa-envelope"></i> <a href="mailto:<?= esc($outraParte['email'], 'attr') ?>"><?= esc($outraParte['email']) ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($outraParte['telefone'])): ?>
                            <li><i class="fa-solid fa-phone"></i> <?= esc($outraParte['telefone']) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($podeResponder): ?>
            <!-- Contraproposta -->
            <form action="<?= site_url('propostas/' . $p['id_proposta'] . '/contraproposta') ?>" method="post"
                  class="negociacao__contra" id="formContraproposta" <?= session('abrirContraproposta') ? '' : 'hidden' ?>>
                <?= csrf_field() ?>
                <h2>Sua contraproposta</h2>

                <?php if (!empty($errosContra)): ?>
                    <ul class="propostas__aviso propostas__aviso--erro">
                        <?php foreach ($errosContra as $erro): ?>
                            <li><?= esc($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($aceitaDinheiro && $aceitaTroca): ?>
                    <div class="negociacao__tipos" role="radiogroup" aria-label="Tipo de oferta">
                        <label><input type="radio" name="tipo" value="dinheiro" <?= $tipoContra === 'dinheiro' ? 'checked' : '' ?>><span><i class="fa-solid fa-coins"></i> Dinheiro</span></label>
                        <label><input type="radio" name="tipo" value="troca" <?= $tipoContra === 'troca' ? 'checked' : '' ?>><span><i class="fa-solid fa-right-left"></i> Troca</span></label>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="tipo" value="<?= $aceitaDinheiro ? 'dinheiro' : 'troca' ?>">
                <?php endif; ?>

                <?php if ($aceitaDinheiro): ?>
                    <div class="negociacao__secao" data-secao="dinheiro">
                        <label for="valor_oferecido">Valor (R$)</label>
                        <input type="number" name="valor_oferecido" id="valor_oferecido" step="0.01" min="0.01" inputmode="decimal"
                               value="<?= esc(old('valor_oferecido', $p['tipo'] === 'dinheiro' ? $p['valor_oferecido'] : '')) ?>">
                    </div>
                <?php endif; ?>

                <?php if ($aceitaTroca): ?>
                    <div class="negociacao__secao" data-secao="troca">
                        <p class="negociacao__rotulo">Livros <?= esc($livrosDeQuem) ?> na troca</p>

                        <ul class="negociacao__escolhidos" id="livrosEscolhidos" aria-live="polite"></ul>

                        <button type="button" class="btn-negociacao btn-negociacao--secundario" id="btnEscolherLivros">
                            <i class="fa-solid fa-book"></i> Escolher livros <?= esc($livrosDeQuem) ?>
                        </button>

                        <!-- Modal: livros cadastrados pelo interessado -->
                        <div class="modal-overlay" id="modalLivrosTroca">
                            <div class="modal-perfil modal-livros-troca" role="dialog" aria-modal="true" aria-labelledby="tituloLivrosTroca">
                                <button type="button" class="modal-fechar" data-fechar-modal aria-label="Fechar">&times;</button>
                                <h2 id="tituloLivrosTroca">Livros <?= esc($livrosDeQuem) ?></h2>
                                <p class="propostas__meta">
                                    <?= $ehDono
                                        ? 'Estes são os livros que ' . esc($primeiroNomeOutra) . ' tem disponíveis no Clube. Escolha o que você aceita na troca.'
                                        : 'Escolha quais dos seus livros você oferece.' ?>
                                </p>

                                <?php if (empty($livrosDoInteressado)): ?>
                                    <p class="propostas__vazio"><?= $ehDono ? esc($primeiroNomeOutra) . ' não tem' : 'Você não tem' ?> livros disponíveis para troca.</p>
                                <?php else: ?>
                                    <div class="negociacao__opcoes">
                                        <?php foreach ($livrosDoInteressado as $l): ?>
                                            <label class="negociacao__opcao">
                                                <input type="checkbox" name="livros_oferecidos[]" value="<?= (int) $l['id_livro'] ?>"
                                                       data-titulo="<?= esc($l['titulo'], 'attr') ?>"
                                                       <?= in_array((int) $l['id_livro'], $livrosMarcados, true) ? 'checked' : '' ?>>
                                                <img src="<?= esc($l['imagem_capa'] ?: base_url('assets/img/capa-padrao.png')) ?>" alt="">
                                                <span><strong><?= esc($l['titulo']) ?></strong><small><?= esc($l['autor']) ?> · <?= esc($l['genero']) ?></small></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <button type="button" class="btn-negociacao btn-negociacao--aceitar" data-fechar-modal>Pronto</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <label for="mensagem">Mensagem (opcional)</label>
                <textarea name="mensagem" id="mensagem" rows="3" maxlength="1000"><?= esc(old('mensagem', '')) ?></textarea>

                <button type="submit" class="btn-negociacao btn-negociacao--aceitar">Enviar contraproposta</button>
            </form>
        <?php endif; ?>

        <!-- Histórico da negociação -->
        <?php if (count($cadeia) > 1): ?>
            <div class="negociacao__historico">
                <h2>Histórico da negociação</h2>
                <ol>
                    <?php foreach ($cadeia as $oferta): ?>
                        <li class="<?= (int) $oferta['id_proposta'] === (int) $p['id_proposta'] ? 'is-atual' : '' ?>">
                            <span class="negociacao__rotulo">
                                <?= esc($nomeAutor($oferta)) ?> · <?= date('d/m/Y H:i', strtotime($oferta['data_criacao'])) ?>
                            </span>
                            <span><?= esc(resumoOferta($oferta)) ?></span>
                            <?php if (!empty($oferta['mensagem'])): ?>
                                <small>“<?= esc($oferta['mensagem']) ?>”</small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>
    </section>
</main>

<?= $this->include('partials/modal_perfil') ?>
<?= $this->include('partials/modal_perfil_script') ?>

<?= $this->include('partials/footer') ?>

<?php if ($podeResponder): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formContraproposta');
    const btnContra = document.getElementById('btnContraproposta');
    const secoes = form.querySelectorAll('.negociacao__secao');
    const modal = document.getElementById('modalLivrosTroca');
    const escolhidos = document.getElementById('livrosEscolhidos');

    btnContra.addEventListener('click', () => {
        form.hidden = !form.hidden;
        btnContra.setAttribute('aria-expanded', String(!form.hidden));
        if (!form.hidden) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    function tipo() {
        const marcado = form.querySelector('input[name="tipo"]:checked') || form.querySelector('input[name="tipo"]');
        return marcado ? marcado.value : null;
    }

    function atualizarSecoes() {
        secoes.forEach(secao => {
            const ativa = secao.dataset.secao === tipo();
            secao.hidden = !ativa;
            secao.querySelectorAll('input').forEach(input => { input.disabled = !ativa; });
        });
        const valor = document.getElementById('valor_oferecido');
        if (valor) valor.required = tipo() === 'dinheiro';
    }

    function atualizarEscolhidos() {
        if (!escolhidos) return;
        const marcados = form.querySelectorAll('input[name="livros_oferecidos[]"]:checked');
        escolhidos.innerHTML = '';
        if (!marcados.length) {
            escolhidos.innerHTML = '<li class="is-vazio">Nenhum livro escolhido.</li>';
            return;
        }
        marcados.forEach(input => {
            const li = document.createElement('li');
            li.textContent = input.dataset.titulo;
            escolhidos.appendChild(li);
        });
    }

    form.querySelectorAll('input[name="tipo"]').forEach(r => r.addEventListener('change', atualizarSecoes));
    form.querySelectorAll('input[name="livros_oferecidos[]"]').forEach(c => c.addEventListener('change', atualizarEscolhidos));

    if (modal) {
        document.getElementById('btnEscolherLivros').addEventListener('click', () => modal.classList.add('aberto'));
        modal.querySelectorAll('[data-fechar-modal]').forEach(b => b.addEventListener('click', () => modal.classList.remove('aberto')));
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('aberto'); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') modal.classList.remove('aberto'); });
    }

    form.addEventListener('submit', (e) => {
        if (tipo() === 'troca' && !form.querySelector('input[name="livros_oferecidos[]"]:checked')) {
            e.preventDefault();
            alert('Escolha ao menos um livro para a troca.');
        }
    });

    atualizarSecoes();
    atualizarEscolhidos();
});
</script>
<?php endif; ?>

</body>
</html>
