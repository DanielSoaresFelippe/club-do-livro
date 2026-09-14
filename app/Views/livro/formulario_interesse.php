<?php
$capa = esc($livro['imagem_capa'] ?? base_url('assets/img/capa-padrao.png'));
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
    <title>Demonstrar interesse - Clube do Livro</title>
</head>

<body style="
  margin:0;
  padding:0;
  min-height:100vh;
  display:flex;
  flex-direction:column;
  background-color:#fbf3e2;
  color:#3d2b34;
  font-family:'Poppins', sans-serif;
">

<?= $this->include('partials/navbar') ?>

    <main style="
        flex:1;
        padding-top:80px;
        background-color:#fbf3e2;
    ">

  <table role="presentation"
         width="100%"
         cellspacing="0"
         cellpadding="0"
         style="background-color:#fbf3e2;">

    <tr>
      <td align="center" style="padding:32px 16px;">

        <table role="presentation"
               width="100%"
               cellspacing="0"
               cellpadding="0"
               border="0"
               style="
                 max-width:120vh;
                 background-color:#fffdfb;
                 border:3px solid #3d2b34;
                 border-radius:22px;
                 box-shadow:0 10px 30px rgba(61,43,52,0.18);
                 overflow:hidden;
               ">

          <tr>
            <td width="40%"
                valign="top"
                style="
                padding:0;
                margin:0;
                background-color:#d98aa9;
                line-height:0;
                font-size:0;
                ">

            <table role="presentation"
                    width="100%"
                    height="100%"
                    cellspacing="0"
                    cellpadding="0"
                    border="0"
                    style="
                    width:100%;
                    height:100%;
                    margin:0;
                    padding:0;
                    border-collapse:collapse;
                    ">

                <tr>
                <td valign="top"
                    style="
                        padding:0;
                        margin:0;
                        height:100%;
                        line-height:0;
                        font-size:0;
                    ">

                    <img src="<?= $capa ?>"
                        alt="Capa do livro"
                        width="100%"
                        style="
                        display:block;
                        width:100%;
                        height:100%;
                        min-height:100%;
                        margin:0;
                        padding:0;
                        border:0;
                        object-fit:cover;
                        box-sizing:border-box;
                        ">

                </td>
                </tr>

            </table>

            </td>
            <td width="62%"
                valign="top"
                style="
                  padding:0;
                  background-color:#fffdfb;
                ">

              <table role="presentation"
                     width="100%"
                     cellspacing="0"
                     cellpadding="0"
                     border="0">

                <tr>
                  <td style="
                    padding:30px 32px;
                    background-color: #fff1a4;
                    border-top-right-radius:19px;
                  ">

                    <table role="presentation"
                           width="100%"
                           cellspacing="0"
                           cellpadding="0"
                           border="0">
                      <tr>

                        <td valign="middle" align="left">
                          <h1 style="
                            margin:0;
                            text-align: center;
                            color: #f0bfd2;
                            -webkit-text-stroke: 0.2px #3d2b34;
                            font-family:'Bangers', cursive;
                            font-size:50px;
                            letter-spacing: 1px;
                          ">
                            Demonstrar interesse
                          </h1>
                        </td>
                      </tr>
                    </table>

                  </td>
                </tr>

              </table>

              <table role="presentation"
                     width="100%"
                     cellspacing="0"
                     cellpadding="0"
                     border="0">
                <tr>
                  <td style="padding:40px 32px 32px;">
                    <table role="presentation"
                           width="100%"
                           cellspacing="0"
                           cellpadding="0"
                           border="0"
                           style="margin:0 0 26px;">
                      <tr>
                        <td valign="middle">

                          <p style="
                            margin:0 0 6px;
                            color:#3d2b34;
                            font-family: 'Poppins', sans-serif;
                            font-size:22px;
                            line-height:1.4;
                            font-weight:bold;
                          ">
                            <?= esc($livro['titulo']) ?>
                          </p>

                          <p style="
                            margin:0;
                            color:#6b5a60;
                            font-family: 'Poppins', sans-serif;
                            font-size:15px;
                            line-height:1.5;
                          ">
                            de <?= esc($livro['autor']) ?>
                          </p>
                        </td>
                      </tr>
                    </table>
                    <p style="
                      margin:0 0 22px;
                      color:#4a3d40;
                      font-family: 'Poppins', sans-serif;
                      font-size:16px;
                    ">
                      Você está prestes a enviar uma mensagem para
                      <strong><?= esc($livro['dono_nome'] ?? 'o dono do livro') ?></strong>
                      demonstrando interesse neste livro.
                      Se quiser, escreva algo abaixo.
                    </p>


                    <form method="post"
                          action="<?= site_url('livro/enviarInteresse/' . $livro['id_livro']) ?>">

                      <?= csrf_field() ?>

                      <label for="mensagem"
                             style="
                               display:block;
                               margin-bottom:8px;
                               color:#3d2b34;
                               font-size:13px;
                               font-weight:bold;
                             ">
                        Mensagem (opcional)
                      </label>

                      <textarea
                        name="mensagem"
                        id="mensagem"
                        rows="5"
                        placeholder="Ex: Olá! Ainda tenho interesse em trocar/comprar esse livro..."
                        style="
                          width:100%;
                          box-sizing:border-box;
                          padding:13px;
                          margin:0 0 22px;
                          border:2px solid #535353;
                          border-radius:12px;
                          color:#3d2b34;
                          font-family:'Poppins', sans-serif;
                          font-size:15px;
                          line-height:1.5;
                          resize:vertical;
                        "
                      ></textarea>

                      <table role="presentation"
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0">

                        <tr>

                            <td width="48%" align="left">

                            <a class="btn-enviar" href="<?= site_url('livro/detalhes/' . $livro['id_livro']) ?>"
                                style="
                                display:inline-block;
                                padding:12px 20px;
                                background-color:#fffdfb;
                                border:3px solid #3d2b34;
                                border-radius:12px;
                                box-shadow:4px 4px 0 #3d2b34;
                                color:#3d2b34;
                                font-size:14px;
                                font-weight:bold;
                                text-decoration:none;
                                box-sizing:border-box;
                                width:100%;
                                text-align:center;
                                ">
                                Cancelar
                            </a>

                            </td>

                            <td width="4%"></td>

                            <td width="48%" align="right">

                                <button class="btn-enviar" type="submit"
                                        style="
                                            width:100%;
                                            padding:13px 20px;
                                            border:3px solid #3d2b34;
                                            border-radius:12px;
                                            box-shadow:4px 4px 0 #3d2b34;
                                            background-color: #fff1a4;
                                            color:#3d2b34;
                                            font-size:15px;
                                            font-weight:bolder;
                                            cursor:pointer;
                                        ">
                                    Enviar interesse
                                </button>

                            </td>

                        </tr>

                        </table>

                    </form>

                  </td>
                </tr>

              </table>

            </td>

          </tr>

        </table>

      </td>
    </tr>

   </table>

  </main>

  <?= $this->include('partials/footer') ?>
</body>
</html>

<style>
  .btn-enviar:hover {
    transform: translateY(1px);
    box-shadow: 0 0 0 #3d2b34 !important;
  }
</style>