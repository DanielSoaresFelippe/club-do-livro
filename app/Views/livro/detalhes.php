<?php
if (!isset($livro)) {
    $livro = [
        'id_livro'    => 0,
        'id_usuario'  => 0,
        'titulo'      => 'Livro não encontrado',
        'autor'       => '',
        'genero'      => '',
        'tipo_transacao'        => 'venda',
        'status'      => 'indisponivel',
        'preco'       => null,
        'faixa_etaria'=> null,
        'descricao'   => '',
        'imagem_capa' => base_url('assets/img/capa-padrao.png'),
        'galeria'     => [],
        'dono_nome'      => '',
        'dono_email'     => '',
        'dono_telefone'  => '',
    ];
}

$rotulosTipo = [
    'venda' => 'à venda',
    'troca' => 'para troca',
    'ambos' => 'venda ou troca',
];

$rotulosStatus = [
    'disponivel'   => 'disponível',
    'reservado'    => 'reservado',
    'indisponivel' => 'indisponível',
];

$galeria = !empty($livro['galeria']) ? $livro['galeria'] : [$livro['imagem_capa']];

$generosDisponiveis = [];
foreach ($recomendados as $item) {
    if (!empty($item['genero']) && !in_array($item['genero'], $generosDisponiveis, true)) {
        $generosDisponiveis[] = $item['genero'];
    }
}
sort($generosDisponiveis);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title><?= esc($livro['titulo']) ?> - Clube do Livro</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="<?= assetUrl('assets/styles/carteirinha.css') ?>">
<link rel="stylesheet" href="<?= assetUrl('assets/styles/livro_detalhes.css') ?>">
</head>
<body>

<?= $this->include('partials/navbar') ?>

<section class="painel-pasta painel-ativo pagina-secundaria" data-painel="livro-detalhes">
    <div class="painel-conteudo">

       <a href="javascript:history.back()" class="detalhes-voltar">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>

        <?php if (session()->getFlashdata('sucesso')): ?>
            <p class="detalhes-sucesso-aviso">
                <?= esc(session()->getFlashdata('sucesso')) ?>
            </p>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')): ?>
            <p class="detalhes-erro-aviso">
                <?= esc(session()->getFlashdata('erro')) ?>
            </p>
        <?php endif; ?>

        <div class="detalhes-hero">
            <div class="detalhes-hero-texto">
                <h1><?= esc($livro['titulo']) ?></h1>
                <p class="detalhes-autor"><?= esc($livro['autor']) ?></p>

                <div class="detalhes-tags">
                    <?php if (!empty($livro['genero'])): ?>
                        <span class="detalhes-pill"><?= esc($livro['genero']) ?></span>
                    <?php endif; ?>
                    <span class="detalhes-pill"><?= esc($rotulosTipo[$livro['tipo_transacao']] ?? $livro['tipo_transacao']) ?></span>
                    <?php if (!empty($livro['faixa_etaria'])): ?>
                        <span class="detalhes-pill"><?= esc($livro['faixa_etaria']) ?></span>
                    <?php endif; ?>
                    <span class="detalhes-pill detalhes-pill-status detalhes-pill-status-<?= esc($livro['status'], 'attr') ?>">
                        <?= esc($rotulosStatus[$livro['status']] ?? $livro['status']) ?>
                    </span>
                </div>

                <?php if (!empty($livro['descricao'])): ?>
                    <div class="detalhes-descricao">
                        <?= nl2br(esc($livro['descricao'])) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($livro['localizacao'])): ?>
                    <p class="detalhes-localizacao">
                        <i class="fa-solid fa-location-dot"></i> <?= esc($livro['localizacao']) ?>
                    </p>
                <?php endif; ?>

                <?php if ($livro['status'] === 'disponivel'): ?>
                    <div class="detalhes-precos">
                        <?php if ($livro['tipo_transacao'] !== 'troca' && !empty($livro['preco'])): ?>
                            <button type="button" class="detalhes-card-preco is-selecionavel is-ativo" data-acao="comprar">
                                <span class="detalhes-card-preco-label">Preço</span>
                                <span class="detalhes-card-preco-valor"><?= esc($livro['preco']) ?></span>
                            </button>
                        <?php endif; ?>
                        <?php if ($livro['tipo_transacao'] !== 'venda'): ?>
                            <button type="button" class="detalhes-card-preco is-selecionavel<?= $livro['tipo_transacao'] === 'troca' ? ' is-ativo' : '' ?>" data-acao="trocar">
                                <span class="detalhes-card-preco-label">Troca</span>
                                <span class="detalhes-card-preco-valor">Propor troca</span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if ((int) session()->get('usuario_id') !== (int) $livro['id_usuario']): ?>
                        <button type="button" class="blob-btn detalhes-btn-chat" id="btnConversarDono">
                            <span class="blob-btn__text">Conversar com o dono</span>

                            <span class="blob-btn__inner">
                                <span class="blob-btn__blobs">
                                    <span class="blob-btn__blob"></span>
                                    <span class="blob-btn__blob"></span>
                                    <span class="blob-btn__blob"></span>
                                    <span class="blob-btn__blob"></span>
                                </span>
                            </span>
                        </button>
                    <?php endif; ?>
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1">
                        <defs>
                            <filter id="goo">
                            <feGaussianBlur in="SourceGraphic" result="blur" stdDeviation="10"></feGaussianBlur>
                            <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 21 -7" result="goo"></feColorMatrix>
                            <feBlend in2="goo" in="SourceGraphic" result="mix"></feBlend>
                            </filter>
                        </defs>
                    </svg>
                <?php else: ?>
                    <p class="detalhes-indisponivel-aviso">Este livro não está disponível no momento.</p>
                <?php endif; ?>
            </div>

            <div class="detalhes-hero-capa">
                <div class="detalhes-capa-fundo">
                    <?php foreach ($galeria as $i => $imagem): ?>
                        <img src="<?= esc($imagem) ?>" alt="<?= esc($livro['titulo']) ?>"
                             class="detalhes-capa-img<?= $i === 0 ? ' is-ativa' : '' ?>"
                             data-indice="<?= $i ?>">
                    <?php endforeach; ?>
                </div>

                <?php if (count($galeria) > 1): ?>
                    <div class="detalhes-capa-pontos">
                        <?php foreach ($galeria as $i => $imagem): ?>
                            <span class="detalhes-capa-ponto<?= $i === 0 ? ' ativo' : '' ?>" data-indice="<?= $i ?>"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <button
                    type="button"
                    class="btn-favoritar<?= $favoritado ? ' favoritado' : '' ?>"
                    data-id-livro="<?= esc($livro['id_livro']) ?>"
                    aria-label="Favoritar livro"
                >
                    <i class="fa-solid fa-heart"></i>
                </button>
            </div>
        </div>

        <?php if (!empty($confirmarExclusao)): ?>
            <div class="detalhes-confirmar-exclusao">
                <p>Deseja realmente excluir <strong><?= esc($livro['titulo']) ?></strong>? Essa ação não pode ser desfeita.</p>

                <div class="detalhes-confirmar-exclusao__acoes">
                    <a href="<?= site_url('livro/detalhes/' . $livro['id_livro']) ?>" class="btn-secundario">
                        Cancelar
                    </a>

                    <form action="<?= site_url('livro/excluir/' . $livro['id_livro']) ?>" method="post" style="display:inline;">
                        <button type="submit" class="btn-perigo">
                            Sim, excluir livro
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="detalhes-recomendados">
            <div class="detalhes-recomendados-cabecalho">

                <h2>Você pode gostar</h2>

                <?php if (!empty($generosDisponiveis)): ?>

                    <div class="detalhes-filtro-genero" id="filtroGenero">

                        <button
                            type="button"
                            class="filtro-pill ativo"
                            data-genero="todos">
                            Todos
                        </button>

                        <?php foreach ($generosDisponiveis as $genero): ?>

                            <button
                                type="button"
                                class="filtro-pill"
                                data-genero="<?= esc($genero, 'attr') ?>">
                                <?= esc($genero) ?>
                            </button>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

            <div class="detalhes-recomendados-grid" id="gradeRecomendados">
                <?php foreach ($recomendados as $item): ?>
                    <a
                        href="<?= base_url('livro/detalhes/' . esc($item['id_livro'], 'url')) ?>"
                        class="recomendado-card"
                        data-genero="<?= esc($item['genero'] ?? '', 'attr') ?>"
                    >
                        <img
                            src="<?= esc($item['capa']) ?>"
                            alt="<?= esc($item['titulo']) ?>"
                        >

                        <span class="recomendado-titulo">
                            <?= esc($item['titulo']) ?>
                        </span>
                    </a>
                <?php endforeach; ?>

                <p
                    class="recomendados-vazio"
                    id="recomendadosVazio"
                    hidden
                >
                    Nenhum livro encontrado nesse gênero.
                </p>
            </div>
        </div>
    </div>
</section>

<?= $this->include('partials/modal_perfil') ?>
<?= $this->include('partials/modal_perfil_script') ?>

<?= $this->include('partials/modal_contato') ?>

<?= $this->include('partials/footer') ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 30);
    });

    const pontos = document.querySelectorAll('.detalhes-capa-ponto');
    const imagens = document.querySelectorAll('.detalhes-capa-img');
    pontos.forEach(ponto => {
        ponto.addEventListener('click', () => {
            const indice = ponto.dataset.indice;
            pontos.forEach(p => p.classList.toggle('ativo', p.dataset.indice === indice));
            imagens.forEach(img => img.classList.toggle('is-ativa', img.dataset.indice === indice));
        });
    });

    const precoCards = document.querySelectorAll('.detalhes-card-preco.is-selecionavel');
    precoCards.forEach(card => {
        card.addEventListener('click', () => {
            precoCards.forEach(c => c.classList.remove('is-ativo'));
            card.classList.add('is-ativo');
        });
    });

    const btnConversar = document.getElementById('btnConversarDono');
    const modalContato = document.getElementById('modalContato');
    const fecharContato = document.getElementById('fecharModalContato');

    if (btnConversar && modalContato) {
        btnConversar.addEventListener('click', () => {
            modalContato.classList.add('aberto');
        });
    }

    if (fecharContato && modalContato) {
        fecharContato.addEventListener('click', () => {
            modalContato.classList.remove('aberto');
        });
    }

    if (modalContato) {
        modalContato.addEventListener('click', (e) => {
            if (e.target === modalContato) {
                modalContato.classList.remove('aberto');
            }
        });
    }

    const filtroGenero = document.getElementById('filtroGenero');
    if (filtroGenero) {
        const botoes = filtroGenero.querySelectorAll('.filtro-pill');
        const cards = document.querySelectorAll('#gradeRecomendados .recomendado-card');
        const vazio = document.getElementById('recomendadosVazio');

        filtroGenero.addEventListener('click', (e) => {
            const botao = e.target.closest('.filtro-pill');
            if (!botao) return;

            botoes.forEach(b => b.classList.toggle('ativo', b === botao));

            const genero = botao.dataset.genero;
            let visiveis = 0;

            cards.forEach(card => {
                const mostrar = genero === 'todos' || card.dataset.genero === genero;
                card.style.display = mostrar ? '' : 'none';
                if (mostrar) visiveis++;
            });

            vazio.hidden = visiveis > 0;
        });
    }
});

const btnFavoritar = document.querySelector('.btn-favoritar');

if (btnFavoritar) {
    btnFavoritar.addEventListener('click', async () => {
        const idLivro = btnFavoritar.dataset.idLivro;

        try {
            const resposta = await fetch(`<?= base_url('livro/favoritar') ?>/${idLivro}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>' 
                }
            });

            if (resposta.status === 401) {
                window.location.href = '<?= base_url('login') ?>';
                return;
            }

            const dados = await resposta.json();

            if (dados.sucesso) {
                btnFavoritar.classList.toggle('favoritado', dados.favoritado);
            }
        } catch (erro) {
            console.error('Erro ao favoritar:', erro);
        }
    });
}
</script>

</body>
</html>