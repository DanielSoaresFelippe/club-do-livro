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

<div class="nav-wrap">
    <nav class="navbar" id="navbar">
        <a href="<?= base_url() ?>" class="nav-logo">
            Clube do Livro
        </a>
         <div class="nav-links">
            <a id="btnAbrirPerfil">Alterar Perfil</a>
            <a id="navHistorico">Visualizar Histórico</a>
            <a id="navFavoritos">Visualizar Favoritos</a>
        </div>
        <form action="<?= base_url('usuarios/logout') ?>" method="post" class="nav-actions">
            <button type="submit" class="btn btn-outline btn-sm nav-sair">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
            </button>
        </form>
    </nav>
</div>

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
            <button type="button" class="pasta" id="btnAbrirPerfil" data-pasta="perfil">
                <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#e05c7a"/>
                </svg>
                <span>Meu Perfil</span>
            </button>

            <button type="button" class="pasta" id="btnHistorico" data-pasta="historico">
                <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#8fb84a"/>
                </svg>
                <span>Histórico de Livros</span>
            </button>

            <button type="button" class="pasta" id="btnFavoritos" data-pasta="favoritos">
                <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#8a2f2f"/>
                </svg>
                <span>Favoritos</span>
            </button>

            <button type="button" class="pasta" id="btnAbrirSite" data-pasta="site">
                <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#ffdf77"/>
                </svg>
                <span>Acessar o site</span>
            </button>
        </div>
    </div>
</section>

<div class="nuvem-divisor nuvem-divisor--historico divisor-cabecalho divisor-ativo" data-divisor="historico">
    <svg viewBox="0 0 1440 90" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path fill="var(--blue-historico)" d="
            M0,90
            Q60,10 120,90
            Q180,10 240,90
            Q300,10 360,90
            Q420,10 480,90
            Q540,10 600,90
            Q660,10 720,90
            Q780,10 840,90
            Q900,10 960,90
            Q1020,10 1080,90
            Q1140,10 1200,90
            Q1260,10 1320,90
            Q1380,10 1440,90
            L1440,90 Z"/>
    </svg>
</div>

<div class="nuvem-divisor nuvem-divisor--favoritos divisor-cabecalho" data-divisor="favoritos">
    <svg viewBox="0 0 1440 90" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path fill="#fff9eb" d="
            M0,90
            Q60,10 120,90
            Q180,10 240,90
            Q300,10 360,90
            Q420,10 480,90
            Q540,10 600,90
            Q660,10 720,90
            Q780,10 840,90
            Q900,10 960,90
            Q1020,10 1080,90
            Q1140,10 1200,90
            Q1260,10 1320,90
            Q1380,10 1440,90
            L1440,90 Z"/>
    </svg>
</div>

<section class="conteudo-pastas" id="conteudoPastas">
    <div class="painel-pasta painel-ativo" data-painel="historico">
        <div class="painel-conteudo">
            <?= $this->include('usuarios/historico') ?>
        </div>
    </div>

    <div class="painel-pasta" data-painel="favoritos">
        <div class="painel-conteudo">
            <?= $this->include('usuarios/favoritos') ?>
        </div>
    </div>
</section>

<div class="modal-overlay" id="modalPerfil">
    <div class="modal-perfil">
        <button type="button" class="modal-fechar" id="fecharModalPerfil">&times;</button>
        <h2>Editar perfil</h2>

        <img id="modalFotoPreview" class="modal-foto-preview" src="" alt="Foto de perfil">

        <span id="btnTrocarFoto" class="modal-foto-btn">
            Trocar foto de perfil
        </span>
            
        <form id="formPerfil" enctype="multipart/form-data">
            <label for="perfilNome">Nome</label>
            <input type="text" id="perfilNome" name="nome" required>
            <div class="modal-erro" data-erro-de="nome"></div>

            <label for="perfilEmail">E-mail</label>
            <input type="email" id="perfilEmail" name="email" required>
            <div class="modal-erro" data-erro-de="email"></div>

            <label for="perfilTelefone">Telefone</label>
            <input type="text" id="perfilTelefone" name="telefone" maxlength="15" inputmode="numeric" placeholder="(00) 00000-0000">
            <div class="modal-erro" data-erro-de="telefone"></div>

            <label for="perfilTipo">Quero</label>
            <select id="perfilTipo" name="tipo" required>
                <option value="cliente">Ler / comprar livros</option>
                <option value="colaborador">Vender / trocar livros</option>
            </select>
            <div class="modal-erro" data-erro-de="tipo"></div>

            <label for="perfilSenha"> Nova senha (deixe em branco para manter a atual) </label>
            <div class="perfil-password-wrap">
                <input type="password" id="perfilSenha" name="senha">
                <button type="button" class="perfil-password-toggle" id="togglePerfilSenha" aria-label="Mostrar senha">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>

            <div class="modal-erro" data-erro-de="senha"></div>

            <input type="file" id="perfilFoto" name="foto_perfil" accept="image/jpeg,image/png,image/webp" hidden>
            <div class="modal-erro" data-erro-de="foto_perfil"></div>

            <button type="submit" class="modal-salvar">Salvar alterações</button>
            <div class="modal-msg-sucesso" id="perfilMsgSucesso">Dados atualizados com sucesso!</div>
        </form>
    </div>
</div>

<div class="nuvem-divisor nuvem-divisor--footer">
    <svg viewBox="0 0 1440 90" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path fill="var(--ink)" d="
            M0,90
            Q60,10 120,90
            Q180,10 240,90
            Q300,10 360,90
            Q420,10 480,90
            Q540,10 600,90
            Q660,10 720,90
            Q780,10 840,90
            Q900,10 960,90
            Q1020,10 1080,90
            Q1140,10 1200,90
            Q1260,10 1320,90
            Q1380,10 1440,90
            L1440,90 Z"/>
    </svg>
</div>

<footer>
    <img class="flor flor-footer-esquerda" src="<?= base_url('assets/img/betterthanthemovies.jpg') ?>">
    <img class="flor flor-footer-direita" src="<?= base_url('assets/img/enemiestolovers.png') ?>">

    <div class="footer-bottom">
        <span>
            © 2026 Clube do Livro. Daniel S., Daniel Q., 
            Gleicekelly, Silmara, Thaise
        </span>
    </div>
</footer>

<script>
const urlBase = "<?= base_url() ?>";

const modalPerfil     = document.getElementById('modalPerfil');
const btnAbrirPerfil  = document.getElementById('btnAbrirPerfil');
const btnFecharPerfil = document.getElementById('fecharModalPerfil');
const formPerfil      = document.getElementById('formPerfil');
const modalFotoPreview = document.getElementById('modalFotoPreview');
const msgSucesso      = document.getElementById('perfilMsgSucesso');

btnAbrirPerfil.addEventListener('click', async () => {
    limparErros();
    try {
        const resposta = await fetch(urlBase + 'usuarios/dados-perfil', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const dados = await resposta.json();

        if (!dados.success) return;

        document.getElementById('perfilNome').value = dados.usuario.nome ?? '';
        document.getElementById('perfilEmail').value = dados.usuario.email ?? '';
        document.getElementById('perfilTelefone').value = mascararTelefone(dados.usuario.telefone ?? '');
        document.getElementById('perfilTipo').value = dados.usuario.tipo ?? 'cliente';
        document.getElementById('perfilSenha').value = '';
        modalFotoPreview.src = dados.usuario.foto_url ?? (urlBase + 'assets/img/avatar-padrao.png');

        modalPerfil.classList.add('aberto');
    } catch (erro) {
        console.error('Erro ao carregar perfil', erro);
    }
});

const perfilSenha = document.getElementById('perfilSenha');
const togglePerfilSenha = document.getElementById('togglePerfilSenha');

togglePerfilSenha.addEventListener('click', () => {
    const mostrando = perfilSenha.type === 'text';

    perfilSenha.type = mostrando ? 'password' : 'text';

    togglePerfilSenha.innerHTML = mostrando
        ? '<i class="fa-solid fa-eye"></i>'
        : '<i class="fa-solid fa-eye-slash"></i>';

    togglePerfilSenha.setAttribute(
        'aria-label',
        mostrando ? 'Mostrar senha' : 'Ocultar senha'
    );
});

btnFecharPerfil.addEventListener('click', () => modalPerfil.classList.remove('aberto'));
modalPerfil.addEventListener('click', (evento) => {
    if (evento.target === modalPerfil) modalPerfil.classList.remove('aberto');
});

document.getElementById('perfilFoto').addEventListener('change', (evento) => {
    const arquivo = evento.target.files[0];
    if (arquivo) modalFotoPreview.src = URL.createObjectURL(arquivo);
});

document.getElementById('btnTrocarFoto').addEventListener('click', () => {
    document.getElementById('perfilFoto').click();
});

formPerfil.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    limparErros();
    msgSucesso.style.display = 'none';

    const dadosForm = new FormData(formPerfil);

    try {
        const resposta = await fetch(urlBase + 'usuarios/atualizar-perfil', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: dadosForm
        });
        const resultado = await resposta.json();

        if (!resultado.success) {
            mostrarErros(resultado.errors || {});
            return;
        }

        msgSucesso.style.display = 'block';

        document.querySelectorAll('.campo-nome, .nome-usuario').forEach(el => el.textContent = resultado.usuario.nome);
        document.querySelector('.campo-email').textContent = resultado.usuario.email;
        if (resultado.usuario.foto_url) {
            document.querySelector('.foto-perfil-usuario img').src = resultado.usuario.foto_url;
        }

        setTimeout(() => modalPerfil.classList.remove('aberto'), 1200);
    } catch (erro) {
        console.error('Erro ao salvar perfil', erro);
    }
});

function mascararTelefone(valor) {
    valor = valor.replace(/\D/g, '').slice(0, 11);

    if (valor.length > 10) {
        valor = valor.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
    } else if (valor.length > 6) {
        valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
    } else if (valor.length > 2) {
        valor = valor.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
    } else if (valor.length > 0) {
        valor = valor.replace(/^(\d*)/, '($1');
    }

    return valor;
}

const perfilTelefone = document.getElementById('perfilTelefone');
perfilTelefone.addEventListener('input', (evento) => {
    evento.target.value = mascararTelefone(evento.target.value);
});

function mostrarErros(erros) {
    Object.keys(erros).forEach((campo) => {
        const alvo = document.querySelector('[data-erro-de="' + campo + '"]');
        if (alvo) alvo.textContent = erros[campo];
    });
}
function limparErros() {
    document.querySelectorAll('.modal-erro').forEach(el => el.textContent = '');
}

document.querySelectorAll('.pasta').forEach((botao) => {
    botao.addEventListener('click', () => {
        const pasta = botao.dataset.pasta;

        if (pasta === 'site') {
            window.location.href = urlBase;
            return;
        }
        if (pasta === 'perfil') {
            return; 
        }

        document.querySelectorAll('.pasta').forEach(p => p.classList.remove('pasta-ativa'));
        botao.classList.add('pasta-ativa');

        document.querySelectorAll('.painel-pasta').forEach(painel => {
            painel.classList.toggle('painel-ativo', painel.dataset.painel === pasta);
        });

        document.getElementById('conteudoPastas').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 30);
});

function ativarPasta(pasta) {
    if (pasta === 'site') {
        window.location.href = urlBase;
        return;
    }
    if (pasta === 'perfil') {
        btnAbrirPerfil.click(); 
        return;
    }

    document.querySelectorAll('.pasta').forEach(p => {
        p.classList.toggle('pasta-ativa', p.dataset.pasta === pasta);
    });

    document.querySelectorAll('.painel-pasta').forEach(painel => {
        painel.classList.toggle('painel-ativo', painel.dataset.painel === pasta);
    });

    document.querySelectorAll('.divisor-cabecalho').forEach(divisor => {
        divisor.classList.toggle('divisor-ativo', divisor.dataset.divisor === pasta);
    });
    
    document.getElementById('conteudoPastas').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

document.querySelectorAll('.pasta').forEach((botao) => {
    botao.addEventListener('click', () => ativarPasta(botao.dataset.pasta));
});

document.getElementById('btnAbrirPerfil').addEventListener('click', () => ativarPasta('perfil'));
document.getElementById('navHistorico').addEventListener('click', () => ativarPasta('historico'));
document.getElementById('navFavoritos').addEventListener('click', () => ativarPasta('favoritos'));

</script>

</body>
</html>