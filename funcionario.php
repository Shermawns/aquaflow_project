<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Funcionários - AquaFlow</title>
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
                <h2 class="fw-bold mb-1" style="color: #0d6efd;">Funcionários</h2>
                <p class="text-muted mb-0">Controle de Colaboradores</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
                <i class="fa-solid fa-plus me-2"></i>Cadastrar funcionário
            </button>
        </div>


        <!-- Lógica de registro-->

        <?php
        if (isset($_POST['cpf'])) {

            $cpf = $_POST['cpf'];
            $nome = $_POST['nome'];

            $q = "SELECT cpf FROM tabela_funcionarios WHERE cpf = '$cpf'";

            $busca = $banco->query($q);

            if ($busca->num_rows > 0) {
                echo "<div class='alert alert-warning' id='msgErro'><strong>Funcionário já cadastrado!</strong></div>";
            } else {
                $admissao = date('Y-m-d');
                $banco->query("INSERT INTO tabela_funcionarios (cpf, nome, data_contratacao) VALUES ('$cpf', '$nome', '$admissao')");
            }
        }
        ?>

        <!-- Lógica de editar funcionário -->

        <?php
        if (isset($_POST['editar_funcionario'])) {
            $id = $_POST['id_edit'];
            $nome = $_POST['nome_edit'];
            $q = "UPDATE tabela_funcionarios SET nome = '$nome' WHERE id = '$id'";
            $banco->query($q);
            echo "<meta http-equiv='refresh' content='0'>";
        }
        ?>

        <!-- Caixa onde fica os funcionarios listados-->

        <div class="card border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-secondary border-0">Funcionário</th>
                                <th class="pe-4 py-3 text-secondary text-end border-0">Ações</th>
                            </tr>
                        </thead>


                        <!-- Lógica de listar todos os funcionarios -->

                        <?php
                        $q = "SELECT * FROM tabela_funcionarios";
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
                                                    <h6 class="mb-0 fw-semibold">' . $reg->nome . '</h6>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Botão de editar -->

                                        <td class="pe-4 text-end">
                                            
                                            <input type="button" 
                                                class="btn btn-sm btn-outline-info rounded-pill me-1" 
                                                value="Visualizar" 
                                                title="Visualizar" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalVisualizar"
                                                data-nome="' . $reg->nome . '"
                                                data-cpf="' . $reg->cpf . '"
                                                data-contratacao="' . date('d/m/Y', strtotime($reg->data_contratacao)) . '"
                                                onclick="carregarDadosVisualizacao(this)">

                                            <input type="button" 
                                                class="btn btn-sm btn-outline-primary rounded-pill me-1" 
                                                value="Editar" 
                                                title="Editar" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditar_func"
                                                data-id="' . $reg->id . '"
                                                data-nome="' . $reg->nome . '"
                                                onclick="carregarDadosEdicao(this)">

                                            <input type="button" 
                                                class="btn btn-sm btn-outline-danger rounded-pill me-1" 
                                                value="Desligar" 
                                                title="Desligar" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalDesligar_func"
                                                data-id="' . $reg->id . '"
                                                data-nome="' . $reg->nome . '"
                                                onclick="confirmarDesligamento(this)">

                                        </td>
                                    </tr>';
                            }
                            echo '</tbody>';
                        } else {
                            echo '<tbody><tr><td colspan="2" class="text-center">Nenhum funcionário cadastrado.</td></tr></tbody>';
                        }
                        ?>

                    </table>
                </div>
            </div>
        </div>
    </div>

        <!-- Modal de visualizar funcionário -->

        <div class="modal fade" id="modalVisualizar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold">Detalhes do Funcionário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4 px-4">
                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold">NOME COMPLETO</label>
                            <input type="text" class="form-control bg-light" id="visualizar_nome" readonly>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold">CPF</label>
                            <input type="text" class="form-control bg-light" id="visualizar_cpf" readonly>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold">DATA DE CONTRATAÇÃO</label>
                            <input type="text" class="form-control bg-light" id="visualizar_contratacao" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de editar funcionário -->

        <div class="modal fade" id="modalEditar_func" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold">Editar Funcionário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>


                    <div class="modal-body py-4 px-4">
                        <form method="post">
                            <input type="hidden" name="id_edit" id="id_edit">

                            <!-- Campo de nome completo -->

                            <div class="mb-3 text-start">
                                <label class="form-label text-muted small fw-bold">NOME COMPLETO</label>
                                <input type="text" class="form-control bg-light" id="funcionario_edit" name="nome_edit" required>
                            </div>

                            <!-- Botão de salvar alterações -->

                            <div class="modal-footer border-top-0 justify-content-center">
                                <button type="submit" name="editar_funcionario" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>



                </div>
            </div>
        </div>

                <!-- Modal de desligar funcionário -->

        <div class="modal fade" id="modalDesligar_func" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">

                    <div class="modal-header border-bottom-0 justify-content-center position-relative">
                        <h5 class="modal-title fw-bold text-danger fs-4">DESLIGAMENTO</h5>
                        <button type="button"class="btn-close position-absolute end-0 me-3"data-bs-dismiss="modal"aria-label="Close"></button>
                    </div>

                    <div class="modal-body py-4 px-4">
                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold">Você tem certeza que quer desligar o funcionário abaixo?</label>
                            <input type="text" class="form-control bg-light" id="conf"readonly>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 justify-content-center gap-3">
                        <button type="button" class="btn btn-secondary btn-lg px-5 rounded-pill shadow-sm" data-bs-dismiss="modal">Não</button>
                        <form method="post">
                            <input type="hidden" name="id_desligamento" id="id_desligamento">
                            <button type="submit" class="btn btn-danger btn-lg px-5 rounded-pill shadow-sm" name="desligar-funcionario">Sim</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <?php 
            if(isset($_POST['desligar-funcionario'])){
                $id = $_POST['id_desligamento'];
                $q = "UPDATE tabela_funcionarios SET data_demissao = NOW() WHERE id = '$id'";

                $banco->query($q);

                echo "<meta http-equiv='refresh' content='0'>";
            }
        ?>



        <!-- Modal de cadastro de funcionários  -->

        <div class="modal fade" id="modalCadastrar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold">Cadastrar Funcionário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Campo de CPF -->

                    <div class="modal-body py-4 px-4">
                        <form method="post">
                            <div class="mb-3 text-start">
                                <label for="cpf" class="form-label text-muted small fw-bold">CPF</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary">
                                        <i class="fa-regular fa-file"></i>
                                    </span>
                                    <input type="text" oninput="mascara(this)" class="form-control bg-light border-start-0 ps-0" id="cpf" name="cpf" minlength="11" required placeholder="Digite o CPF">
                                </div>
                            </div>


                            <!-- Campo de nome completo  -->

                            <div class="mb-3 text-start">
                                <label for="pass" class="form-label text-muted small fw-bold">NOME COMPLETO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0" id="name" name="nome" maxlength="100" placeholder="Digite seu nome completo">
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
        var funcionario = botao.getAttribute('data-nome');

        document.getElementById('id_edit').value = id;
        document.getElementById('funcionario_edit').value = funcionario;
    }

    // Função para preencher o modal de visualização
    
    function carregarDadosVisualizacao(botao) {
        var nome = botao.getAttribute('data-nome');
        var cpf = botao.getAttribute('data-cpf');
        var contratacao = botao.getAttribute('data-contratacao');

        document.getElementById('visualizar_nome').value = nome;
        document.getElementById('visualizar_cpf').value = cpf;
        document.getElementById('visualizar_contratacao').value = contratacao;
    }

        // Função para confirmar desligamento do funcionario
    
    function confirmarDesligamento(botao) {
        var id = botao.getAttribute('data-id');
        var nome = botao.getAttribute('data-nome');

        document.getElementById('conf').value = nome;
        document.getElementById('id_desligamento').value = id;
    }

    //funcao para padronizar cpf

    function mascara(i) {

        var v = i.value;

        if (isNaN(v[v.length - 1])) {
            i.value = v.substring(0, v.length - 1);
            return;
        }

        i.setAttribute("maxlength", "14");
        if (v.length == 3 || v.length == 7) i.value += ".";
        if (v.length == 11) i.value += "-";

    }
</script>