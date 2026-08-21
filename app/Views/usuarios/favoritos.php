<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Favoritos - Clube do Livro</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
        margin: 0;
        font-family: 'Georgia', serif;
        background-color: #eef0dd;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
    }
    h1 { color: #7a2f2f; }
    p { color: #4a4a3a; max-width: 420px; }
    a {
        margin-top: 20px;
        color: #7a2f2f;
        text-decoration: underline;
    }
</style>
</head>
<body>
    <h1>Livros favoritados</h1>
    <p>Aqui vão aparecer os livros que você marcou como favorito. Página em construção.</p>
    <a href="<?= base_url('usuarios/perfil') ?>">&larr; Voltar para minha carteirinha</a>
</body>
</html>