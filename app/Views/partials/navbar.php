<?php
    $usuarioLogado = (bool) session()->get('usuario_logado');
    $uri = service('uri');
    $carteirinha = $uri->getPath() == '/index.php/usuarios/perfil';
?>

<div class="nav-wrap">
    <nav class="navbar" id="navbar">
        <a href="<?= base_url('/') ?>" class="nav-logo">
            Clube do Livro
        </a>

        <button type="button" class="nav-toggle" id="navToggle" aria-label="Abrir menu" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>

        <?php if ($usuarioLogado): ?>

            <div class="nav-links" id="navLinks">
                <?php if (!$carteirinha): ?>
                    <a href="<?= base_url('usuarios/perfil') ?>">Visualizar Carteirinha</a>
                <?php endif; ?>
                <a href="javascript:void(0)" class="js-abrir-perfil">Alterar Perfil</a>
                <a href="<?= base_url('usuarios/historico') ?>">Visualizar Histórico</a>
                <a href="<?= base_url('usuarios/favoritos') ?>">Visualizar Favoritos</a>
            </div>

            <form action="<?= base_url('usuarios/logout') ?>" method="post" class="nav-actions">
                <button type="submit" class="btn btn-outline btn-sm nav-sair">
                    <i class="fa-solid fa-right-from-bracket"></i> Sair
                </button>
            </form>

        <?php else: ?>

            <div class="nav-links" id="navLinks">
                <a href="#categorias">Categorias</a>
                <a href="#recentes">Agora na estante</a>
                <a href="#como-funciona">Como funciona</a>
            </div>

            <div class="nav-actions">
                <a href="#" class="login-link" data-auth-open="login">Login</a>
                <a href="#" class="btn btn-primary" data-auth-open="cadastro">Cadastrar</a>
            </div>

        <?php endif; ?>
    </nav>
</div>

<script>
    (function () {
        const toggle = document.getElementById('navToggle');
        const links = document.getElementById('navLinks');

        if (toggle && links) {
            toggle.addEventListener('click', function () {
                const aberto = links.classList.toggle('aberto');
                toggle.setAttribute('aria-expanded', aberto);
            });
        }
    })();
</script>