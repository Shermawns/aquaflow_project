<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquaflow - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 400px; width: 100%;">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold text-primary #4facfe">AquaFlow</h1>
                    <h2 class="h6 text-secondary">Acesse sua conta</h2>
                </div>
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
                    <a href="cadastro.php" class="text-decoration-none">Não tem conta? Cadastre-se</a>
            </div>
        </div>
    </div>
</body>

</html>