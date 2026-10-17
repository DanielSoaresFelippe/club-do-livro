<?php
/**
 * Gatilho: a oferta foi recusada (para quem fez a oferta).
 *
 * Variáveis: $nomeDestinatario, $nomeQuemRecusou, $tituloLivro, $oferta,
 *            $porOutraNegociacao (recusa automática: o livro foi negociado com outra pessoa), $link
 */
$this->setVar('tituloPagina', 'Proposta recusada');
$this->setVar('textoBotao', 'Ver outros livros');
?>
<?= $this->extend('emails/_layout') ?>

<?= $this->section('manchete') ?>
<?= $porOutraNegociacao ? 'Este livro já foi negociado' : 'Sua proposta foi recusada' ?>
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
<p style="
  margin:0 0 22px;
  color:#4a3d40;
  font-size:15px;
  line-height:1.7;
">
  <?php if ($porOutraNegociacao): ?>
    <strong><?= esc($nomeQuemRecusou) ?></strong>
    fechou negócio com outra pessoa pelo livro
    <strong><?= esc($tituloLivro) ?></strong>,
    então sua proposta foi encerrada.
  <?php else: ?>
    <strong><?= esc($nomeQuemRecusou) ?></strong>
    recusou sua proposta pelo livro
    <strong><?= esc($tituloLivro) ?></strong>.
  <?php endif; ?>
</p>

<?= view('emails/_caixa', ['rotulo' => 'Sua oferta', 'texto' => $oferta]) ?>

<p style="
  margin:0 0 22px;
  color:#4a3d40;
  font-size:15px;
  line-height:1.7;
">
  Que tal procurar outra história? Sempre tem livro novo chegando na estante.
</p>
<?= $this->endSection() ?>
