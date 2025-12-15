<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquaflow - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>


<?php 
    session_start();

    require_once "config/banco.php";
    require_once "config/function.php";

    $user = $_POST['usuario'] ?? null;
    $pass = $_POST['senha'] ?? null;


    if(!is_null($user) && !is_null($pass)){
        $val = "SELECT usuario, senha FROM tabela_usuarios WHERE usuario='$user' ";
        $busca = $banco->query($val);

        if($busca->num_rows > 0){
            $reg = $busca->fetch_object();
            if(testarHash($pass, $reg->senha)){
                $_SESSION['usuario'] = $reg->usuario;
                echo "<div class='alert alert-success' id='msgErro'><strong>Login realizado com sucesso!</strong></div>";
                header('location: main.php');   
            }else{
                echo "<div class='alert alert-danger' id='msgErro'><strong>Senha incorreta!</strong></div>";
            }
        }else{
            echo "<div class='alert alert-warning' id='msgErro'><strong>Usuario não cadastrado!</strong></div>";
        }
    }
?>

<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 400px; width: 100%;">
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="imgs/Gemini_Generated_Image_3y2rqt3y2rqt3y2r.png" alt="Logo AquaFlow" class="img-fluid mb-2" style="max-width: 300px;">
                    <h2 class="h5 text-primary">Acesse sua conta</h2>
                </div>

                <form method="post">
                    <div class="mb-3">
                        <label for="user" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="user" name="usuario" autocomplete="username" required placeholder="Digite seu usuário">
                    </div>

                    <div class="mb-5">
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var alerta = document.getElementById('msgErro');
    
        if (alerta) {
            setTimeout(function() {
                alerta.style.transition = "opacity 0.5s ease";
                alerta.style.opacity = "0";
                setTimeout(function(){
                    alerta.remove();
                }, 500); 
            }, 3000);
        }   
    });
</script>