<?php
/**
 * Moldura dos e-mails do Clube do Livro (mesmo design de emails/interesse_livro.php).
 *
 * Variáveis: $tituloPagina, $nomeDestinatario, $link, $textoBotao
 * Seções:    manchete (h1 do cabeçalho rosa) e conteudo (corpo, após a saudação)
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($tituloPagina) ?> - Clube do Livro</title>
    <style>
      .btn-enviar:hover {
        transform: translateY(2px);
        box-shadow: 0 0 0 #3d2b34 !important;
      }
    </style>
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
                <?= $this->renderSection('manchete') ?>
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
                Olá, <?= esc($nomeDestinatario) ?>!
              </p>

              <?= $this->renderSection('conteudo') ?>

              <table role="presentation"
                     cellspacing="0"
                     cellpadding="0"
                     border="0"
                     style="margin:0 0 24px;">

                <tr>
                  <td>

                    <a class="btn-enviar" href="<?= esc($link) ?>"
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
                      <?= esc($textoBotao) ?>
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
