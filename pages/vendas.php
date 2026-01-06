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
if (!isset($_SESSION['usuario'])) {
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
    $produtos_json = $_POST['produtos_json'];

    $lista_produtos = json_decode($produtos_json, true);

    if (empty($id_func) || empty($data)) {
        $toast_mensagem = "Erro: Preencha todos os campos obrigatórios!";
        $toast_tipo = "erro";
    } elseif (empty($lista_produtos)) {
        $toast_mensagem = "Erro: Nenhum produto adicionado à venda!";
        $toast_tipo = "erro";
    } else {
        $estoque_ok = true;
        foreach ($lista_produtos as $item) {
            $id_prod = $item['id'];
            $qtd = $item['qtd'];
            $busca = $banco->query("SELECT qtd_estoque, nome_produto FROM tabela_produtos WHERE id = '$id_prod'");
            $produto = $busca->fetch_object();

            if ($qtd > $produto->qtd_estoque) {
                $estoque_ok = false;
                $toast_mensagem = "Erro: Estoque insuficiente para " . $produto->nome_produto;
                $toast_tipo = "erro";
                break;
            }
        }

        if ($estoque_ok) {

            $banco->query("INSERT INTO tabela_vendas (funcionario_vendas, data_venda) VALUES ('$id_func', '$data')");
            $id_venda = $banco->insert_id;


            foreach ($lista_produtos as $item) {
                $id_prod = $item['id'];
                $qtd = $item['qtd'];

                $banco->query("INSERT INTO tabela_vendas_produtos (id_venda, id_produto, qtd_vendido) VALUES ('$id_venda', '$id_prod', '$qtd')");
                $banco->query("UPDATE tabela_produtos SET qtd_estoque = qtd_estoque - '$qtd' WHERE id = '$id_prod'");
            }

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
                                    <th class="py-3 text-secondary border-0" style="width: 35%;">Funcionário</th>
                                    <th class="py-3 text-secondary border-0" style="width: 20%;">Data</th>
                                    <th class="py-3 text-secondary border-0" style="width: 35%;">Produtos</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $q = "SELECT 
                                        SUM(tabela_vendas_produtos.qtd_vendido) as total_itens,
                                        tabela_funcionarios.nome AS funcionario_vendas,
                                        tabela_vendas.data_venda,
                                        GROUP_CONCAT(CONCAT('<small class=\"fw-bold text-primary\">', tabela_vendas_produtos.qtd_vendido, 'x</small> ', tabela_produtos.nome_produto) SEPARATOR '<br>') AS lista_produtos
                                        FROM tabela_vendas
                                        INNER JOIN tabela_funcionarios ON tabela_vendas.funcionario_vendas = tabela_funcionarios.id
                                        INNER JOIN tabela_vendas_produtos ON tabela_vendas.id = tabela_vendas_produtos.id_venda
                                        INNER JOIN tabela_produtos ON tabela_vendas_produtos.id_produto = tabela_produtos.id
                                        GROUP BY tabela_vendas.id 
                                        ORDER BY tabela_vendas.data_venda DESC";

                                $busca = $banco->query($q);

                                if ($busca->num_rows > 0) {
                                    while ($reg = $busca->fetch_object()) {
                                        echo '<tr>
                                                
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
                                                    <span class="d-block text-dark" style="line-height: 1.6;">' . $reg->lista_produtos . '</span>
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
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold">Registrar Venda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body py-4 px-4">
                        <form method="post" id="formVenda" onsubmit="return prepararEnvio()">
                            <input type="hidden" name="produtos_json" id="produtos_json">

                            <div class="row">
                                <div class="col-md-6 mb-3 text-start">
                                    <label for="func_venda" class="form-label text-muted small fw-bold">FUNCIONÁRIO <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-select bg-light border-start-0 ps-0" id="func_venda" name="funcionario" required>
                                            <option value="" selected disabled>Selecione...</option>
                                            <?php
                                            $q = "SELECT * FROM tabela_funcionarios WHERE data_demissao IS NULL ORDER BY nome";
                                            $busca = $banco->query($q);
                                            $busca->data_seek(0); 
                                            while ($reg = $busca->fetch_object()) {
                                                echo "<option value='$reg->id'>$reg->nome</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3 text-start">
                                    <label for="data_venda" class="form-label text-muted small fw-bold">DATA DA VENDA <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="date" class="form-control bg-light border-start-0 ps-0" id="data_venda" name="data_venda" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h6 class="text-start fw-bold text-primary">Adicionar Itens</h6>

                            <div class="row align-items-end mb-3">
                                <div class="col-md-6 text-start">
                                    <label for="prod_venda" class="form-label text-muted small fw-bold">PRODUTO <span class="text-danger">*</span></label>
                                    <select class="form-select bg-light" id="prod_venda">
                                        <option value="" selected disabled>Selecione...</option>
                                        <?php
                                        $q = "SELECT * FROM tabela_produtos WHERE qtd_estoque > 0 ORDER BY nome_produto";
                                        $busca = $banco->query($q);
                                        $busca->data_seek(0);
                                        while ($reg = $busca->fetch_object()) {
                                            echo "<option value='$reg->id' data-nome='$reg->nome_produto'>$reg->nome_produto (Estoque: $reg->qtd_estoque)</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 text-start">
                                    <label for="qtd_venda" class="form-label text-muted small fw-bold">QTD <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control bg-light" id="qtd_venda" min="1" placeholder="0">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-success w-100 rounded-pill" onclick="adicionarProduto()">
                                        <i class="fa-solid fa-plus me-1"></i> Add
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive mb-3" style="max-height: 150px; overflow-y: auto;">
                                <table class="table table-sm table-bordered" id="tabelaItens">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Produto</th>
                                            <th style="width: 80px;">Qtd</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Itens adicionados via JS -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="modal-footer border-top-0 justify-content-center">
                                <button type="submit" name="registrar_venda" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Registrar Venda</button>
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

<script>
    let listaProdutos = [];

    function adicionarProduto() {
        const selectProd = document.getElementById('prod_venda');
        const inputQtd = document.getElementById('qtd_venda');

        const idProd = selectProd.value;
        const nomeProd = selectProd.options[selectProd.selectedIndex].getAttribute('data-nome');
        const qtd = parseInt(inputQtd.value);

        if (!idProd || !qtd || qtd <= 0) {
            alert("Selecione um produto e uma quantidade válida.");
            return;
        }

        // Verificar se já existe na lista
        const existe = listaProdutos.find(p => p.id === idProd);
        if (existe) {
            existe.qtd += qtd;
        } else {
            listaProdutos.push({
                id: idProd,
                nome: nomeProd,
                qtd: qtd
            });
        }

        atualizarTabela();

        selectProd.value = "";
        inputQtd.value = "";
    }

    function removerProduto(index) {
        listaProdutos.splice(index, 1);
        atualizarTabela();
    }

    function atualizarTabela() {
        const tbody = document.querySelector('#tabelaItens tbody');
        tbody.innerHTML = "";

        listaProdutos.forEach((p, index) => {
            let row = `<tr>
                <td>${p.nome}</td>
                <td>${p.qtd}</td>
                <td><button type="button" class="btn btn-sm btn-danger py-0 px-2" onclick="removerProduto(${index})">&times;</button></td>
            </tr>`;
            tbody.innerHTML += row;
        });
    }

    function prepararEnvio() {
        if (listaProdutos.length === 0) {
            alert("Adicione pelo menos um produto à venda!");
            return false;
        }
        document.getElementById('produtos_json').value = JSON.stringify(listaProdutos);
        return true;
    }
</script>