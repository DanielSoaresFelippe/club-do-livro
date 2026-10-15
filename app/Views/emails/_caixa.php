<?php
/**
 * Caixa de destaque dos e-mails (mesmo estilo do bloco "Mensagem" de interesse_livro.php).
 *
 * Variáveis: $rotulo, $texto (texto puro: é escapado aqui)
 */
?>
<table role="presentation"
       width="100%"
       cellspacing="0"
       cellpadding="0"
       border="0"
       style="margin:0 0 24px;">

  <tr>
    <td style="
      padding:16px 18px;
      background-color:#fff0f5;
      border-left:4px solid #ffb3cf;
    ">

      <p style="
        margin:0 0 6px;
        color: #a15a78;
        font-size:12px;
        font-weight:bold;
      ">
        <?= esc($rotulo) ?>
      </p>

      <p style="
        margin:0;
        color:#4a3d40;
        font-size:14px;
        line-height:1.6;
      ">
        <?= nl2br(esc($texto)) ?>
      </p>

    </td>
  </tr>

</table>
