<script>
const urlBase = "<?= base_url() ?>";

const modalPerfil      = document.getElementById('modalPerfil');
const btnsAbrirPerfil  = document.querySelectorAll('.js-abrir-perfil');
const btnFecharPerfil  = document.getElementById('fecharModalPerfil');
const formPerfil       = document.getElementById('formPerfil');
const modalFotoPreview = document.getElementById('modalFotoPreview');
const msgSucesso       = document.getElementById('perfilMsgSucesso');

async function abrirModalPerfil() {
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
}

btnsAbrirPerfil.forEach((botao) => botao.addEventListener('click', abrirModalPerfil));

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
        document.querySelectorAll('.campo-email').forEach(el => el.textContent = resultado.usuario.email);
        if (resultado.usuario.foto_url) {
            document.querySelectorAll('.foto-perfil-usuario img').forEach(el => el.src = resultado.usuario.foto_url);
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
</script>