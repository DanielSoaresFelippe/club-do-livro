<?php
/**
 * Grade do catálogo + paginação.
 * Usada na página (livro/index) e na resposta da busca dinâmica (fetch).
 */
?>
    <?php $total = $pager->getTotal('livros'); ?>
    <p class="livro-grid__total">
        <?= $total === 1 ? '1 livro encontrado' : number_format($total, 0, ',', '.') . ' livros encontrados' ?>
    </p>

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

                        <?php if ((int) session()->get('usuario_id') === (int) $livro['id_usuario']): ?>
                            <span class="livro-card__dono"><i class="fa-solid fa-user"></i> Seu livro</span>
                        <?php endif; ?>

                        <?php if (!empty($livro['localizacao'])): ?>
                            <p class="livro-card__localizacao">
                                <i class="fa-solid fa-location-dot"></i> <?= esc($livro['localizacao']) ?>
                            </p>
                        <?php endif; ?>
                        
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
