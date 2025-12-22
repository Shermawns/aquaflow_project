<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Funcionários - AquaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>


    <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header("location: ../login/login.php");
            exit;
        }

        require_once "../config/banco.php";
        require_once "../config/function.php";
        require "../includes/header.php";

        $toast_mensagem = "";
        $toast_tipo = "";
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










        <!-- Lógica de cadastrar funcionario -->


        <?php
        if (isset($_POST['cpf'])) {
            $cpf = $_POST['cpf'];
            $nome = $_POST['nome'];

            $stmt = $banco->prepare("SELECT cpf FROM tabela_funcionarios WHERE cpf = ?");
            
            $stmt->bind_param("s", $cpf);

            $stmt->execute();


            if ($stmt->get_result()->num_rows > 0) {
                $toast_mensagem = "Erro: Usuário já cadastrado";
                $toast_tipo = "erro";
            } else {
                $admissao = date('Y-m-d');
                $stmt = $banco->prepare("INSERT INTO tabela_funcionarios (cpf, nome, data_contratacao) VALUES (?, ?, ?)");
                $stmt->bind_param('sss', $cpf, $nome, $admissao);
                if($stmt->execute()){
                    $toast_mensagem = "Funcionário cadastrado com sucesso";
                    $toast_tipo = "success";
                }else {
                    $toast_mensagem = "Erro ao cadastrar no banco de dados.";
                    $toast_tipo = "erro";
                }

            }
        }
        ?>











        <!-- Lógica de editar funcionario -->


        <?php
            if (isset($_POST['editar_funcionario'])) {
                $id = $_POST['id_edit'];
                $nome = $_POST['nome_edit'];
                
                $stmt = $banco->prepare("UPDATE tabela_funcionarios SET nome = ? WHERE id = ?");
                $stmt->bind_param('si', $nome, $id);
                
                if ($stmt->execute()) {
                    $toast_mensagem = "Funcionário atualizado com sucesso!";
                    $toast_tipo = "success";
                }
            }
        ?>











        <!--Lógica de desligamento -->


        <?php
        if (isset($_POST['desligar-funcionario'])) {
            $id = $_POST['id_desligamento'];
            $stmt = $banco->prepare( "UPDATE tabela_funcionarios SET data_demissao = NOW() WHERE id = ?");
            $stmt->bind_param('i', $id);
            if($stmt->execute()){
                $toast_mensagem = "Funcionário desligado com sucesso!";
                $toast_tipo = "success";
            }
        }
        ?>








        <!--Lógica de ativação -->


        <?php
        if (isset($_POST['ativar-funcionario'])) {
            $id = $_POST['id_ativar'];
            $stmt =$banco->prepare ("UPDATE tabela_funcionarios SET data_demissao = NULL WHERE id = ?");
            $stmt->bind_param('i', $id);
            if($stmt->execute()){
                $toast_mensagem = "Funcionário ativado novamente!";
                $toast_tipo = "success";
            }
        }
        ?>











        <!-- Tabela de listagem -->



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



                        <!-- Lógica de listar funcionarios -->

                        <?php
                        $q = ("SELECT * FROM tabela_funcionarios ORDER BY nome");

                        $resultado = $banco->query($q);

                        if ($resultado->num_rows > 0) {
                            echo '<tbody>';
                            while ($reg = $resultado->fetch_object()) {
                                echo '<tr>
                                     <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px;height: 40px;">
                                                    <i class="fa-regular fa-user"></i>
                                                </div>
                                            <div>
                                                    <h6 class="mb-0 fw-semibold">' . $reg->nome . '</h6>
                                            </div>
                                        </div>
                                     </td>

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
                                                onclick="carregarDadosEdicao(this)">';

                                if ($reg->data_demissao == null) {
                                    echo '<button type="button" 
                                                class="btn btn-sm btn-outline-danger rounded-pill me-1" 
                                                title="Desligar" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalDesligar_func"
                                                data-id="' . $reg->id . '"
                                                data-nome="' . $reg->nome . '"
                                                onclick="confirmarDesligamento(this)">
                                                Desligar
                                            </button>';
                                } else {
                                    echo '<button type="button" 
                                                class="btn btn-sm btn-success rounded-pill me-1"
                                                title="Ativar" 
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalReativar_func"
                                                data-id="' . $reg->id . '"
                                                data-nome="' . $reg->nome . '"
                                                onclick="confirmarAtivacao(this)">
                                                Ativar
                                            </button>';
                                }
                            }
                        } else {
                            echo '<tbody><tr><td colspan="2" class="text-center">Nenhum funcionário cadastrado.</td></tr></tbody>';
                        }
                        ?>




                    </table>
                </div>
            </div>
        </div>
    </div>














    <!-- Modal de visualização -->

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










    <!-- Modal de Edição -->

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

                        <div class="mb-3 text-start">
                            <label for="funcionario_edit" class="form-label text-muted small fw-bold">NOME COMPLETO</label>
                            <input type="text" class="form-control bg-light" id="funcionario_edit" name="nome_edit" required>
                        </div>

                        <div class="modal-footer border-top-0 justify-content-center">
                            <button type="submit" name="editar_funcionario" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>








    <!-- Modal de desligamento -->

    <div class="modal fade" id="modalDesligar_func" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 justify-content-center position-relative">
                    <h5 class="modal-title fw-bold text-danger fs-4">DESLIGAMENTO</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4 px-4">
                    <div class="mb-3 text-start">
                        <label class="form-label text-muted small fw-bold">Você tem certeza que quer desligar o funcionário abaixo?</label>
                        <input type="text" class="form-control bg-light" id="conf" readonly>
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











    <!-- Modal de Ativação -->

    <div class="modal fade" id="modalReativar_func" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 justify-content-center position-relative">
                    <h5 class="modal-title fw-bold text-success fs-4">ATIVAÇÃO</h5>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4 px-4">
                    <div class="mb-3 text-start">
                        <label class="form-label text-muted small fw-bold">Você tem certeza que quer ativar o funcionário abaixo?</label>
                        <input type="text" class="form-control bg-light" id="confativ" readonly>
                    </div>
                </div>
                <div class="modal-footer border-top-0 justify-content-center gap-3">
                    <button type="button" class="btn btn-secondary btn-lg px-5 rounded-pill shadow-sm" data-bs-dismiss="modal">Não</button>
                    <form method="post">
                        <input type="hidden" name="id_ativar" id="id_ativar">
                        <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow-sm" name="ativar-funcionario">Sim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>










    <!-- Modal de cadastrar funcionário -->

    <div class="modal fade" id="modalCadastrar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Cadastrar Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

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

                        <div class="mb-3 text-start">
                            <label for="name" class="form-label text-muted small fw-bold">NOME COMPLETO</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-primary">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" class="form-control bg-light border-start-0 ps-0" id="name" name="nome" maxlength="100" placeholder="Digite seu nome completo">
                            </div>
                        </div>

                        <div class="modal-footer border-top-0 justify-content-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Cadastrar</button>
                        </div>
                    </form>
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
        document.getElementById('visualizar_contratacao').value = contratacao ? contratacao : "";
    }







    // Função para confirmar desligamento do funcionario
    function confirmarDesligamento(botao) {
        var id = botao.getAttribute('data-id');
        var nome = botao.getAttribute('data-nome');

        document.getElementById('conf').value = nome;
        document.getElementById('id_desligamento').value = id;
    }








    // Função para confirmar ativacao do funcionario
    function confirmarAtivacao(botao) {
        var id = botao.getAttribute('data-id');
        var nome = botao.getAttribute('data-nome');

        document.getElementById('confativ').value = nome;
        document.getElementById('id_ativar').value = id;
    }







    // Funcao para formatar para modelo de cpf
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

<script>
    var mensagem = "<?php echo $toast_mensagem; ?>";
    var tipo = "<?php echo $toast_tipo; ?>";

    if (mensagem) {
        var corFundo = tipo === "success" ?
            "linear-gradient(to right, #00b09b, #2cabd1ff)" :
            "linear-gradient(to right, #ff5f6d, #e562f7ff)";

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