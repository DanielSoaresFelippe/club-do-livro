<?= $this->include('partials/navbar') ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="<?= assetUrl('assets/styles/carteirinha.css') ?>">
</head>
<body>
<section class="livro-hero">
    <div class="livro-hero__overlay"></div>
</section>

<div class="onda-livro" aria-hidden="true">
    <svg viewBox="0 0 1200 110" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path class="onda-livro__preenchimento"
              d="M0,60 C 100,15 200,105 300,60 C 400,15 500,105 600,60 C 700,15 800,105 900,60 C 1000,15 1100,105 1200,60 L1200,110 L0,110 Z" />
    </svg>
</div>

<section class="livro-filtros">
    <form method="get" action="<?= base_url('livro') ?>" class="livro-filtros__form">
        <div class="campo">
            <label for="tipo_transacao">Tipo</label>
            <select name="tipo_transacao" id="tipo_transacao">
                <option value="">Todos</option>
                <option value="venda" <?= $tipoSelecionado === 'venda' ? 'selected' : '' ?>>Venda</option>
                <option value="troca" <?= $tipoSelecionado === 'troca' ? 'selected' : '' ?>>Troca</option>
                <option value="ambos" <?= $tipoSelecionado === 'ambos' ? 'selected' : '' ?>>Venda ou troca</option>
            </select>
        </div>

        <div class="campo">
            <label for="genero">Gênero</label>
            <select name="genero" id="genero">
                <option value="">Todos os gêneros</option>
                <?php foreach ($generos as $genero): ?>
                    <option value="<?= $genero['id_genero'] ?>"
                        <?= (int) $generoSelecionado === (int) $genero['id_genero'] ? 'selected' : '' ?>>
                        <?= esc($genero['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Filtrar</button>

        <?php if ($generoSelecionado || $tipoSelecionado): ?>
            <a href="<?= base_url('livro') ?>" class="livro-filtros__limpar">Limpar filtros</a>
        <?php endif; ?>
    </form>
</section>

<section class="livro-grid">
    <?php if (empty($livros)): ?>
        <p class="livro-grid__vazio">Nenhum livro encontrado com esses filtros.</p>
    <?php else: ?>
        <div class="livro-grid__lista">
            <?php foreach ($livros as $livro): ?>
                <a href="<?= base_url('livro/detalhes/' . $livro['id_livro']) ?>" class="livro-card">
                    <div class="livro-card__capa">
                        <img src="<?= esc($livro['imagem_capa'] ?? base_url('assets/img/capa-padrao.png')) ?>"
                             alt="Capa de <?= esc($livro['titulo']) ?>">

                        <span class="livro-card__badge livro-card__badge--<?= esc($livro['tipo_transacao']) ?>">
                            <?= [
                                'venda' => 'Venda',
                                'troca' => 'Troca',
                                'ambos' => 'Venda ou troca',
                            ][$livro['tipo_transacao']] ?? esc($livro['tipo_transacao']) ?>
                        </span>
                    </div>

                    <div class="livro-card__info">
                        <span class="livro-card__genero"><?= esc($livro['genero']) ?></span>
                        <h3><?= esc($livro['titulo']) ?></h3>
                        <p class="livro-card__autor"><?= esc($livro['autor']) ?></p>

                        <?php if (in_array($livro['tipo_transacao'], ['venda', 'ambos'], true) && $livro['preco']): ?>
                            <span class="livro-card__preco">
                                R$ <?= number_format((float) $livro['preco'], 2, ',', '.') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($pager->getPageCount('livros') > 1): ?>
            <nav class="livro-paginacao">

                <!-- BOTÃO ANTERIOR -->
                <?php $urlAnterior = $pager->getPreviousPageURI('livros'); ?>
                <?php if ($urlAnterior): ?>
                    <a href="<?= $urlAnterior ?>" class="livro-paginacao__seta">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php else: ?>
                    <span class="livro-paginacao__seta livro-paginacao__seta--desabilitada">
                        <i class="fa-solid fa-chevron-left"></i>
                    </span>
                <?php endif; ?>

                <!-- INDICADOR -->
                <span class="livro-paginacao__info">
                    Página <?= $pager->getCurrentPage('livros') ?> de <?= $pager->getPageCount('livros') ?>
                </span>

                <!-- BOTÃO PRÓXIMO -->
                <?php $urlProxima = $pager->getNextPageURI('livros'); ?>
                <?php if ($urlProxima): ?>
                    <a href="<?= $urlProxima ?>" class="livro-paginacao__seta">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="livro-paginacao__seta livro-paginacao__seta--desabilitada">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                <?php endif; ?>

            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>
</body>
</html>
<?= $this->include('partials/footer') ?>