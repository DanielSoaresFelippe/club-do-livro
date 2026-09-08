<?= $this->include('partials/navbar') ?>

<header class="header-banner">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="<?= assetUrl('assets/styles/livro_meus.css') ?>">
    <link rel="stylesheet" href="<?= assetUrl('assets/styles/home.css') ?>">
    <link rel="stylesheet" href="<?= assetUrl('assets/styles/carteirinha.css') ?>">
    <div class="header-banner__overlay"></div>
</header>

<main class="meus-livros">

    <div class="meus-livros__topo">
        <h2>Meus Livros</h2>
        <p><?= count($livros ?? []) ?> livro(s) cadastrado(s)</p>
    </div>

    <div class="meus-livros__grid">

        <?php if (!empty($livros)): ?>

            <?php foreach ($livros as $livro): ?>
                <div class="livro-card">
                    <span class="livro-card__status livro-card__status--<?= esc($livro['tipo_transacao'] ?? 'venda') ?>">
                        <?= esc(ucfirst($livro['tipo_transacao'] ?? 'Venda')) ?>
                    </span>

                    <div class="livro-card__capa">
                        <img
                            src="<?= !empty($livro['imagem_capa']) ? $livro['imagem_capa'] : base_url('assets/img/capa_padrao.png') ?>"
                            alt="Capa de <?= esc($livro['titulo']) ?>">
                    </div>

                    <div class="livro-card__info">
                        <h3><?= esc($livro['titulo']) ?></h3>
                        <p class="livro-card__autor"><?= esc($livro['autor'] ?? 'Autor não informado') ?></p>
                        <p class="livro-card__genero"><?= esc($livro['genero'] ?? '') ?></p>

                        <div class="livro-card__acoes">
                            <a href="<?= site_url('livro/editar/' . $livro['id_livro']) ?>" class="btn-icone" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button type="button" class="btn-icone btn-icone--excluir" title="Excluir"
                                data-id="<?= esc($livro['id_livro']) ?>">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>

        <?php else: ?>

            <div class="meus-livros__vazio">
                <p>Você ainda não cadastrou nenhum livro.</p>
                <p>Clique no botão abaixo para adicionar o primeiro!</p>
            </div>

        <?php endif; ?>

    </div>

</main>

<a href="<?= base_url('/novo') ?>" id="btnAdicionarLivro" class="fab-bloop" title="Adicionar novo livro">
    <span class="fab-bloop__mais">+</span>
</a>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-icone--excluir').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const idLivro = btn.dataset.id;

                if (!confirm('Deseja realmente excluir este livro?')) return;

                fetch(`${window.location.origin}/livro/excluir/${idLivro}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        window.location.reload();
                    } else {
                        alert('Não foi possível excluir o livro.');
                    }
                })
                .catch(error => {
                    console.error('Erro ao excluir:', error);
                    alert('Ocorreu um erro ao excluir o livro.');
                });
            });
        });
    });
</script>

<?= $this->include('partials/footer') ?>