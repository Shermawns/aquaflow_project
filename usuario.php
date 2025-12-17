<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - AquaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php

        session_start();
        if (!isset($_SESSION['usuario'])) {
            header("location: login.php");
            exit;
        }

        require_once "config/banco.php";
        require_once "config/function.php";
        require "header.php";


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
        if (isset($_POST['usuario'])) {

            $user = $_POST['usuario'];
            $pass = $_POST['senha'];
            $confpass = $_POST['confirmar'];

            $q = "SELECT usuario FROM tabela_usuarios WHERE usuario = '$user'";

            $busca = $banco->query($q);

            if ($busca->num_rows > 0) {
                echo "<div class='alert alert-warning' id='msgErro'><strong>Usuario já cadastrado!</strong></div>";
            } else {
                if ($pass == $confpass) {
                    $hash = gerarHash($pass);

                    $banco->query("INSERT INTO tabela_usuarios (usuario, senha) VALUES ('$user', '$hash')");
                } else {
                    echo "<div class='alert alert-warning' id='msgErro'><strong>As senhas não se conferem!</strong></div>";
                }
            }
        }
        ?>






        <!-- Lógica de editar usuário -->

        <?php
        if (isset($_POST['editar_usuario'])) {
            $id = $_POST['id_edit'];
            $user = $_POST['usuario_edit'];
            $pass = $_POST['senha_edit'];
            $conf = $_POST['confirmar_edit'];

            if (empty($pass)) {
                $q = "UPDATE tabela_usuarios SET usuario = '$user' WHERE id = '$id'";
                $banco->query($q);
                echo "<meta http-equiv='refresh' content='0'>";
            } else {
                if ($pass == $conf) {
                    $hash = gerarHash($pass);
                    $q = "UPDATE tabela_usuarios SET usuario = '$user', senha = '$hash' WHERE id = '$id'";
                    $banco->query($q);
                    echo "<meta http-equiv='refresh' content='0'>";
                } else {
                    echo "<div class='alert alert-warning' id='msgErro'><strong>As senhas da edição não conferem!</strong></div>";
                }
            }
        }
        ?>




        <!-- Lógica de deletar conta -->

        <?php
        if (isset($_GET['id'])) {
            $conta = $_GET['id'];

            $busca = $banco->query("SELECT usuario FROM tabela_usuarios WHERE id = '$conta'");
            $reg = $busca->fetch_object();

            $delete = $banco->query("DELETE FROM tabela_usuarios WHERE id = '$conta'");

            if ($delete && $reg && $reg->usuario == $_SESSION['usuario']) {
                session_destroy();
                header('Location: login.php');
                exit;
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
                                <label class="form-label text-muted small fw-bold">USUÁRIO</label>
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
                                <label for="user" class="form-label text-muted small fw-bold">USUÁRIO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0" id="user" name="usuario" autocomplete="username" required placeholder="Digite o login">
                                </div>
                            </div>


                            <!-- Campo de senha  -->

                            <div class="mb-3 text-start">
                                <label for="pass" class="form-label text-muted small fw-bold">SENHA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control bg-light border-start-0 ps-0" id="pass" name="senha" autocomplete="new-password" required placeholder="Crie uma senha forte">
                                </div>
                            </div>

                            <!-- Campo de confirmar senha -->

                            <div class="mb-4 text-start">
                                <label for="confirm" class="form-label text-muted small fw-bold">CONFIRMAR SENHA</label>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var alerta = document.getElementById('msgErro');

        if (alerta) {
            setTimeout(function() {
                alerta.style.transition = "opacity 0.5s ease";
                alerta.style.opacity = "0";
                setTimeout(function() {
                    alerta.remove();
                }, 500);
            }, 3000);
        }
    });

    // Função para preencher o modal de edição
    
    function carregarDadosEdicao(botao) {
        var id = botao.getAttribute('data-id');
        var usuario = botao.getAttribute('data-usuario');

        document.getElementById('id_edit').value = id;
        document.getElementById('usuario_edit').value = usuario;
    }
</script>