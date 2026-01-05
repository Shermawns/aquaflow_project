<?php
session_start();

require_once "../config/banco.php";
require_once "../config/function.php";

$toast_mensagem = "";
$toast_tipo = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['usuario'] ?? '';
    $pass = $_POST['senha'] ?? '';

    if (empty($user) || empty($pass)) {
        $toast_mensagem = "Erro: Preencha todos os campos obrigatórios!";
        $toast_tipo = "erro";
    } else {

        $stmt = $banco->prepare("SELECT * FROM tabela_usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $reg = $result->fetch_object();
            if (password_verify($pass, $reg->senha)) {
                $_SESSION['usuario'] = $user;
                header('location:../pages/main.php');
                exit;
            } else {
                $toast_mensagem = "Senha incorreta!";
                $toast_tipo = "erro";
            }
        } else {
            $toast_mensagem = "Usuário não encontrado!";
            $toast_tipo = "erro";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquaflow - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 400px; width: 100%;">
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="../imgs/Gemini_Generated_Image_3y2rqt3y2rqt3y2r.png" alt="Logo AquaFlow" class="img-fluid mb-2" style="max-width: 300px;">
                    <h2 class="h5 text-primary">Acesse sua conta</h2>
                </div>

                <form method="post">
                    <div class="mb-3">
                        <label for="user" class="form-label">Usuário <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="user" name="usuario" autocomplete="username" maxlength="50" placeholder="Digite seu usuário">
                    </div>

                    <div class="mb-5">
                        <label for="pass" class="form-label">Senha <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="pass" name="senha" autocomplete="current-password" required placeholder="Digite sua senha">
                    </div>

                    <div class="d-grid">
                        <input type="submit" class="btn btn-primary btn-lg" value="Entrar">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>

</html>
<script>
    var mensagem = "<?php echo $toast_mensagem; ?>";
    var tipo = "<?php echo $toast_tipo; ?>";

    if (mensagem) {
        var corFundo = tipo === "sucesso" ?
            "linear-gradient(to right, #11998e, #38ef7d)" : // Verde (Sucesso)
            "linear-gradient(to right, #ff416c, #ff4b2b)"; // Vermelho (Erro)

        Toastify({
            text: mensagem,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            stopOnFocus: true,
            style: {
                background: corFundo,
            }
        }).showToast();
    }
</script>