<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Aquaflow - Login</title>
</head>
<body>
    <div id="login">
        <h1>Bem-vindo ao Aquaflow!</h1> <h2>Faça seu login para continuar</h2>
        
        <form action="index.php" method="post">
            <div class="input">
                <label for="user">Usuário:</label>
                <input type="text" id="user" name="usuario" autocomplete="username" required>
            </div>

            <div class="input">
                <label for="pass">Senha:</label>
                <input type="password" id="pass" name="senha" autocomplete="current-password" required>
            </div>

            <input type="submit" value="Entrar">
        </form>
        <a href="cadastro.php">Não tem conta? Cadastre-se</a>
    </div>
</body>
</html>