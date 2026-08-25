<div class="painel-cabecalho">
    <h2>Histórico de Livros</h2>
    <p>Livros que você colocou à venda, trocou ou já trocou com outros leitores.</p>
</div>

<?php if (empty($historico)): ?>
    <p class="painel-vazio">Você ainda não tem nenhum livro no histórico.</p>
<?php else: ?>
    <ul class="lista-historico">
        <?php foreach ($historico as $item): ?>
            <li>
                <strong><?= esc($item['titulo']) ?></strong>
                <span class="status-<?= esc($item['status']) ?>"><?= esc($item['status']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>