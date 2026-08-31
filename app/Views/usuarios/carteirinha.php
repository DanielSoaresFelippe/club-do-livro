<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Minha carteirinha - Clube do Livro</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="<?= assetUrl('assets/styles/carteirinha.css') ?>">
</head>
<body>

<?= $this->include('partials/navbar') ?>

<header class="perfil-hero" id="top">
    <img class="flor flor-esquerda dado" src='<?= base_url('assets/img/bybooksDados.png'); ?>'>
    <img class="flor flor-direita relicario" src='<?= base_url('assets/img/bookLoversRelicario.png'); ?>'>

    <div class="perfil-hero-inner">
        <span class="eyebrow">minha conta</span>
        <h1>Olá, <?= esc($usuario['nome']) ?>!</h1>
        <h2>Acompanhe seu perfil, seu histórico e os livros que você favoritou.</h2>
    </div>
    <div class="ribbon">
      <div class="ribbon-track">
        <span>Cada livro trocado ganha uma nova história</span>
        <span>Ler é multiplicar histórias</span>
        <span>Seu livro merece outra estante</span>
        <span>Nenhuma história termina, ela só muda de dono</span>
        <span>Cada livro trocado ganha uma nova história</span>
        <span>Ler é multiplicar histórias</span>
        <span>Seu livro merece outra estante</span>
        <span>Nenhuma história termina, ela só muda de dono</span>
      </div>
    </div>
</header>

<section class="carteirinha-section">
    <div class="carteirinha-area">
        <div class="cartao">
            <div class="certificado-wrap">
                <img class="certificado-bg" src="<?= base_url('assets/img/certificadoLeitor.png') ?>" alt="Certificado de amante de livros">

                <div class="foto-perfil-usuario">
                    <img src="<?= $usuario['foto_perfil'] ? base_url('uploads/perfil/' . $usuario['foto_perfil']) : base_url('assets/img/avatar-padrao.png') ?>" alt="Foto de <?= esc($usuario['nome']) ?>">
                </div>

                <div class="campo-nome"><?= esc($usuario['nome']) ?></div>
                <div class="campo-email"><?= esc($usuario['email']) ?></div>
            </div>
        </div>

        <div class="pastas-linha">
            <button type="button" class="pasta js-abrir-perfil" data-pasta="perfil">
                <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#e05c7a"/>
                </svg>
                <span>Meu Perfil</span>
            </button>

            <a href="<?= base_url('usuarios/historico') ?>" class="pasta" data-pasta="historico">
                <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#8fb84a"/>
                </svg>
                <span>Histórico de Livros</span>
            </a>

            <a href="<?= base_url('usuarios/favoritos') ?>" class="pasta" data-pasta="favoritos">
                <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#8a2f2f"/>
                </svg>
                <span>Favoritos</span>
            </a>

            <a href="<?= base_url() ?>" class="pasta" data-pasta="site">
                <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#ffdf77"/>
                </svg>
                <span>Acessar o site</span>
            </a>
        </div>
    </div>
</section>

<?= $this->include('partials/modal_perfil') ?>

<?= $this->include('partials/footer') ?>

<?= $this->include('partials/modal_perfil_script') ?>

<script>
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 30);
});
</script>

</body>
</html>