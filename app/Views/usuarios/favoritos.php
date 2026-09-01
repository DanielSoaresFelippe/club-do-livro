<?php
if (!isset($favoritos)) {
    $favoritos = [
        ['id_livro' => 1, 'titulo' => 'O Sol Entre as Páginas', 'autor' => 'Marina Aguiar', 'capa' => base_url('assets/img/heart.jpg')],
        ['id_livro' => 2, 'titulo' => 'Cartas Para Ninguém', 'autor' => 'Théo Bastos', 'capa' => 'livro2.jpg'],
        ['id_livro' => 3, 'titulo' => 'A Menina do Vento Sul', 'autor' => 'Iracema Coutinho', 'capa' => 'livro3.jpg'],
        ['id_livro' => 4, 'titulo' => 'Fragmentos de Uma Cidade', 'autor' => 'Rui Sampaio', 'capa' => 'livro4.jpg'],
        ['id_livro' => 5, 'titulo' => 'O Último Verão em Ipanema', 'autor' => 'Clarice Novaes', 'capa' => 'livro5.jpg'],
        ['id_livro' => 6, 'titulo' => 'Sob o Céu de Outubro', 'autor' => 'Vinícius Prado', 'capa' => 'livro6.jpg'],
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Favoritos - Clube do Livro</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="<?= assetUrl('assets/styles/carteirinha.css') ?>">
</head>
<body>

<?= $this->include('partials/navbar') ?>

<header class="header-favoritos">
    <div class="header-favoritos-conteudo">
        <h2>Favoritos</h2>
        <span class="eyebrow" style="color: var(--ink);">seus livros guardadinhos</span>
        <p style="color: var(--ink)">Aqueles livros que você separou pra ler com calma, um dia desses.</p>
    </div>
</header>

<div class="onda-abelha" aria-hidden="true">
    <svg viewBox="0 0 1200 110" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <path class="onda-preenchimento"
              d="M0,60 C 100,15 200,105 300,60 C 400,15 500,105 600,60 C 700,15 800,105 900,60 C 1000,15 1100,105 1200,60 L1200,110 L0,110 Z" />
        <path id="trilhaOnda" class="onda-linha"
              d="M0,60 C 100,15 200,105 300,60 C 400,15 500,105 600,60 C 700,15 800,105 900,60 C 1000,15 1100,105 1200,60" />

        <image class="abelhinha"
            href="<?= base_url('assets/img/abelhinha.png') ?>"
            xlink:href="<?= base_url('assets/img/abelhinha.png') ?>"
            width="130" height="95" x="-65" y="-47.5">
            <animateMotion dur="15s" repeatCount="indefinite" rotate="autoReverse">
                <mpath href="#trilhaOnda" />
            </animateMotion>
        </image>
    </svg>
</div>

<section class="painel-pasta painel-ativo pagina-secundaria" data-painel="favoritos">
    <div class="painel-conteudo">
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
                                                <img src="<?= esc($item['capa']) ?>" alt="<?= esc($item['titulo']) ?>">
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
    </div>
</section>

<?= $this->include('partials/modal_perfil') ?>

<?= $this->include('partials/footer') ?>

<?= $this->include('partials/modal_perfil_script') ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const trilha = document.querySelector('.favoritos-trilha');
    if (trilha) {
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
    }

    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 30);
    });
});
</script>

</body>
</html>