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

<div class="carteirinha-area">
    <button type="button" class="pasta pasta-perfil" id="btnAbrirPerfil">
        <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#e05c7a"/>
        </svg>
        <span>Meu Perfil</span>
    </button>
    
    <button type="button" class="pasta pasta-site" id="btnAbrirSite">
        <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#ffdf77"/>
        </svg>
        <span>Acessar o site</span>
    </button>

    <button type="button" class="pasta pasta-historico" id="btnHistorico">
        <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#8fb84a"/>
        </svg>
        <span>Historico de Livros</span>
    </button>

    <button type="button" class="pasta pasta-favoritos" id="btnFavoritos">
        <svg viewBox="0 0 74 58" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 12 C2 8 5 6 9 6 H28 L34 12 H65 C69 12 72 15 72 19 V50 C72 54 69 56 65 56 H9 C5 56 2 54 2 50 Z" fill="#8a2f2f"/>
        </svg>
        <span>Favoritos</span>
    </button>

    <div class="cartao">
        <div class="certificado-wrap">
            <img class="certificado-bg" src="<?= base_url('assets/img/certificadoLeitor.png') ?>" alt="Certificado de amante de livros">

            <div class="foto-perfil-usuario">
                <img src="<?= $usuario['foto_perfil'] ? base_url('uploads/perfil/' . $usuario['foto_perfil']) : base_url('assets/img/avatar-padrao.png') ?>" alt="Foto de <?= esc($usuario['nome']) ?>">
            </div>

            <div class="campo-nome"><?= esc($usuario['nome']) ?></div>
            <div class="campo-email"><?= esc($usuario['email']) ?></div>
        </div>

        <div class="nome-usuario"><?= esc($usuario['nome']) ?></div>
    </div>
</div>

<div class="modal-overlay" id="modalPerfil">
    <div class="modal-perfil">
        <button type="button" class="modal-fechar" id="fecharModalPerfil">&times;</button>
        <h2>Meu perfil</h2>

        <img id="modalFotoPreview" class="modal-foto-preview" src="" alt="Foto de perfil">

        <form id="formPerfil" enctype="multipart/form-data">
            <label for="perfilNome">Nome</label>
            <input type="text" id="perfilNome" name="nome" required>
            <div class="modal-erro" data-erro-de="nome"></div>

            <label for="perfilEmail">E-mail</label>
            <input type="email" id="perfilEmail" name="email" required>
            <div class="modal-erro" data-erro-de="email"></div>

            <label for="perfilTelefone">Telefone</label>
            <input type="text" id="perfilTelefone" name="telefone">
            <div class="modal-erro" data-erro-de="telefone"></div>

            <label for="perfilSenha">Nova senha (deixe em branco para manter a atual)</label>
            <input type="password" id="perfilSenha" name="senha">
            <div class="modal-erro" data-erro-de="senha"></div>

            <label for="perfilFoto">Trocar foto de perfil</label>
            <input type="file" id="perfilFoto" name="foto_perfil" accept="image/jpeg,image/png,image/webp">
            <div class="modal-erro" data-erro-de="foto_perfil"></div>

            <button type="submit" class="modal-salvar">Salvar alterações</button>
            <div class="modal-msg-sucesso" id="perfilMsgSucesso">Dados atualizados com sucesso!</div>
        </form>
    </div>
</div>

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
        document.getElementById('perfilTelefone').value = dados.usuario.telefone ?? '';
        document.getElementById('perfilSenha').value = '';
        modalFotoPreview.src = dados.usuario.foto_url ?? (urlBase + 'assets/img/avatar-padrao.png');

        modalPerfil.classList.add('aberto');
    } catch (erro) {
        console.error('Erro ao carregar perfil', erro);
    }
});

btnFecharPerfil.addEventListener('click', () => modalPerfil.classList.remove('aberto'));
modalPerfil.addEventListener('click', (evento) => {
    if (evento.target === modalPerfil) modalPerfil.classList.remove('aberto');
});

document.getElementById('perfilFoto').addEventListener('change', (evento) => {
    const arquivo = evento.target.files[0];
    if (arquivo) modalFotoPreview.src = URL.createObjectURL(arquivo);
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

function mostrarErros(erros) {
    Object.keys(erros).forEach((campo) => {
        const alvo = document.querySelector('[data-erro-de="' + campo + '"]');
        if (alvo) alvo.textContent = erros[campo];
    });
}
function limparErros() {
    document.querySelectorAll('.modal-erro').forEach(el => el.textContent = '');
}

document.getElementById('btnHistorico').addEventListener('click', () => {
    window.location.href = urlBase + 'usuarios/historico';
});
document.getElementById('btnFavoritos').addEventListener('click', () => {
    window.location.href = urlBase + 'usuarios/favoritos';
});
document.getElementById('btnAbrirSite').addEventListener('click', () => {
    window.location.href = urlBase;
});
</script>

</body>
</html>