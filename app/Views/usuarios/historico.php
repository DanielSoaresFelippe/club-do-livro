<?php
// Dados fictícios para preencher o varal do histórico (uso da clude para cria-los)
$historico = [
    [
        'titulo'      => 'A Menina que Roubava Livros',
        'autor'       => 'Markus Zusak',
        'tipo'        => 'troca',      // troca | venda | ambos
        'status'      => 'disponivel', // disponivel | reservado | indisponivel
        'preco'       => null,
        'imagem_capa' => 'https://placehold.co/300x420/e59fc2/2a2313?text=A+Menina',
    ],
    [
        'titulo'      => 'O Nome do Vento',
        'autor'       => 'Patrick Rothfuss',
        'tipo'        => 'venda',
        'status'      => 'reservado',
        'preco'       => 'R$ 32,00',
        'imagem_capa' => 'https://placehold.co/300x420/c7c364/2a2313?text=Nome+do+Vento',
    ],
    [
        'titulo'      => 'Duna',
        'autor'       => 'Frank Herbert',
        'tipo'        => 'ambos',
        'status'      => 'disponivel',
        'preco'       => 'R$ 28,00',
        'imagem_capa' => 'https://placehold.co/300x420/8f8c3a/fbf3e2?text=Duna',
    ],
    [
        'titulo'      => 'Homem-Aranha: De Volta ao Lar',
        'autor'       => 'Marvel Comics',
        'tipo'        => 'venda',
        'status'      => 'disponivel',
        'preco'       => 'R$ 18,00',
        'imagem_capa' => 'https://placehold.co/300x420/f3cadd/2a2313?text=Homem-Aranha',
    ],
    [
        'titulo'      => 'Orgulho e Preconceito',
        'autor'       => 'Jane Austen',
        'tipo'        => 'troca',
        'status'      => 'indisponivel',
        'preco'       => null,
        'imagem_capa' => 'https://placehold.co/300x420/ecb8d3/2a2313?text=Orgulho',
    ],
    [
        'titulo'      => '1984',
        'autor'       => 'George Orwell',
        'tipo'        => 'ambos',
        'status'      => 'disponivel',
        'preco'       => 'R$ 22,00',
        'imagem_capa' => 'https://placehold.co/300x420/4d4c26/fbf3e2?text=1984',
    ],
    [
        'titulo'      => 'X-Men: Dias de um Futuro Esquecido',
        'autor'       => 'Marvel Comics',
        'tipo'        => 'venda',
        'status'      => 'reservado',
        'preco'       => 'R$ 15,00',
        'imagem_capa' => 'https://placehold.co/300x420/b6b24a/2a2313?text=X-Men',
    ],
];
?>

<div class="painel-cabecalho">
    <h2>Histórico de Livros</h2>
    <p>Livros que você colocou à venda, trocou ou já trocou com outros leitores.</p>
</div>

<?php if (empty($historico)): ?>
    <p class="painel-vazio">
        Você ainda não tem nenhum livro no histórico.
    </p>
<?php else: ?>

    <?php $paginasHistorico = array_chunk($historico, 5); ?>

    <div class="varal-container">
        <div class="varal-viewport">
            <div class="varal-trilha" id="varalTrilha">
                <?php foreach ($paginasHistorico as $indicePagina => $pagina): ?>
                    <div class="varal-pagina" data-pagina="<?= $indicePagina ?>">
                        <?php foreach ($pagina as $indiceLivro => $item): ?>
                            <div class="roupa-livro roupa-<?= $indiceLivro + 1 ?>">
                                <article class="livro-cartao">
                                    <span class="livro-tag tag-<?= esc($item['tipo']) ?>">
                                        <?= $item['tipo'] === 'troca' ? 'Troca' : ($item['tipo'] === 'venda' ? 'Venda' : 'Troca ou venda') ?>
                                    </span>

                                    <div class="livro-capa">
                                        <img
                                            src="<?= esc($item['imagem_capa'] ?? base_url('assets/img/capa-padrao.png')) ?>"
                                            alt="<?= esc($item['titulo']) ?>"
                                            loading="lazy"
                                        >
                                        <span class="livro-status livro-status-<?= esc($item['status']) ?>">
                                            <?= esc($item['status']) ?>
                                        </span>
                                    </div>

                                    <div class="livro-info">
                                        <strong class="livro-titulo"><?= esc($item['titulo']) ?></strong>
                                        <span class="livro-autor"><?= esc($item['autor']) ?></span>

                                        <div class="livro-meta">
                                            <?php if ($item['preco']): ?>
                                                <span class="livro-preco"><?= esc($item['preco']) ?></span>
                                            <?php else: ?>
                                                <span class="livro-preco livro-preco-troca">Somente troca</span>
                                            <?php endif; ?>
                                            <a href="#" class="btn btn-outline btn-sm">Ver</a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (count($paginasHistorico) > 1): ?>
            <div class="varal-navegacao">
                <button type="button" class="varal-seta" id="varalAnterior" disabled aria-label="Página anterior">
                    &larr;
                </button>

                <div class="varal-pontos" id="varalPontos">
                    <?php for ($i = 0; $i < count($paginasHistorico); $i++): ?>
                        <span class="varal-ponto<?= $i === 0 ? ' ativo' : '' ?>"></span>
                    <?php endfor; ?>
                </div>

                <button type="button" class="varal-seta" id="varalProximo" aria-label="Próxima página">
                    &rarr;
                </button>
            </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const trilha = document.getElementById('varalTrilha');
    if (!trilha) return;

    const paginas = trilha.querySelectorAll('.varal-pagina');
    const pontos = document.querySelectorAll('#varalPontos .varal-ponto');
    const btnAnterior = document.getElementById('varalAnterior');
    const btnProximo = document.getElementById('varalProximo');

    let paginaAtual = 0;
    const totalPaginas = paginas.length;
    let animando = false;

    function irParaPagina(indice) {
        if (animando || indice < 0 || indice >= totalPaginas || indice === paginaAtual) return;

        animando = true;
        paginaAtual = indice;

        trilha.classList.add('puxando');
        trilha.style.transform = `translateX(-${paginaAtual * 100}%)`;

        pontos.forEach((ponto, i) => ponto.classList.toggle('ativo', i === paginaAtual));

        if (btnAnterior) btnAnterior.disabled = paginaAtual === 0;
        if (btnProximo) btnProximo.disabled = paginaAtual === totalPaginas - 1;

        trilha.addEventListener('transitionend', function limpar() {
            trilha.classList.remove('puxando');
            animando = false;
            trilha.removeEventListener('transitionend', limpar);
        });
    }

    btnAnterior?.addEventListener('click', () => irParaPagina(paginaAtual - 1));
    btnProximo?.addEventListener('click', () => irParaPagina(paginaAtual + 1));

    let toqueInicioX = 0;
    trilha.addEventListener('touchstart', (e) => { toqueInicioX = e.touches[0].clientX; }, { passive: true });
    trilha.addEventListener('touchend', (e) => {
        const delta = e.changedTouches[0].clientX - toqueInicioX;
        if (Math.abs(delta) < 40) return;
        delta < 0 ? irParaPagina(paginaAtual + 1) : irParaPagina(paginaAtual - 1);
    }, { passive: true });
});
</script>