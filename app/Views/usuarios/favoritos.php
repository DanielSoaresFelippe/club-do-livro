<?php
if (!isset($favoritos)) {
    $favoritos = [
        ['id_livro' => 1, 'titulo' => 'O Sol Entre as Páginas', 'autor' => 'Marina Aguiar', 'capa' => 'livro1.jpg'],
        ['id_livro' => 2, 'titulo' => 'Cartas Para Ninguém', 'autor' => 'Théo Bastos', 'capa' => 'livro2.jpg'],
        ['id_livro' => 3, 'titulo' => 'A Menina do Vento Sul', 'autor' => 'Iracema Coutinho', 'capa' => 'livro3.jpg'],
        ['id_livro' => 4, 'titulo' => 'Fragmentos de Uma Cidade', 'autor' => 'Rui Sampaio', 'capa' => 'livro4.jpg'],
        ['id_livro' => 5, 'titulo' => 'O Último Verão em Ipanema', 'autor' => 'Clarice Novaes', 'capa' => 'livro5.jpg'],
        ['id_livro' => 6, 'titulo' => 'Sob o Céu de Outubro', 'autor' => 'Vinícius Prado', 'capa' => 'livro6.jpg'],
    ];
}
?>

<div class="painel-cabecalho-favoritos">
    <h2>Favoritos</h2>
    <p>Livros que você salvou para ver depois.</p>
</div>

<?php if (empty($favoritos)): ?>
    <p class="painel-vazio">Você ainda não favoritou nenhum livro.</p>
<?php else: ?>
    <div class="favoritos-cesta">
        <div class="favoritos-viewport">
            <div class="favoritos-trilha">
                <?php foreach (array_chunk($favoritos, 3) as $pagina): ?>
                    <div class="favoritos-pagina">
                        <?php foreach ($pagina as $i => $item): ?>
                            <a href="<?= base_url('livro/detalhes/' . esc($item['id_livro'], 'url')) ?>" class="livro-cartao-favorito tilt-<?= $i % 3 ?>">
                                <div class="livro-cartao">
                                    <span class="livro-tag tag-favorito">Favorito</span>
                                    <div class="livro-capa">
                                        <img src="<?= base_url('uploads/livros/' . esc($item['capa'])) ?>" alt="<?= esc($item['titulo']) ?>">
                                    </div>
                                    <div class="livro-info">
                                        <span class="livro-titulo"><?= esc($item['titulo']) ?></span>
                                        <span class="livro-autor"><?= esc($item['autor']) ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="favoritos-navegacao">
            <button type="button" class="varal-seta" id="favSetaAnterior" aria-label="Página anterior">&#8592;</button>
            <div class="varal-pontos" id="favPontos"></div>
            <button type="button" class="varal-seta" id="favSetaProxima" aria-label="Próxima página">&#8594;</button>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const trilha = document.querySelector('.favoritos-trilha');
        if (!trilha) return;

        const paginas = trilha.querySelectorAll('.favoritos-pagina');
        const pontosContainer = document.getElementById('favPontos');
        const setaAnterior = document.getElementById('favSetaAnterior');
        const setaProxima = document.getElementById('favSetaProxima');
        let paginaAtual = 0;

        paginas.forEach((_, i) => {
            const ponto = document.createElement('span');
            ponto.classList.add('varal-ponto');
            if (i === 0) ponto.classList.add('ativo');
            ponto.addEventListener('click', () => irParaPagina(i));
            pontosContainer.appendChild(ponto);
        });

        function atualizar() {
            trilha.style.transform = `translateY(-${paginaAtual * 100}%)`;
            pontosContainer.querySelectorAll('.varal-ponto').forEach((p, i) => {
                p.classList.toggle('ativo', i === paginaAtual);
            });
            setaAnterior.disabled = paginaAtual === 0;
            setaProxima.disabled = paginaAtual === paginas.length - 1;
        }

        function irParaPagina(indice) {
            paginaAtual = Math.max(0, Math.min(indice, paginas.length - 1));
            atualizar();
        }

        setaAnterior.addEventListener('click', () => irParaPagina(paginaAtual - 1));
        setaProxima.addEventListener('click', () => irParaPagina(paginaAtual + 1));

        atualizar();
    });
</script>