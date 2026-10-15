<?php
/**
 * Modal "Fazer proposta" (lance em dinheiro ou troca).
 *
 * Espera: $livro, $livrosParaTroca
 */
$aceitaDinheiro = $livro['tipo_transacao'] !== 'troca';
$aceitaTroca    = $livro['tipo_transacao'] !== 'venda';
$livrosParaTroca = $livrosParaTroca ?? [];
$errosProposta  = session('errosProposta') ?? [];
$tipoAtual      = old('tipo', $aceitaDinheiro ? 'dinheiro' : 'troca');
$livrosMarcados = array_map('intval', (array) old('livros_oferecidos', []));
?>
<div class="modal-overlay" id="modalProposta">
    <div class="modal-perfil modal-proposta" role="dialog" aria-modal="true" aria-labelledby="tituloModalProposta">
        <button type="button" class="modal-fechar" id="fecharModalProposta" aria-label="Fechar">&times;</button>

        <h2 id="tituloModalProposta">Fazer proposta</h2>
        <p class="modal-proposta__intro">
            Para "<strong><?= esc($livro['titulo']) ?></strong>"
            <?php if ($aceitaDinheiro && !empty($livro['preco'])): ?>
                — preço pedido: <strong>R$ <?= number_format((float) $livro['preco'], 2, ',', '.') ?></strong>
            <?php endif; ?>
        </p>

        <?php if (!empty($errosProposta)): ?>
            <ul class="modal-proposta__erros">
                <?php foreach ($errosProposta as $erro): ?>
                    <li><?= esc($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="<?= site_url('livro/proposta/' . $livro['id_livro']) ?>" method="post" id="formProposta">
            <?= csrf_field() ?>

            <?php if ($aceitaDinheiro && $aceitaTroca): ?>
                <div class="modal-proposta__tipos" role="radiogroup" aria-label="Tipo de oferta">
                    <label class="modal-proposta__tipo">
                        <input type="radio" name="tipo" value="dinheiro" <?= $tipoAtual === 'dinheiro' ? 'checked' : '' ?>>
                        <span><i class="fa-solid fa-coins"></i> Dinheiro</span>
                    </label>
                    <label class="modal-proposta__tipo">
                        <input type="radio" name="tipo" value="troca" <?= $tipoAtual === 'troca' ? 'checked' : '' ?>>
                        <span><i class="fa-solid fa-right-left"></i> Troca</span>
                    </label>
                </div>
            <?php else: ?>
                <input type="hidden" name="tipo" value="<?= $aceitaDinheiro ? 'dinheiro' : 'troca' ?>">
            <?php endif; ?>

            <?php if ($aceitaDinheiro): ?>
                <div class="modal-proposta__secao" data-secao="dinheiro">
                    <label for="valor_oferecido">Quanto você oferece? (R$)</label>
                    <input type="number" name="valor_oferecido" id="valor_oferecido"
                        step="0.01" min="0.01" inputmode="decimal"
                        placeholder="<?= !empty($livro['preco']) ? esc(number_format((float) $livro['preco'], 2, '.', '')) : '0.00' ?>"
                        value="<?= esc(old('valor_oferecido', '')) ?>">
                </div>
            <?php endif; ?>

            <?php if ($aceitaTroca): ?>
                <div class="modal-proposta__secao" data-secao="troca">
                    <p class="modal-proposta__rotulo">Quais livros seus você oferece?</p>

                    <?php if (empty($livrosParaTroca)): ?>
                        <p class="modal-proposta__vazio">
                            Você não tem livros disponíveis para trocar.
                            <?php if (in_array(session('usuario_tipo'), ['colaborador', 'admin'], true)): ?>
                                <a href="<?= site_url('livro/novo') ?>">Cadastrar um livro</a>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                        <div class="modal-proposta__livros">
                            <?php foreach ($livrosParaTroca as $meuLivro): ?>
                                <label class="modal-proposta__livro">
                                    <input type="checkbox" name="livros_oferecidos[]"
                                        value="<?= (int) $meuLivro['id_livro'] ?>"
                                        <?= in_array((int) $meuLivro['id_livro'], $livrosMarcados, true) ? 'checked' : '' ?>>
                                    <img src="<?= esc($meuLivro['imagem_capa'] ?: base_url('assets/img/capa-padrao.png')) ?>" alt="">
                                    <span>
                                        <strong><?= esc($meuLivro['titulo']) ?></strong>
                                        <small><?= esc($meuLivro['autor']) ?></small>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <label for="mensagem">Mensagem para o dono (opcional)</label>
            <textarea name="mensagem" id="mensagem" rows="3" maxlength="1000"
                placeholder="Ex: posso retirar no fim de semana."><?= esc(old('mensagem', '')) ?></textarea>

            <button type="submit" class="modal-proposta__enviar">Enviar proposta</button>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalProposta');
    const abrir = document.getElementById('btnFazerProposta');
    const fechar = document.getElementById('fecharModalProposta');
    const form = document.getElementById('formProposta');

    if (!modal || !form) return;

    const radios = form.querySelectorAll('input[name="tipo"][type="radio"]');
    const secoes = form.querySelectorAll('.modal-proposta__secao');

    function tipoSelecionado() {
        const marcado = form.querySelector('input[name="tipo"]:checked') || form.querySelector('input[name="tipo"]');
        return marcado ? marcado.value : null;
    }

    function atualizarSecoes() {
        const tipo = tipoSelecionado();
        secoes.forEach(secao => {
            const ativa = secao.dataset.secao === tipo;
            secao.hidden = !ativa;
            secao.querySelectorAll('input').forEach(input => { input.disabled = !ativa; });
        });
        const valor = document.getElementById('valor_oferecido');
        if (valor) valor.required = tipo === 'dinheiro';
    }

    function abrirModal(tipo) {
        const radio = tipo && form.querySelector(`input[name="tipo"][value="${tipo}"]`);
        if (radio && radio.type === 'radio') radio.checked = true;
        atualizarSecoes();
        modal.classList.add('aberto');
    }

    radios.forEach(radio => radio.addEventListener('change', atualizarSecoes));

    if (abrir) {
        abrir.addEventListener('click', () => {
            // Usa a opção escolhida nos cards de preço/troca, se houver
            const card = document.querySelector('.detalhes-card-preco.is-ativo');
            abrirModal(card && card.dataset.acao === 'trocar' ? 'troca' : (card ? 'dinheiro' : null));
        });
    }

    fechar.addEventListener('click', () => modal.classList.remove('aberto'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('aberto');
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') modal.classList.remove('aberto');
    });

    form.addEventListener('submit', (e) => {
        if (tipoSelecionado() === 'troca' && !form.querySelector('input[name="livros_oferecidos[]"]:checked')) {
            e.preventDefault();
            alert('Selecione ao menos um livro seu para oferecer na troca.');
        }
    });

    atualizarSecoes();

    <?php if (session('abrirModalProposta')): ?>
        modal.classList.add('aberto');
    <?php endif; ?>
});
</script>
