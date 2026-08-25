<div class="painel-cabecalho">
    <h2>Favoritos</h2>
    <p>Livros que você salvou para ver depois.</p>
</div>

<?php if (empty($favoritos)): ?>
    <p class="painel-vazio">Você ainda não favoritou nenhum livro.</p>
<?php else: ?>
    <ul class="lista-historico">
        <?php foreach ($favoritos as $item): ?>
            <li><strong><?= esc($item['titulo']) ?></strong></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>