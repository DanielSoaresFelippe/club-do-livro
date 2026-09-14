<?php
$nomeDono        = esc($nomeDono);
$nomeInteressado = esc($nomeInteressado);
$tituloLivro     = esc($tituloLivro);
$link            = esc($link);
$mensagem        = esc($mensagem ?? '');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interesse no seu livro - Clube do Livro</title>
</head>

<body style="margin:0; padding:0; background-color:#fdf6f9; color:#3d2b34; font-family:'Poppins', sans-serif;">

  <table role="presentation"
         width="100%"
         cellspacing="0"
         cellpadding="0"
         border="0"
         style="background-color:#fdf6f9;">

    <tr>
      <td align="center" style="padding:32px 16px;">
        <table role="presentation"
               width="100%"
               cellspacing="0"
               cellpadding="0"
               border="0"
               style="max-width:620px; background-color:#fffdfb; border:3px solid #3d2b34;">
          <tr>
            <td style="
              padding:30px 32px;
              background-color: #d98aa9;
              border-bottom:3px solid #3d2b34;
            ">

              <p style="
                margin:0 0 8px;
                color: #3d2b34;
                font-size:12px;
                font-weight:bold;
                text-transform:uppercase;
              ">
                clube do livro
              </p>

              <h1 style="
                margin:0;
                color: #3d2b34;
                font-family:'Poppins', sans-serif;
                font-size:34px;
                line-height:1.15;
              ">
                Alguém tem interesse no seu livro!
              </h1>

            </td>
          </tr>
          <tr>
            <td style="padding:40px 32px 32px;">

              <p style="
                margin:0 0 18px;
                font-size:18px;
                line-height:1.5;
              ">
                Olá, <?= $nomeDono ?>!
              </p>

              <p style="
                margin:0 0 22px;
                color:#4a3d40;
                font-size:15px;
                line-height:1.7;
              ">
                <strong><?= $nomeInteressado ?></strong>
                demonstrou interesse no livro
                <strong><?= $tituloLivro ?></strong>
                que você anunciou no Clube do Livro.
              </p>

              <?php if (!empty($mensagem)): ?>
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
                        Mensagem
                      </p>

                      <p style="
                        margin:0;
                        color:#4a3d40;
                        font-size:14px;
                        line-height:1.6;
                      ">
                        <?= nl2br($mensagem) ?>
                      </p>

                    </td>
                  </tr>

                </table>

              <?php endif; ?>

              <table role="presentation"
                     cellspacing="0"
                     cellpadding="0"
                     border="0"
                     style="margin:0 0 24px;">

                <tr>
                  <td>

                    <a class="btn-enviar" href="<?= $link ?>"
                       style="
                            width:100%;
                            padding:13px 20px;
                            border:2px solid #3d2b34;
                            border-radius:18px;
                            box-shadow:4px 4px 0 #3d2b34;
                            background-color: #d98aa9;
                            color:#3d2b34;
                            font-size:15px;
                            font-weight:bolder;
                            cursor:pointer;
                            text-decoration:none;
                        ">
                      Ver anúncio do livro
                    </a>

                  </td>
                </tr>

              </table>

            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

<style>
  .btn-enviar:hover {
    transform: translateY(2px);
    box-shadow: 0 0 0 #3d2b34 !important;
  }
</style>