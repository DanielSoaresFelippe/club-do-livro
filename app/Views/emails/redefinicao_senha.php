_<?php
$nome = esc($nome);
$link = esc($link);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redefinição de senha - Clube do Livro</title>
</head>
<body style="margin:0; padding:0; background-color:#fbf3e2; color:#2a2313; font-family:Arial, Helvetica, sans-serif;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#fbf3e2;">
    <tr>
      <td align="center" style="padding:32px 16px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:620px; background-color:#fffdf8; border:3px solid #2a2313;">
          <tr>
            <td style="padding:30px 32px; background-color:#b6b24a; border-bottom:3px solid #2a2313;">
              <p style="margin:0 0 8px; color:#4d4c26; font-size:12px; font-weight:bold; letter-spacing:2px; text-transform:uppercase;">clube do livro</p>
              <h1 style="margin:0; color:#2a2313; font-family:Georgia, 'Times New Roman', serif; font-size:34px; line-height:1.15;">Sua história continua</h1>
            </td>
          </tr>
          <tr>
            <td style="padding:40px 32px 32px;">
              <p style="margin:0 0 18px; font-size:18px; line-height:1.5;">Olá, <?= $nome ?>!</p>
              <p style="margin:0 0 22px; color:#3d3319; font-size:15px; line-height:1.7;">Recebemos um pedido para criar uma nova senha para sua conta. Clique no botão abaixo para continuar.</p>
              <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 24px;">
                <tr>
                  <td style="background-color:#2a2313;">
                    <a href="<?= $link ?>" style="display:inline-block; padding:15px 24px; color:#fbf3e2; font-size:15px; font-weight:bold; text-decoration:none;">Criar nova senha</a>
                  </td>
                </tr>
              </table>
              <p style="margin:0 0 12px; color:#3d3319; font-size:13px; line-height:1.6;">Este link expira em 30 minutos e só pode ser usado uma vez.</p>
              <p style="margin:0; color:#3d3319; font-size:13px; line-height:1.6;">Se você não solicitou essa alteração, pode ignorar este e-mail com segurança.</p>
            </td>
          </tr>
          <tr>
            <td style="padding:18px 32px; background-color:#f1e2c4; border-top:1px solid #ecb8d3;">
              <p style="margin:0; color:#4d4c26; font-size:12px; line-height:1.5;">Cada livro trocado ganha uma nova história.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>