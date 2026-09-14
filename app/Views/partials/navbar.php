<?php
    $usuarioLogado = (bool) session()->get('usuario_logado');
    $uri = service('uri');
    $carteirinha = $uri->getPath() == '/index.php/usuarios/perfil';
    $usuarioTipo   = session()->get('usuario_tipo');
    $ehColaborador = $usuarioTipo === 'colaborador';
?>

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="<?= assetUrl('assets/styles/carteirinha.css') ?>">
</head>

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
                <?php if ($ehColaborador): ?>
                    <a href="<?= base_url('usuarios/perfil') ?>">Visualizar Carteirinha</a>
                    <a href="<?= base_url('livro/meus-livros') ?>">Meus Livros</a>
                <?php else: ?>
                    <a href="<?= base_url('usuarios/perfil') ?>">Visualizar Carteirinha</a>
                <?php endif; ?>
                <a href="<?= base_url('livro') ?>">Ver Catálogo</a>
                <a href="javascript:void(0)" class="js-abrir-perfil">Alterar Perfil</a>
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