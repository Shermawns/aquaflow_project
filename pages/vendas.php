<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Vendas - AquaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<?php 
    session_start();
    if(!isset($_SESSION['usuario'])){
        header('location:../login/login.php');
        exit();
    }

    require_once "../config/banco.php";
    require "../includes/header.php";

    $toast_mensagem = "";
    $toast_tipo = "";
?>


<?php
if (isset($_POST['registrar_venda'])) {
    $id_func = $_POST['funcionario'];
    $data = $_POST['data_venda'];
    $id_prod = $_POST['produto_id'];
    $qtd = $_POST['qtd_produto'];

    if (empty($id_func) || empty($data) || empty($id_prod) || empty($qtd)) {
        $toast_mensagem = "Erro: Todos os campos são obrigatórios!";
        $toast_tipo = "erro";
    } else {
        $stmt = $banco->prepare("SELECT qtd_estoque FROM tabela_produtos WHERE id = ?");
        $stmt->bind_param("i", $id_prod);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $produto = $resultado->fetch_object();

        if ($qtd > $produto->qtd_estoque) {
            $toast_mensagem = "Erro: Estoque insuficiente! Disponível: " . $produto->qtd_estoque;
            $toast_tipo = "erro";
        } else {
            $stmt_vendas = $banco->prepare("INSERT INTO tabela_vendas (funcionario_vendas, data_venda) VALUES (?, ?)");
            $stmt_vendas->bind_param("is", $id_func, $data);
            $stmt_vendas->execute();
            
            $id_venda = $banco->insert_id;

            $stmt_itens = $banco->prepare("INSERT INTO tabela_vendas_produtos (id_venda, id_produto, qtd_vendido) VALUES (?, ?, ?)");
            $stmt_itens->bind_param("iii", $id_venda, $id_prod, $qtd);
            $stmt_itens->execute();

            $stmt_estoque = $banco->prepare("UPDATE tabela_produtos SET qtd_estoque = qtd_estoque - ? WHERE id = ?");
            $stmt_estoque->bind_param("ii", $qtd, $id_prod);
            $stmt_estoque->execute();

            $toast_mensagem = "Venda registrada com sucesso!";
            $toast_tipo = "sucesso";
        }
    }
}
?>



<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h2 class="fw-bold mb-1" style="color: #0d6efd;">Vendas</h2>
                <p class="text-muted mb-0">Gerenciamento de vendas</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrar_venda">
                <i class="fa-solid fa-plus me-2"></i>Registrar Venda
            </button>
        </div>

        <div class="card border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-secondary border-0" style="width: 14%;">Qtd Vendida</th>
                                <th class="py-3 text-secondary border-0" style="width: 35%;">Funcionário</th>
                                <th class="py-3 text-secondary border-0" style="width: 20%;">Data</th>
                                <th class="py-3 text-secondary border-0" style="width: 35%;">Produto</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                                $q = "SELECT 
                                        tabela_vendas_produtos.qtd_vendido,
                                        tabela_funcionarios.nome AS funcionario_vendas,
                                        tabela_vendas.data_venda,
                                        tabela_produtos.nome_produto AS id_produto
                                    FROM tabela_vendas
                                    INNER JOIN tabela_funcionarios ON tabela_vendas.funcionario_vendas = tabela_funcionarios.id
                                    INNER JOIN tabela_vendas_produtos ON tabela_vendas.id = tabela_vendas_produtos.id_venda
                                    INNER JOIN tabela_produtos ON tabela_vendas_produtos.id_produto = tabela_produtos.id
                                    ORDER BY tabela_vendas_produtos.qtd_vendido DESC";

                                $busca = $banco->query($q);

                            // Lógica de listar todas as vendas

                            if ($busca->num_rows > 0) {
                                while ($reg = $busca->fetch_object()) {
                                    echo '<tr>
                                            <td class="align-middle ps-4">
                                                <span class="badge bg-light text-secondary px-1 py-4">
                                                    <i class="fa-solid fa-layer-group me-1"></i> ' . $reg->qtd_vendido . ' un.
                                                </span>
                                            </td>
                                            
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fa-regular fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold text-dark">' . $reg->funcionario_vendas . '</h6>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="align-middle text-muted fw-medium">
                                                <i class="fa-regular fa-calendar me-1"></i> ' . date('d/m/Y', strtotime($reg->data_venda)) . '
                                            </td>

                                             <td class="align-middle text-muted fw-medium">
                                                <span class="d-block fw-semibold text-dark">' . $reg->id_produto . '</span>
                                            </td>

                                            </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center p-3">Nenhuma venda registrada.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


       <div class="modal fade" id="modalCadastrar_venda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Registrar Venda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4 px-4">
                    <form method="post">
                        
                        <div class="mb-3 text-start">
                            <label for="func_venda" class="form-label text-muted small fw-bold">FUNCIONÁRIO</label>
                            <div class="input-group">
                                <select class="form-select bg-light border-start-0 ps-0" id="func_venda" name="funcionario" required>
                                    <option value="" selected disabled>Selecione...</option>
                                    <?php
                                        $q = "SELECT * FROM tabela_funcionarios WHERE data_demissao IS NULL ORDER BY nome";
                                        $busca = $banco->query($q);
                                        while ($reg = $busca->fetch_object()) {
                                            echo "<option value='$reg->id'>$reg->nome</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 text-start">
                            <label for="data_venda" class="form-label text-muted small fw-bold">DATA DA VENDA</label>
                            <div class="input-group">
                                <input type="date" class="form-control bg-light border-start-0 ps-0" id="data_venda" name="data_venda" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3 text-start">
                            <label for="prod_venda" class="form-label text-muted small fw-bold">PRODUTO</label>
                            <div class="input-group">
                                <select class="form-select bg-light border-start-0 ps-0" id="prod_venda" name="produto_id" required>
                                    <option value="" selected disabled>Selecione...</option>
                                    <?php
                                    $q = "SELECT * FROM tabela_produtos WHERE qtd_estoque > 0 ORDER BY nome_produto";
                                    $busca = $banco->query($q);
                                    while ($reg = $busca->fetch_object()) {
                                        echo "<option value='$reg->id'>$reg->nome_produto (Estoque: $reg->qtd_estoque)</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 text-start">
                            <label for="qtd_venda" class="form-label text-muted small fw-bold">QUANTIDADE</label>
                            <div class="input-group">
                                <input type="number" class="form-control bg-light border-start-0 ps-0" id="qtd_venda" name="qtd_produto" min="1" placeholder="Digite a quantidade" required>
                            </div>
                        </div>

                        <div class="modal-footer border-top-0 justify-content-center">
                            <button type="submit" name="registrar_venda" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Registrar</button>
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
    var mensagem = "<?php echo $toast_mensagem; ?>";
    var tipo = "<?php echo $toast_tipo; ?>";

    if (mensagem) {
        var corFundo = tipo === "sucesso" ?
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
