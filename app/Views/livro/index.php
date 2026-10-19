<?= $this->include('partials/navbar') ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Clube do Livro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="<?= assetUrl('assets/styles/carteirinha.css') ?>">
</head>
<body class="body-index">
<section class="livro-hero">
    <div class="livro-hero__overlay"></div>
</section>

<section class="livro-filtros">
    <form method="get" action="<?= base_url('livro') ?>" class="livro-filtros__form" id="formFiltros" role="search">
        <div class="campo">
            <label for="busca">Buscar</label>
            <input type="search" name="busca" id="busca"
                placeholder="Título, autor ou gênero..."
                autocomplete="off"
                value="<?= esc($buscaSelecionada ?? '') ?>">
        </div>

        <div class="campo">
            <label for="tipo_transacao">Tipo</label>
            <select name="tipo_transacao" id="tipo_transacao">
                <option value="">Todos</option>
                <option value="venda" <?= $tipoSelecionado === 'venda' ? 'selected' : '' ?>>Venda</option>
                <option value="troca" <?= $tipoSelecionado === 'troca' ? 'selected' : '' ?>>Troca</option>
                <option value="ambos" <?= $tipoSelecionado === 'ambos' ? 'selected' : '' ?>>Venda ou troca</option>
            </select>
        </div>

        <div class="campo">
            <label for="genero">Gênero</label>
            <select name="genero" id="genero">
                <option value="">Todos os gêneros</option>
                <?php foreach ($generos as $genero): ?>
                    <option value="<?= $genero['id_genero'] ?>"
                        <?= (int) $generoSelecionado === (int) $genero['id_genero'] ? 'selected' : '' ?>>
                        <?= esc($genero['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="localizacao">Localização</label>
            <select name="localizacao" id="localizacao">
                <option value="">Todas as localizações</option>
                <?php foreach ($localizacoes as $loc): ?>
                    <option value="<?= esc($loc['localizacao']) ?>"
                        <?= $localizacaoSelecionada === $loc['localizacao'] ? 'selected' : '' ?>>
                        <?= esc($loc['localizacao']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <a href="<?= base_url('livro') ?>" class="livro-filtros__limpar" id="limparFiltros"
            <?= ($generoSelecionado || $tipoSelecionado || $localizacaoSelecionada || $buscaSelecionada) ? '' : 'hidden' ?>>Limpar filtros</a>
    </form>
</section>

<section class="livro-grid" id="gradeLivros" aria-live="polite" aria-busy="false">
    <?= $this->include('livro/_grade') ?>
</section>

<script>
/*
 * Busca dinâmica do catálogo: atualiza a grade sem recarregar a página.
 * - texto: espera o usuário parar de digitar (debounce) antes de buscar
 * - selects: buscam na hora
 * - paginação: os links da grade também são carregados por fetch
 * Sem JavaScript, o formulário continua funcionando como GET normal.
 */
(function () {
    const ESPERA_DIGITACAO_MS = 350;

    const form = document.getElementById('formFiltros');
    const busca = document.getElementById('busca');
    const grade = document.getElementById('gradeLivros');
    const limpar = document.getElementById('limparFiltros');
    const urlCatalogo = form.getAttribute('action');

    let timer = null;
    let requisicaoAtual = null;

    function urlDosFiltros() {
        const params = new URLSearchParams();
        new FormData(form).forEach((valor, campo) => {
            valor = String(valor).trim();
            if (valor !== '') params.set(campo, valor);
        });
        const query = params.toString();
        return urlCatalogo + (query ? '?' + query : '');
    }

    function temFiltroAtivo() {
        return [...new FormData(form).values()].some(v => String(v).trim() !== '');
    }

    async function carregar(url, { empilharHistorico = false, rolarParaGrade = false } = {}) {
        // Cancela a busca anterior: só a resposta da última digitação importa
        if (requisicaoAtual) requisicaoAtual.abort();
        requisicaoAtual = new AbortController();

        grade.setAttribute('aria-busy', 'true');
        grade.classList.add('is-carregando');

        try {
            const resposta = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                signal: requisicaoAtual.signal,
            });
            if (!resposta.ok) throw new Error('HTTP ' + resposta.status);

            const dados = await resposta.json();
            grade.innerHTML = dados.html;

            const metodo = empilharHistorico ? 'pushState' : 'replaceState';
            history[metodo]({ catalogo: true }, '', url);
            limpar.hidden = !temFiltroAtivo();

            if (rolarParaGrade) grade.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (erro) {
            if (erro.name === 'AbortError') return;
            window.location.href = url; // falhou o fetch: cai no carregamento normal
        } finally {
            grade.setAttribute('aria-busy', 'false');
            grade.classList.remove('is-carregando');
        }
    }

    busca.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => carregar(urlDosFiltros()), ESPERA_DIGITACAO_MS);
    });

    form.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', () => carregar(urlDosFiltros()));
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        clearTimeout(timer);
        carregar(urlDosFiltros());
    });

    limpar.addEventListener('click', (e) => {
        e.preventDefault();
        form.reset();
        form.querySelectorAll('input, select').forEach(campo => { campo.value = ''; });
        carregar(urlCatalogo);
    });

    // Paginação por fetch (links gerados pelo Pager do CodeIgniter)
    grade.addEventListener('click', (e) => {
        const link = e.target.closest('.livro-paginacao a');
        if (!link) return;
        e.preventDefault();
        carregar(link.href, { empilharHistorico: true, rolarParaGrade: true });
    });

    // Voltar/avançar do navegador entre páginas
    window.addEventListener('popstate', () => {
        const params = new URLSearchParams(location.search);
        form.querySelectorAll('input, select').forEach(campo => { campo.value = params.get(campo.name) ?? ''; });
        carregar(location.href);
    });
})();
</script>
</body>
</html>
<?= $this->include('partials/footer') ?>
<?= $this->include('partials/modal_perfil') ?>
<?= $this->include('partials/modal_perfil_script') ?>
