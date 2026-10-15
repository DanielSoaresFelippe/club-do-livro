<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Minhas propostas - Clube do Livro</title>
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

<main class="propostas">
    <header class="propostas__cabecalho">
        <h1>Minhas propostas</h1>
        <p>Acompanhe as ofertas nos seus livros e as que você fez.</p>
    </header>

    <?php if (session()->getFlashdata('erro')): ?>
        <p class="propostas__aviso propostas__aviso--erro"><?= esc(session()->getFlashdata('erro')) ?></p>
    <?php endif; ?>

    <nav class="propostas__abas" aria-label="Tipo de proposta">
        <?php foreach (['recebidas' => 'Recebidas', 'enviadas' => 'Enviadas'] as $chave => $rotulo): ?>
            <a href="<?= site_url('propostas') . ($chave === 'enviadas' ? '?aba=enviadas' : '') ?>"
               class="propostas__aba<?= $aba === $chave ? ' is-ativa' : '' ?>"
               <?= $aba === $chave ? 'aria-current="page"' : '' ?>>
                <?= $rotulo ?>
                <?php if ($contagem[$chave] > 0): ?>
                    <span class="propostas__contador" title="Aguardando sua resposta"><?= $contagem[$chave] ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if ($aba === 'recebidas'): ?>

        <?php if (empty($porLivro)): ?>
            <p class="propostas__vazio">Nenhum dos seus livros recebeu proposta ainda.</p>
        <?php endif; ?>

        <?php foreach ($porLivro as $livro): ?>
            <section class="propostas__livro">
                <div class="propostas__livro-topo">
                    <img src="<?= esc($livro['capa']) ?>" alt="" class="propostas__capa">
                    <div>
                        <h2>
                            <a href="<?= site_url('livro/detalhes/' . $livro['id_livro']) ?>"><?= esc($livro['titulo']) ?></a>
                        </h2>
                        <p class="propostas__meta">
                            <?= count($livro['negociacoes']) ?> negociaç<?= count($livro['negociacoes']) === 1 ? 'ão' : 'ões' ?>
                            <?php if ($livro['status'] === 'reservado'): ?>
                                · <span class="propostas__tag propostas__tag--aceita">Reservado</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <ul class="propostas__lista">
                    <?php foreach ($livro['negociacoes'] as $n): ?>
                        <li>
                            <a href="<?= site_url('propostas/' . $n['id_proposta']) ?>" class="propostas__item<?= $n['aguardando_voce'] ? ' is-sua-vez' : '' ?>">
                                <span class="propostas__item-quem">
                                    <i class="fa-solid fa-user"></i> <?= esc($n['interessado_nome']) ?>
                                </span>
                                <span class="propostas__item-oferta">
                                    <i class="fa-solid <?= $n['tipo'] === 'troca' ? 'fa-right-left' : 'fa-coins' ?>"></i>
                                    <?= esc(resumoOferta($n)) ?>
                                </span>
                                <span class="propostas__item-status">
                                    <?php if ($n['aguardando_voce']): ?>
                                        <span class="propostas__tag propostas__tag--sua-vez">Sua vez</span>
                                    <?php elseif ($n['status'] === 'pendente'): ?>
                                        <span class="propostas__tag">Aguardando <?= esc(strtok($n['interessado_nome'], ' ')) ?></span>
                                    <?php else: ?>
                                        <span class="propostas__tag propostas__tag--<?= esc($n['status'], 'attr') ?>"><?= rotuloStatusProposta($n['status']) ?></span>
                                    <?php endif; ?>
                                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endforeach; ?>

    <?php else: ?>

        <?php if (empty($negociacoes)): ?>
            <p class="propostas__vazio">
                Você ainda não fez nenhuma proposta.
                <a href="<?= site_url('livro') ?>">Ver catálogo</a>
            </p>
        <?php else: ?>
            <ul class="propostas__lista propostas__lista--enviadas">
                <?php foreach ($negociacoes as $n): ?>
                    <li>
                        <a href="<?= site_url('propostas/' . $n['id_proposta']) ?>" class="propostas__item<?= $n['aguardando_voce'] ? ' is-sua-vez' : '' ?>">
                            <img src="<?= esc($n['livro_capa'] ?: base_url('assets/img/capa-padrao.png')) ?>" alt="" class="propostas__capa propostas__capa--mini">
                            <span class="propostas__item-quem">
                                <strong><?= esc($n['livro_titulo']) ?></strong>
                                <small>de <?= esc($n['dono_nome']) ?></small>
                            </span>
                            <span class="propostas__item-oferta">
                                <i class="fa-solid <?= $n['tipo'] === 'troca' ? 'fa-right-left' : 'fa-coins' ?>"></i>
                                <?= esc(resumoOferta($n)) ?>
                            </span>
                            <span class="propostas__item-status">
                                <?php if ($n['aguardando_voce']): ?>
                                    <span class="propostas__tag propostas__tag--sua-vez">Contraproposta — sua vez</span>
                                <?php elseif ($n['status'] === 'pendente'): ?>
                                    <span class="propostas__tag">Aguardando o dono</span>
                                <?php else: ?>
                                    <span class="propostas__tag propostas__tag--<?= esc($n['status'], 'attr') ?>"><?= rotuloStatusProposta($n['status']) ?></span>
                                <?php endif; ?>
                                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    <?php endif; ?>
</main>

<?= $this->include('partials/modal_perfil') ?>
<?= $this->include('partials/modal_perfil_script') ?>

<?= $this->include('partials/footer') ?>
</body>
</html>
