<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("location: ../login/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - AquaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <?php

    require_once "../config/banco.php";
    require_once "../config/function.php";
    require "../includes/header.php";

    $toast_mensagem = "";
    $toast_tipo = "";

    ?>





    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h2 class="fw-bold mb-1" style="color: #0d6efd;">Usuários</h2>
                <p class="text-muted mb-0">Gerencie o acesso ao sistema</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
                <i class="fa-solid fa-plus me-2"></i>Cadastrar Usuário
            </button>
        </div>






        <!-- Lógica de registro-->

<?php

if (isset($_POST['usuario']) && !isset($_POST['editar_usuario'])) {
    $user = $_POST['usuario'];
    $pass = $_POST['senha'];
    $confpass = $_POST['confirmar'];

<<<<<<< HEAD
            if (empty($user) || empty($pass) || empty($confpass)) {
                $toast_mensagem = "Erro: Preencha todos os campos!";
                $toast_tipo = "erro";
            } else {
                $q = "SELECT usuario FROM tabela_usuarios WHERE usuario = '$user'";
                $busca = $banco->query($q);

                if ($busca->num_rows > 0) {
                    $toast_mensagem = "Erro: Usuário já cadastrado!";
                    $toast_tipo = "erro";
                } else {
                    if ($pass == $confpass) {
                        $hash = gerarHash($pass);

                        $banco->query("INSERT INTO tabela_usuarios (usuario, senha) VALUES ('$user', '$hash')");
                        $toast_mensagem = "Usuário cadastrado com sucesso!";
                        $toast_tipo = "sucesso";
                    } else {
                        $toast_mensagem = "Erro: As senhas não conferem!";
                        $toast_tipo = "erro";
                    }
                }
=======
    $stmt = $banco->prepare("SELECT usuario FROM tabela_usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $toast_mensagem = "Erro: Usuário já cadastrado!";
        $toast_tipo = "erro";
    } else {
        if ($pass === $confpass) {
            $hash = gerarHash($pass);
            
            $stmt_in = $banco->prepare("INSERT INTO tabela_usuarios (usuario, senha) VALUES (?, ?)");
            $stmt_in->bind_param("ss", $user, $hash);
            
            if ($stmt_in->execute()) {
                $toast_mensagem = "Usuário cadastrado com sucesso!";
                $toast_tipo = "success";
>>>>>>> dbf2c8fa79d338be7ff146eb97e2cda7785e611f
            }
        } else {
            $toast_mensagem = "Erro: As senhas não conferem!";
            $toast_tipo = "erro";
        }
    }
}


if (isset($_POST['editar_usuario'])) {
    $id = $_POST['id_edit'];
    $user = $_POST['usuario_edit'];
    $pass = $_POST['senha_edit'];
    $conf = $_POST['confirmar_edit'];

<<<<<<< HEAD



        <!-- Lógica de editar usuário -->

        <?php
        if (isset($_POST['editar_usuario'])) {
            $id = $_POST['id_edit'];
            $user = $_POST['usuario_edit'];
            $pass = $_POST['senha_edit'];
            $conf = $_POST['confirmar_edit'];

            if (empty($user)) {
                $toast_mensagem = "Erro: O nome de usuário não pode ser vazio!";
                $toast_tipo = "erro";
            } else {
                if (empty($pass)) {
                    $q = "UPDATE tabela_usuarios SET usuario = '$user' WHERE id = '$id'";
                    $banco->query($q);
                    $toast_mensagem = "Usuário editado com sucesso!";
                    $toast_tipo = "sucesso";
                    // echo "<meta http-equiv='refresh' content='0'>"; // Removido para mostrar o Toast
                } else {
                    if ($pass == $conf) {
                        $hash = gerarHash($pass);
                        $q = "UPDATE tabela_usuarios SET usuario = '$user', senha = '$hash' WHERE id = '$id'";
                        $banco->query($q);
                        $toast_mensagem = "Usuário editado com sucesso!";
                        $toast_tipo = "sucesso";
                        // echo "<meta http-equiv='refresh' content='0'>"; // Removido
                    } else {
                        $toast_mensagem = "Erro: As senhas da edição não conferem!";
                        $toast_tipo = "erro";
                    }
                }
            }
=======
    if (empty($pass)) {
        $stmt = $banco->prepare("UPDATE tabela_usuarios SET usuario = ? WHERE id = ?");
        $stmt->bind_param("si", $user, $id);
    } else {
        if ($pass === $conf) {
            $hash = gerarHash($pass);
            $stmt = $banco->prepare("UPDATE tabela_usuarios SET usuario = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("ssi", $user, $hash, $id);
            $toast_mensagem = "Dados alterados com sucesso!";
            $toast_tipo = "success";
        } else {
            $toast_mensagem = "Erro: As senhas não conferem!";
            $toast_tipo = "erro";
>>>>>>> dbf2c8fa79d338be7ff146eb97e2cda7785e611f
        }
    }
}


if (isset($_GET['id'])) {
    $id_del = $_GET['id'];

    $stmt = $banco->prepare("SELECT usuario FROM tabela_usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_del);
    $stmt->execute();
    $res = $stmt_busca->get_result()->fetch_object();

    if ($res) {
        $usuario = $res->usuario;
        $stmt_del = $banco->prepare("DELETE FROM tabela_usuarios WHERE id = ?");
        $stmt_del->bind_param("i", $id_del);
        
        if ($stmt_del->execute()) {
            if ($usuario_alvo === $_SESSION['usuario']) {
                session_destroy();
                header('Location: ../login/login.php');
                exit;
            }
            $toast_mensagem = "Usuário removido com sucesso!";
            $toast_tipo = "success";
        }
    }
}
?>




        <!-- Caixa onde fica os usuários listados-->

        <div class="card border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-secondary border-0">Usuário</th>
                                <th class="pe-4 py-3 text-secondary text-end border-0">Ações</th>
                            </tr>
                        </thead>



                        <!-- Lógica de listar todos os usuários -->

                        <?php
                        $q = "SELECT * FROM tabela_usuarios";
                        $busca = $banco->query($q);

                        if ($busca->num_rows > 0) {
                            echo '<tbody>';
                            while ($reg = $busca->fetch_object()) {
                                echo '<tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fa-regular fa-user"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">' . $reg->usuario . '</h6>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Botões de editar e excluir -->

                                        <td class="pe-4 text-end">
                                            
                                            <input type="button" 
                                                class="btn btn-sm btn-outline-primary rounded-pill me-1" 
                                                value="Editar" 
                                                title="Editar" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditar_func"
                                                data-id="' . $reg->id . '"
                                                data-usuario="' . $reg->usuario . '"
                                                onclick="carregarDadosEdicao(this)">
                                            
                                            <input type="button" 
                                                class="btn btn-sm btn-outline-danger rounded-pill" 
                                                value="Excluir" 
                                                onclick="window.location.href=\'usuario.php?id=' . $reg->id . '\'">
                                        </td>


                                    </tr>';
                            }
                            echo '</tbody>';
                        } else {
                            echo '<tbody><tr><td colspan="2" class="text-center">Nenhum usuário cadastrado.</td></tr></tbody>';
                        }
                        ?>



                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de editar usuário -->

        <div class="modal fade" id="modalEditar_func" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold">Editar Usuário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>



                    <!-- Formulário de ediçao -->

                    <div class="modal-body py-4 px-4">
                        <form method="post">
                            <input type="hidden" name="id_edit" id="id_edit">

                            <div class="mb-3 text-start">
                                <label class="form-label text-muted small fw-bold">USUÁRIO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-light" id="usuario_edit" name="usuario_edit" maxlength="50" required>
                            </div>

                            <div class="mb-3 text-start">
                                <label class="form-label text-muted small fw-bold">NOVA SENHA (Deixe vazio para manter)</label>
                                <input type="password" class="form-control bg-light" name="senha_edit" placeholder="Nova senha">
                            </div>

                            <div class="mb-4 text-start">
                                <label class="form-label text-muted small fw-bold">CONFIRMAR NOVA SENHA</label>
                                <input type="password" class="form-control bg-light" name="confirmar_edit" placeholder="Repita a nova senha">
                            </div>

                            <div class="modal-footer border-top-0 justify-content-center">
                                <button type="submit" name="editar_usuario" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>



                </div>
            </div>
        </div>


        <!-- Modal de cadastro de usuarios  -->

        <div class="modal fade" id="modalCadastrar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold">Cadastrar Usuário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Campo de usuário -->

                    <div class="modal-body py-4 px-4">
                        <form method="post">
                            <div class="mb-3 text-start">
                                <label for="user" class="form-label text-muted small fw-bold">USUÁRIO <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0" id="user" name="usuario" autocomplete="username" required placeholder="Digite o login">
                                </div>
                            </div>


                            <!-- Campo de senha  -->

                            <div class="mb-3 text-start">
                                <label for="pass" class="form-label text-muted small fw-bold">SENHA <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control bg-light border-start-0 ps-0" id="pass" name="senha" autocomplete="new-password" required placeholder="Crie uma senha forte">
                                </div>
                            </div>

                            <!-- Campo de confirmar senha -->

                            <div class="mb-4 text-start">
                                <label for="confirm" class="form-label text-muted small fw-bold">CONFIRMAR SENHA <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary">
                                        <i class="fa-solid fa-check-double"></i>
                                    </span>
                                    <input type="password" class="form-control bg-light border-start-0 ps-0" id="confirm" name="confirmar" autocomplete="new-password" required placeholder="Repita a senha">
                                </div>
                            </div>

                            <!-- Botão submit -->

                            <div class="modal-footer border-top-0 justify-content-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Cadastrar</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    // Função para preencher o modal de edição

    function carregarDadosEdicao(botao) {
        var id = botao.getAttribute('data-id');
        var usuario = botao.getAttribute('data-usuario');

        document.getElementById('id_edit').value = id;
        document.getElementById('usuario_edit').value = usuario;
    }
</script>

<script>
    var mensagem = "<?php echo $toast_mensagem; ?>";
    var tipo = "<?php echo $toast_tipo; ?>";

    if (mensagem) {
<<<<<<< HEAD
        var corFundo = tipo === "sucesso" ?
            "linear-gradient(to right, #11998e, #38ef7d)" :
            "linear-gradient(to right, #ff416c, #ff4b2b)";
=======
        var corFundo = tipo === "success" ?
            "linear-gradient(to right, #00b09b, #2cabd1ff)" :
            "linear-gradient(to right, #ff5f6d, #e562f7ff)";
>>>>>>> dbf2c8fa79d338be7ff146eb97e2cda7785e611f

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