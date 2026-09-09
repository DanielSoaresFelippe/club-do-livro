<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nova senha - Clube do Livro</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= assetUrl('assets/styles/home.css') ?>">
  <style>
    .reset-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(24px, 6vw, 72px) 20px;
      background-color: var(--cream);
      background-image: url('<?= assetUrl('assets/img/fundo.png') ?>');
      background-position: top center;
      background-size: 100% auto;
      background-repeat: no-repeat;
    }

    .reset-card {
      width: min(900px, 100%);
      min-height: 540px;
      display: grid;
      grid-template-columns: 1fr .9fr;
      overflow: hidden;
      background: #f8df91;
      border: 3px solid var(--ink);
      border-radius: 28px;
      box-shadow: 10px 10px 0 var(--ink);
    }

    .reset-content {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: clamp(32px, 6vw, 64px);
    }

    .reset-eyebrow {
      margin-bottom: 2px;
    }

    .reset-title {
      margin-bottom: 12px;
      font-family: var(--font-comic);
      font-size: clamp(2rem, 5vw, 3.1rem);
      font-weight: 400;
      letter-spacing: 1px;
      color: var(--ink);
      -webkit-text-stroke: 1px var(--ink);
    }

    .reset-intro {
      max-width: 420px;
      margin-bottom: 28px;
      color: var(--ink-soft);
      font-size: .92rem;
    }

    .reset-form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .reset-form .btn {
      margin-top: 6px;
    }

    .reset-message {
      min-height: 24px;
      color: var(--red-pow);
      font-size: .82rem;
      font-weight: 600;
    }

    .reset-message.is-success {
      color: var(--green-deep);
    }

    .reset-back {
      align-self: center;
      margin-top: 8px;
      color: var(--ink);
      font-size: .84rem;
      font-weight: 700;
    }

    .reset-back:hover {
      text-decoration: underline;
    }

    .reset-art {
      min-height: 100%;
      background-image: url('<?= assetUrl('assets/img/fundoLogin.png') ?>');
      background-position: center;
      background-size: 100% 100%;
      background-repeat: no-repeat;
    }

    .reset-invalid {
      max-width: 440px;
      text-align: center;
    }

    .reset-invalid .reset-title {
      font-size: clamp(1.8rem, 4vw, 2.6rem);
    }

    @media (max-width: 680px) {
      .reset-page {
        padding: 20px 14px;
      }

      .reset-card {
        display: block;
        min-height: 0;
      }

      .reset-content {
        padding: 34px 24px 30px;
      }

      .reset-art {
        min-height: 150px;
      }
    }
  </style>
</head>
<body>
  <main class="reset-page">
    <section class="reset-card">
      <div class="reset-content <?= $erro ? 'reset-invalid' : '' ?>">
        <span class="eyebrow reset-eyebrow">clube do livro</span>
        <h1 class="reset-title"><?= $erro ? 'Link inválido' : 'Crie uma nova senha' ?></h1>
        <?php if ($erro): ?>
          <p class="reset-intro"><?= esc($erro) ?></p>
          <a class="btn btn-primary" href="<?= base_url('/') ?>">Voltar ao início</a>
        <?php else: ?>
          <p class="reset-intro">Escolha uma senha forte para voltar a explorar, trocar e descobrir novas histórias.</p>
          <form class="reset-form" id="resetForm">
            <input type="hidden" name="token" value="<?= esc($token) ?>">
            <div class="auth-field">
              <label for="resetPassword">Nova senha</label>
              <input id="resetPassword" type="password" name="senha" required minlength="8" autocomplete="new-password">
            </div>
            <div class="auth-field">
              <label for="resetConfirmation">Confirme a nova senha</label>
              <input id="resetConfirmation" type="password" name="confirmacao" required minlength="8" autocomplete="new-password">
            </div>
            <p class="reset-message" id="resetMessage" role="status" aria-live="polite"></p>
            <button class="btn btn-primary" type="submit">Salvar nova senha</button>
            <a class="reset-back" href="<?= base_url('/') ?>">Voltar para o início</a>
          </form>
          <script>
            document.getElementById('resetForm').addEventListener('submit', async (event) => {
              event.preventDefault();
              const message = document.getElementById('resetMessage');
              const submit = event.target.querySelector('button[type="submit"]');
              submit.disabled = true;
              message.classList.remove('is-success');

              try {
                const response = await fetch('<?= base_url('usuarios/redefinir') ?>', {
                  method: 'POST',
                  body: new FormData(event.target),
                });
                const data = await response.json();
                message.textContent = data.success
                  ? 'Senha alterada. Redirecionando...'
                  : Object.values(data.errors || {})[0] || 'Não foi possível alterar a senha.';
                message.classList.toggle('is-success', Boolean(data.success));
                if (data.success) setTimeout(() => window.location.href = data.redirect, 1200);
              } catch (error) {
                message.textContent = 'Não foi possível concluir a alteração. Tente novamente.';
              } finally {
                submit.disabled = false;
              }
            });
          </script>
        <?php endif; ?>
      </div>
      <div class="reset-art" aria-hidden="true"></div>
    </section>
  </main>
</body>
</html>