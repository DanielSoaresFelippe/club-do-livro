<?php
/**
 * Gatilho: nova proposta recebida (para o dono do livro).
 *
 * Variáveis: $nomeDestinatario, $nomeAutor, $tituloLivro, $oferta, $mensagem, $prazo, $link
 */
$this->setVar('tituloPagina', 'Nova proposta pelo seu livro');
$this->setVar('textoBotao', 'Ver proposta');
?>
<?= $this->extend('emails/_layout') ?>

<?= $this->section('manchete') ?>
Você recebeu uma proposta!
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
<p style="
  margin:0 0 22px;
  color:#4a3d40;
  font-size:15px;
  line-height:1.7;
">
  <strong><?= esc($nomeAutor) ?></strong>
  fez uma proposta pelo livro
  <strong><?= esc($tituloLivro) ?></strong>
  que você anunciou no Clube do Livro.
</p>

<?= view('emails/_caixa', ['rotulo' => 'Oferta', 'texto' => $oferta]) ?>

<?php if (!empty($mensagem)): ?>
  <?= view('emails/_caixa', ['rotulo' => 'Mensagem', 'texto' => $mensagem]) ?>
<?php endif; ?>

<p style="
  margin:0 0 22px;
  color:#4a3d40;
  font-size:15px;
  line-height:1.7;
">
  Você pode aceitar, recusar ou fazer uma contraproposta até
  <strong><?= esc($prazo) ?></strong>.
</p>
<?= $this->endSection() ?>
