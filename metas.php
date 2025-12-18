<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Metas - AquaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<?php
    session_start();

    if (!isset($_SESSION['usuario'])) {
        header('location:login.php');
        exit();
    }

    require_once "config/banco.php";
    require "header.php";

    $toast_mensagem = "";
    $toast_tipo = "";

?>


<?php 

    if (isset($_POST['cadastrar_meta'])) {

        $func  = $_POST['funcionario'];
        $mes   = $_POST['mes'];
        $valor = $_POST['meta'];


        $mes_banco = $mes . "-01";

        $valor = str_replace('.', '', $valor);
        
        $valor = str_replace(',', '.', $valor);
        $data_atual = date('Y-m-01');

        $busca = "SELECT id FROM tabela_metas WHERE funcionario_meta = '$func' AND mes_meta = '$mes_banco'";
        $check = $banco->query($busca);

        if (empty($func)) {
            $toast_mensagem = "Erro: Selecione um funcionário!";
            $toast_tipo = "error";
        } elseif ($mes_banco < $data_atual) {
            $toast_mensagem = "Erro: Não é possível definir metas para meses passados!";
            $toast_tipo = "error";
        } elseif ($valor < 0) {
            $toast_mensagem = "Erro: O valor da meta não pode ser negativo!";
            $toast_tipo = "error";
        } elseif ($check->num_rows > 0) {
            $toast_mensagem = "Erro: Este funcionário JÁ possui uma meta para este mês!";
            $toast_tipo = "error";
        } else {
            $q = "INSERT INTO tabela_metas (funcionario_meta, mes_meta, vlr_meta) 
                VALUES ('$func', '$mes_banco', '$valor')";

            if ($banco->query($q)) {
                $toast_mensagem = "Meta definida com sucesso!";
                $toast_tipo = "success";
            } else {
                $toast_mensagem = "Erro ao salvar no banco de dados.";
                $toast_tipo = "error";
            }
        }
    }

?>


<?php 
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        if ($banco->query("DELETE FROM tabela_metas WHERE id = '$id'")) {
            $toast_mensagem = "Meta deletada com sucesso!";
            $toast_tipo = "success";
        } else {
            $toast_mensagem = "Erro ao deletar: " . $banco->error;
            $toast_tipo = "error";
        }
    }
?>


<?php 
    if(isset($_POST['editar_meta'])){
        $id = $_POST['id'];
        $vlr = $_POST['vlr'];

        $vlr = str_replace('.', '', $vlr);
        $vlr = str_replace(',', '.', $vlr);

        $q = "UPDATE tabela_metas SET vlr_meta = '$vlr' WHERE id = '$id'";

        if($banco->query($q)){
            $toast_mensagem = "Meta atualizada com sucesso!";
            $toast_tipo = "success";
        } else {
            $toast_mensagem = "Erro ao atualizar";
            $toast_tipo = "error";
        }
    }
?>


<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h2 class="fw-bold mb-1" style="color: #0d6efd;">Metas</h2>
                <p class="text-muted mb-0">Gerenciamento de metas</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrar_meta">
                <i class="fa-solid fa-plus me-2"></i>Definir meta
            </button>
        </div>

        <div class="card border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-secondary border-0" style="width: 28%;">Funcionário</th>
                                <th class="py-3 text-secondary border-0 " style="width: 15%;">Mês</th>
                                <th class="py-3 text-secondary border-0 ">Valor R$</th>
                                <th class="pe-4 py-3 text-secondary text-end border-0">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q = "SELECT tabela_metas.*, tabela_funcionarios.nome 
                                FROM tabela_metas 
                                INNER JOIN tabela_funcionarios 
                                ON tabela_metas.funcionario_meta = tabela_funcionarios.id";
                            $busca = $banco->query($q);

                            if ($busca->num_rows > 0) {
                                while ($reg = $busca->fetch_object()) {
                                    echo '<tr>
                                            <td class="align-middle ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                        <i class="fa-regular fa-user"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <span class="d-block fw-semibold text-dark">' . $reg->nome . '</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="align-middle text-muted fw-medium">
                                                <i class="fa-regular fa-calendar me-1"></i> ' . date('d/m/Y', strtotime($reg->mes_meta)) . '
                                            </td>

                                            <td class="align-middle">
                                                <small class="text-muted">R$</small>
                                                <span class="fw-bold text-success">' . number_format($reg->vlr_meta, 2, ',', '.') . '</span>
                                            </td>

                                            <td class="align-middle text-end">
                                                <button type="button" 
                                                    class="btn btn-sm btn-outline-primary rounded-pill"
                                                    title="Editar" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEditar_meta"
                                                    data-id="' . $reg->id . '"
                                                    data-valor="' . $reg->vlr_meta . '"
                                                    onclick="carregarDados(this)">
                                                    Editar
                                                </button>

                                                <a href="?id=' . $reg->id . '" 
                                                    class="btn btn-sm btn-outline-danger rounded-pill" 
                                                    title="Excluir">
                                                    Excluir
                                                </a>
                                            </td>
                                        </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center p-3">Nenhuma meta cadastrada.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCadastrar_meta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Definir Nova Meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <form method="post">

                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold" for="id_funcionario">FUNCIONÁRIO</label>
                            <select class="form-select form-select-sm bg-light" id="id_funcionario" name="funcionario" required>
                                <option value="" selected disabled>Selecione...</option>
                                <?php
                                    $q = "SELECT * FROM tabela_funcionarios WHERE data_demissao IS NULL";
                                    $busca = $banco->query($q);
                                    if ($busca->num_rows > 0) {
                                        while ($reg = $busca->fetch_object()) {
                                            echo "<option value='$reg->id'>$reg->nome</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold" for="mes_meta">MÊS </label>
                            <input type="month" class="form-control bg-light" id="mes_meta" name="mes" required>
                        </div>

                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold" for="vlr_meta">VALOR DA META (R$)</label>
                            <input type="text" class="form-control bg-light" id="vlr_meta" name="meta" placeholder="0,00" required>
                        </div>

                        <div class="modal-footer border-top-0 justify-content-center">
                            <button type="submit" name="cadastrar_meta" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                Salvar Meta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modalEditar_meta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Editar meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <form method="post">
                        <input type="hidden" name="id" id="id_edit">

                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold">VALOR DA META</label>
                            <input type="number" step="0.01" class="form-control bg-light" id="vlr_edit" name="vlr" required>
                        </div>

                        <div class="modal-footer border-top-0 justify-content-center">
                            <button type="submit" name="editar_meta" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        function carregarDados(botao) {
            
            var id = botao.getAttribute('data-id');
            var vlr = botao.getAttribute('data-valor');

            document.getElementById('id_edit').value = id;
            document.getElementById('vlr_edit').value = vlr;

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
</body>

</html>