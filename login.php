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
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 400px; width: 100%;">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold text-primary #4facfe">AquaFlow</h1>
                    <h2 class="h6 text-secondary">Acesse sua conta</h2>
                </div>

                <form action="index.php" method="post">
                    <div class="mb-3">
                        <label for="user" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="user" name="usuario" autocomplete="username" required placeholder="Digite seu usuário">
                    </div>

                    <div class="mb-3">
                        <label for="pass" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="pass" name="senha" autocomplete="current-password" required placeholder="Digite sua senha">
                    </div>

                    <div class="d-grid">
                        <input type="submit" class="btn btn-primary btn-lg" value="Entrar">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>