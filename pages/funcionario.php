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

<?php
// session_start movido para o topo
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

        if (empty($cpf) || empty($nome)) {
            $toast_mensagem = "Erro: Preencha todos os campos!";
            $toast_tipo = "erro";
        } else {
            $q = "SELECT cpf FROM tabela_funcionarios WHERE cpf = '$cpf'";
            $busca = $banco->query($q);

            if ($busca->num_rows > 0) {
                $toast_mensagem = "Erro: Funcionário já cadastrado!";
                $toast_tipo = "erro";
            } else {
                $admissao = date('Y-m-d');
                if ($banco->query("INSERT INTO tabela_funcionarios (cpf, nome, data_contratacao) VALUES ('$cpf', '$nome', '$admissao')")) {
                    $toast_mensagem = "Funcionário cadastrado com sucesso!";
                    $toast_tipo = "sucesso";
                } else {
                    $toast_mensagem = "Erro ao cadastrar funcionário!";
                    $toast_tipo = "erro";
                }
            }
        }
    }
    ?>











    <!-- Lógica de editar funcionario -->


    <?php
    if (isset($_POST['editar_funcionario'])) {
        $id = $_POST['id_edit'];
        $nome = $_POST['nome_edit'];
        if (empty($nome)) {
            $toast_mensagem = "Erro: O nome não pode ser vazio!";
            $toast_tipo = "erro";
        } else {
            $q = "UPDATE tabela_funcionarios SET nome = '$nome' WHERE id = '$id'";
            if ($banco->query($q)) {
                $toast_mensagem = "Dados do funcionário atualizados!";
                $toast_tipo = "sucesso";
            } else {
                $toast_mensagem = "Erro ao atualizar funcionário!";
                $toast_tipo = "erro";
            }
        }
    }
    ?>











    <!--Lógica de desligamento -->


    <?php
    if (isset($_POST['desligar-funcionario'])) {
        $id = $_POST['id_desligamento'];
        $q = "UPDATE tabela_funcionarios SET data_demissao = NOW() WHERE id = '$id'";
        if ($banco->query($q)) {
            $toast_mensagem = "Funcionário desligado com sucesso!";
            $toast_tipo = "sucesso";
        } else {
            $toast_mensagem = "Erro ao desligar funcionário!";
            $toast_tipo = "erro";
        }
    }
    ?>








    <!--Lógica de ativação -->


    <?php
    if (isset($_POST['ativar-funcionario'])) {
        $id = $_POST['id_ativar'];
        $q = "UPDATE tabela_funcionarios SET data_demissao = NULL WHERE id = '$id'";
        if ($banco->query($q)) {
            $toast_mensagem = "Funcionário reativado com sucesso!";
            $toast_tipo = "sucesso";
        } else {
            $toast_mensagem = "Erro ao reativar funcionário!";
            $toast_tipo = "erro";
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
                    $q = "SELECT * FROM tabela_funcionarios ORDER BY nome";
                    $busca = $banco->query($q);

                    if ($busca->num_rows > 0) {
                        echo '<tbody>';
                        while ($reg = $busca->fetch_object()) {
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
                                                data-id="' . $reg->id . '"
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Detalhes do Funcionário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 px-4">
                <div class="row">
                    <div class="col-md-4 mb-3 text-start">
                        <label class="form-label text-muted small fw-bold">NOME</label>
                        <input type="text" class="form-control bg-light" id="visualizar_nome" readonly>
                    </div>
                    <div class="col-md-4 mb-3 text-start">
                        <label class="form-label text-muted small fw-bold">CPF</label>
                        <input type="text" class="form-control bg-light" id="visualizar_cpf" readonly>
                    </div>
                    <div class="col-md-4 mb-3 text-start">
                        <label class="form-label text-muted small fw-bold">ADMISSÃO</label>
                        <input type="text" class="form-control bg-light" id="visualizar_contratacao" readonly>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-bold text-primary mb-3">Últimas Metas</h6>
                <div class="table-responsive mb-4" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-hover" id="tabela_metas_func">
                        <thead>
                            <tr>
                                <th>Mês</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <h6 class="fw-bold text-primary mb-3">Últimas Vendas</h6>
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-hover" id="tabela_vendas_func">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Produto</th>
                                <th>Qtd</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
                        <label for="funcionario_edit" class="form-label text-muted small fw-bold">NOME COMPLETO <span class="text-danger">*</span></label>
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
                        <label for="cpf" class="form-label text-muted small fw-bold">CPF <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-primary">
                                <i class="fa-regular fa-file"></i>
                            </span>
                            <input type="text" oninput="mascara(this)" class="form-control bg-light border-start-0 ps-0" id="cpf" name="cpf" minlength="11" required placeholder="Digite o CPF">
                        </div>
                    </div>

                    <div class="mb-3 text-start">
                        <label for="name" class="form-label text-muted small fw-bold">NOME COMPLETO <span class="text-danger">*</span></label>
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











<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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

        var id = botao.getAttribute('data-id');

        document.getElementById('visualizar_nome').value = nome;
        document.getElementById('visualizar_cpf').value = cpf;
        document.getElementById('visualizar_contratacao').value = contratacao ? contratacao : "";

        // Limpar tabelas
        const tbodyMetas = document.querySelector('#tabela_metas_func tbody');
        const tbodyVendas = document.querySelector('#tabela_vendas_func tbody');
        tbodyMetas.innerHTML = '<tr><td colspan="2" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Carregando...</td></tr>';
        tbodyVendas.innerHTML = '<tr><td colspan="3" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Carregando...</td></tr>';

        // Fetch AJAX
        const formData = new FormData();
        formData.append('id', id);

        fetch('../pages/get_employee_details.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                tbodyMetas.innerHTML = '';
                tbodyVendas.innerHTML = '';

                // Preencher Metas
                if (data.metas.length > 0) {
                    data.metas.forEach(meta => {
                        let row = `<tr>
                        <td>${new Date(meta.mes_meta).toLocaleDateString('pt-BR', {timeZone: 'UTC', month:'2-digit', year:'numeric'})}</td>
                        <td class="text-success fw-bold">R$ ${parseFloat(meta.vlr_meta).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                    </tr>`;
                        tbodyMetas.innerHTML += row;
                    });
                } else {
                    tbodyMetas.innerHTML = '<tr><td colspan="2" class="text-center text-muted">Nenhuma meta recente.</td></tr>';
                }

                // Preencher Vendas
                if (data.vendas.length > 0) {
                    data.vendas.forEach(venda => {
                        let row = `<tr>
                        <td>${new Date(venda.data_venda).toLocaleDateString('pt-BR', {timeZone: 'UTC'})}</td>
                        <td>${venda.nome_produto}</td>
                         <td>${venda.qtd_vendido}</td>
                    </tr>`;
                        tbodyVendas.innerHTML += row;
                    });
                } else {
                    tbodyVendas.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Nenhuma venda recente.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                tbodyMetas.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Erro ao carregar.</td></tr>';
                tbodyVendas.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Erro ao carregar.</td></tr>';
            });

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
        i.setAttribute("maxlength", "14");
        if (v.length == 3 || v.length == 7) i.value += ".";
        if (v.length == 11) i.value += "-";
    }
</script>

<script>
    var mensagem = "<?php echo $toast_mensagem; ?>";
    var tipo = "<?php echo $toast_tipo; ?>";

    if (mensagem) {
        var corFundo = tipo === "sucesso" ?
            "linear-gradient(to right, #11998e, #38ef7d)" :
            "linear-gradient(to right, #ff416c, #ff4b2b)";

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