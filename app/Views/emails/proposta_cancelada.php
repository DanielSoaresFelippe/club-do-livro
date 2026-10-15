<?php
/**
 * Gatilho: quem fez a oferta desistiu dela (para a outra parte da negociação).
 *
 * Variáveis: $nomeDestinatario, $nomeAutor, $tituloLivro, $oferta, $link
 */
$this->setVar('tituloPagina', 'Proposta cancelada');
$this->setVar('textoBotao', 'Ver minhas propostas');
?>
<?= $this->extend('emails/_layout') ?>

<?= $this->section('manchete') ?>
Uma proposta foi cancelada
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
<p style="
  margin:0 0 22px;
  color:#4a3d40;
  font-size:15px;
  line-height:1.7;
">
  <strong><?= esc($nomeAutor) ?></strong>
  desistiu da proposta pelo livro
  <strong><?= esc($tituloLivro) ?></strong>.
  A negociação foi encerrada.
</p>

<?= view('emails/_caixa', ['rotulo' => 'Oferta cancelada', 'texto' => $oferta]) ?>
<?= $this->endSection() ?>
