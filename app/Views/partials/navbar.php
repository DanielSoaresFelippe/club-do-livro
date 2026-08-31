<div class="nav-wrap">
    <nav class="navbar" id="navbar">
        <a href="<?= base_url() ?>" class="nav-logo">
            Clube do Livro
        </a>
        <div class="nav-links">
            <a href="javascript:void(0)" class="js-abrir-perfil">Alterar Perfil</a>
            <a href="<?= base_url('usuarios/historico') ?>">Visualizar Histórico</a>
            <a href="<?= base_url('usuarios/favoritos') ?>">Visualizar Favoritos</a>
        </div>
        <form action="<?= base_url('usuarios/logout') ?>" method="post" class="nav-actions">
            <button type="submit" class="btn btn-outline btn-sm nav-sair">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
            </button>
        </form>
    </nav>
</div>