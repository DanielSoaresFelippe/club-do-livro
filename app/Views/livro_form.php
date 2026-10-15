<?= $this->include('partials/navbar') ?>

<header class="header-banner">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Prop+Double+Ink:wght@100..900&family=Fleur+De+Leah&family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= assetUrl('assets/styles/livro_form.css') ?>">
    <link rel="stylesheet" href="<?= assetUrl('assets/styles/home.css') ?>">
    <link rel="stylesheet" href="<?= assetUrl('assets/styles/carteirinha.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Bitcount+Prop+Double+Ink:wght@100..900&family=Fleur+De+Leah&family=Pixelify+Sans:wght@400..700&family=Puppies+Play&display=swap" rel="stylesheet">
    <div class="header-banner__overlay"></div>
</header>

<main class="livro-form-page">
    <div class="folha-formulario">     
        <div class="livro-form-page__voltar">
            <a href="<?= site_url('livro/meus-livros') ?>">&larr; Voltar para meus livros</a>
        </div>
        <h2><?= isset($livro) ? 'Editar livro' : 'Adicionar novo livro' ?></h2>

        <?php if (session('errors')): ?>
            <div class="form-livro__erros">
                <ul>
                    <?php foreach (session('errors') as $erro): ?>
                        <li><?= esc($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (session('erro')): ?>
            <div class="form-livro__erros">
                <p><?= esc(session('erro')) ?></p>
            </div>
        <?php endif; ?>

        <?= form_open_multipart(
            isset($livro) ? 'livro/atualizar/' . $livro['id_livro'] : 'livro/salvar',
            ['class' => 'form-livro']
        ) ?>

            <div class="form-livro__grupo">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo"
                    value="<?= old('titulo', $livro['titulo'] ?? '') ?>" required>
            </div>

            <div class="form-livro__linha">
                <div class="form-livro__grupo">
                    <label for="autor">Autor</label>
                    <input type="text" name="autor" id="autor"
                        value="<?= old('autor', $livro['autor'] ?? '') ?>">
                </div>
                <div class="form-livro__grupo">
                    <label for="editora">Editora</label>
                    <input type="text" name="editora" id="editora"
                        value="<?= old('editora', $livro['editora'] ?? '') ?>">
                </div>
            </div>

            <div class="form-livro__linha">
                <div class="form-livro__grupo">
                    <label for="id_genero">Gênero</label>
                    <select name="id_genero" id="id_genero" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($generos ?? [] as $genero): ?>
                            <option value="<?= esc($genero['id_genero']) ?>"
                                <?= (old('id_genero', $livro['id_genero'] ?? '') == $genero['id_genero']) ? 'selected' : '' ?>>
                                <?= esc($genero['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-livro__grupo">
                    <label for="ano_publicacao">Ano de publicação</label>
                    <input type="number" name="ano_publicacao" id="ano_publicacao"
                        min="1000" max="<?= date('Y') ?>"
                        value="<?= old('ano_publicacao', $livro['ano_publicacao'] ?? '') ?>"
                        oninput="if (this.value.length > 4) this.value = this.value.slice(0, 4);">
                </div>
            </div>

            <div class="form-livro__linha">
                <div class="form-livro__grupo">
                    <label for="estado_conservacao">Estado de conservação</label>
                    <select name="estado_conservacao" id="estado_conservacao" required>
                        <?php
                        $estadoAtual = old('estado_conservacao', $livro['estado_conservacao'] ?? '');
                        $estados = [
                            'novo'       => 'Novo',
                            'seminovo'   => 'Seminovo',
                            'usado'      => 'Usado',
                            'desgastado' => 'Desgastado',
                        ];
                        ?>
                        <option value="">Selecione...</option>
                        <?php foreach ($estados as $valor => $rotulo): ?>
                            <option value="<?= $valor ?>" <?= $estadoAtual === $valor ? 'selected' : '' ?>>
                                <?= $rotulo ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-livro__grupo">
                    <label for="tipo_transacao">Forma de negociação</label>
                    <?php
                    $tipoAtual = old('tipo_transacao', $livro['tipo_transacao'] ?? 'ambos');
                    $tipos = [
                        'venda' => 'Apenas venda (dinheiro)',
                        'troca' => 'Apenas troca',
                        'ambos' => 'Ambos',
                    ];
                    ?>
                    <select name="tipo_transacao" id="tipo_transacao" required>
                        <?php foreach ($tipos as $valor => $rotulo): ?>
                            <option value="<?= $valor ?>" <?= $tipoAtual === $valor ? 'selected' : '' ?>>
                                <?= $rotulo ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-livro__grupo" id="grupoPreco">
                <label for="preco">Preço (R$)</label>
                <input type="number" name="preco" id="preco" step="0.01" min="0"
                    value="<?= old('preco', $livro['preco'] ?? '') ?>">
            </div>

            <fieldset class="form-livro__endereco">
                <legend>Onde o livro está</legend>

                <div class="form-livro__grupo">
                    <label for="cep">CEP</label>
                    <input type="text" name="cep" id="cep" maxlength="9" inputmode="numeric"
                        placeholder="00000-000" autocomplete="postal-code"
                        value="<?= esc(old('cep', $livro['cep'] ?? '')) ?>" required>
                    <span id="cepStatus" class="form-livro__cep-status" aria-live="polite"></span>
                </div>
                <div class="form-livro__grupo">
                    <label for="bairro">Bairro</label>
                    <input type="text" name="bairro" id="bairro" maxlength="100"
                        value="<?= esc(old('bairro', $livro['bairro'] ?? '')) ?>">
                </div>
                <div class="form-livro__grupo">
                    <label for="cidade">Cidade</label>
                    <input type="text" name="cidade" id="cidade" maxlength="100"
                        value="<?= esc(old('cidade', $livro['cidade'] ?? '')) ?>" required>
                </div>
                <div class="form-livro__grupo">
                    <label for="estado">UF</label>
                    <input type="text" name="estado" id="estado" maxlength="2"
                        pattern="[A-Za-z]{2}" title="Sigla do estado com 2 letras, ex: MG"
                        value="<?= esc(old('estado', $livro['estado'] ?? '')) ?>" required>
                </div>
            </fieldset>

            <div class="form-livro__grupo">
                <label for="descricao">Descrição</label>
                <textarea name="descricao" id="descricao" rows="4"><?= old('descricao', $livro['descricao'] ?? '') ?></textarea>
            </div>

            <div class="form-livro__grupo">
                <label for="imagem_capa">Capa do livro</label>
                <input type="file" name="imagem_capa" id="imagem_capa"
                    accept="image/jpeg,image/png,image/webp">
                <span class="form-livro__dica">JPG, PNG ou WEBP, até 2 MB.</span>
                <span id="capaStatus" class="form-livro__cep-status" aria-live="polite"></span>

                <?php if (!empty($livro['imagem_capa'])): ?>
                    <div class="form-livro__capa-atual">
                        <img src="<?= esc($livro['imagem_capa']) ?>" alt="Capa atual">
                        <span>Capa atual — envie outra imagem para substituir</span>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-salvar-livro">
                <?= isset($livro) ? 'Salvar alterações' : 'Cadastrar livro' ?>
            </button>

        <?= form_close() ?>

    </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoSelect = document.getElementById('tipo_transacao');
        const grupoPreco = document.getElementById('grupoPreco');

        function alternarPreco() {
            grupoPreco.style.display = tipoSelect.value === 'troca' ? 'none' : 'flex';
        }

        tipoSelect.addEventListener('change', alternarPreco);
        alternarPreco();

        const cepInput = document.getElementById('cep');
        const cepStatus = document.getElementById('cepStatus');
        const campos = {
            bairro: document.getElementById('bairro'),
            cidade: document.getElementById('cidade'),
            estado: document.getElementById('estado'),
        };
        let ultimoCepBuscado = cepInput.value.replace(/\D/g, '');

        function buscarCep() {
            const cepLimpo = cepInput.value.replace(/\D/g, '');

            if (cepLimpo.length !== 8 || cepLimpo === ultimoCepBuscado) {
                return;
            }

            ultimoCepBuscado = cepLimpo;
            cepStatus.textContent = 'Buscando endereço...';

            fetch('https://viacep.com.br/ws/' + cepLimpo + '/json/')
                .then(function (resposta) {
                    if (!resposta.ok) {
                        throw new Error('ViaCEP indisponível');
                    }
                    return resposta.json();
                })
                .then(function (dados) {
                    if (cepLimpo !== cepInput.value.replace(/\D/g, '')) {
                        return; // o usuário já digitou outro CEP
                    }

                    if (dados.erro) {
                        cepStatus.textContent = 'CEP não encontrado. Preencha cidade e UF manualmente.';
                        return;
                    }

                    campos.bairro.value = dados.bairro || '';
                    campos.cidade.value = dados.localidade || '';
                    campos.estado.value = dados.uf || '';
                    cepStatus.textContent = dados.bairro ? '' : 'CEP geral da cidade — informe o bairro, se quiser.';
                })
                .catch(function () {
                    ultimoCepBuscado = '';
                    cepStatus.textContent = 'Não foi possível buscar o CEP agora. Preencha manualmente.';
                });
        }

        cepInput.addEventListener('input', function () {
            let valor = cepInput.value.replace(/\D/g, '').slice(0, 8);
            if (valor.length > 5) {
                valor = valor.slice(0, 5) + '-' + valor.slice(5);
            }
            cepInput.value = valor;
            buscarCep();
        });

        cepInput.addEventListener('blur', buscarCep);

        campos.estado.addEventListener('input', function () {
            campos.estado.value = campos.estado.value.replace(/[^a-z]/gi, '').toUpperCase();
        });

        const capaInput = document.getElementById('imagem_capa');
        const capaStatus = document.getElementById('capaStatus');
        const tiposCapa = ['image/jpeg', 'image/png', 'image/webp'];

        capaInput.addEventListener('change', function () {
            const arquivo = capaInput.files[0];
            capaStatus.textContent = '';

            if (!arquivo) {
                return;
            }

            if (!tiposCapa.includes(arquivo.type)) {
                capaStatus.textContent = 'A capa deve ser JPG, PNG ou WEBP.';
                capaInput.value = '';
            } else if (arquivo.size > 2 * 1024 * 1024) {
                capaStatus.textContent = 'A capa deve ter no máximo 2 MB.';
                capaInput.value = '';
            }
        });
    });
</script>

<?= $this->include('partials/footer') ?>