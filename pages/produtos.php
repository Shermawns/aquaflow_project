<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Gerenciar Produtos - AquaFlow</title>
</head>

<body>

    <?php

        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('location:../login/login.php');
            exit();
        }

        require_once "../config/banco.php";
        require_once "../config/function.php";
        require "../includes/header.php";

        $toast_mensagem = "";
        $toast_tipo = "";
        
    ?>



    <!-- Lógica de editar produtos -->


    <?php
    if (isset($_POST['editar_produto'])) {

        $id    = $_POST['id'];
        $name  = $_POST['produto'];
        $preco = $_POST['preco'];
        $qtd   = $_POST['qtd'];

        $preco = str_replace(',', '.', $preco);


        $sql = "SELECT qtd_estoque FROM tabela_produtos WHERE id = '$id'";
        $res = $banco->query($sql);
        $reg = $res->fetch_object();

        if ($qtd < $reg->qtd_estoque) {
            $toast_mensagem = "Erro: A quantidade não pode ser menor que o estoque atual!";
            $toast_tipo = "erro";
        } else {
            $sql = "UPDATE tabela_produtos SET nome_produto = '$name', vlr_unitario = '$preco', qtd_estoque = '$qtd' WHERE id = '$id'";
            if ($banco->query($sql)) {
                $toast_mensagem = "Produto atualizado com sucesso!";
                $toast_tipo = "sucesso";
            } else {
                $toast_mensagem = "Erro ao atualizar no banco!";
                $toast_tipo = "erro";
            }
        }
    }
    ?>




    <!-- Lógica de cadastrar produtos -->

    <?php
    if (isset($_POST['cadastrar_produto'])) {

        $name  = $_POST['produto'];
        $preco = $_POST['preco'];
        $qtd   = $_POST['qtd'];


        $check_sql = "SELECT id FROM tabela_produtos WHERE nome_produto = '$name'";
        $busca = $banco->query($check_sql);

        if ($qtd < 0) {
            $toast_mensagem = "Erro: Não é possivel cadastrar um produto com quantidade negativa!";
            $toast_tipo = "erro";
        } else {
            if ($busca->num_rows > 0) {
                $toast_mensagem = "Erro: Já existe um produto com este nome!";
                $toast_tipo = "erro";
            } else {
                $q = "INSERT INTO tabela_produtos (nome_produto, vlr_unitario, qtd_estoque)
                        VALUES ('$name', '$preco', '$qtd')";

                if ($banco->query($q)) {
                    $toast_mensagem = "Produto cadastrado com sucesso!";
                    $toast_tipo = "sucesso";
                } else {
                    $toast_mensagem = "Erro ao inserir no banco de dados.";
                    $toast_tipo = "erro";
                }
            }
        }
    }
    ?>





    <!-- Caixa de listar produtos -->


    <div class="container py-5">


        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h2 class="fw-bold mb-1" style="color: #0d6efd;">Produtos</h2>
                <p class="text-muted mb-0">Gerenciamento de produtos</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrar_product">
                <i class="fa-solid fa-plus me-2"></i>Cadastrar produto
            </button>
        </div>



        <div class="card border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-secondary border-0" style="width: 10%;">Qtd</th>

                                <th class="py-3 text-secondary border-0" style="width: 20%;">Produto</th>

                                <th class="py-3 text-secondary border-0 ">Preço R$</th>

                                <th class="pe-4 py-3 text-secondary text-end border-0">Ações</th>
                            </tr>
                        </thead>
                        <tbody>



                            <!-- Lógica de listar produtos -->



                            <?php
                            $q = "SELECT * FROM tabela_produtos ORDER BY nome_produto";
                            $busca = $banco->query($q);

                            if ($busca->num_rows > 0) {
                                while ($reg = $busca->fetch_object()) {
                                    echo '<tr>
                                            <td class="align-middle ps-4">
                                                <span class="badge bg-light text-secondary px-1 py-4">
                                                    <i class="fa-solid fa-layer-group me-1"></i> ' . $reg->qtd_estoque . ' un.
                                                </span>
                                            </td>

                                            <td class="align-middle">
                                                <div class="d-flex align-items-center"> 
                                                    <div class="ms-3">
                                                        <span class="d-block fw-semibold text-dark">' . $reg->nome_produto . '</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="align-middle">
                                                <small class="text-muted">R$</small> 
                                                <span class="fw-bold text-success">' . number_format($reg->vlr_unitario, 2, ',', '.') . '</span>
                                            </td>

                                            <td class="align-middle text-end pe-4">
                                                <button type="button" 
                                                    class="btn btn-sm btn-outline-primary rounded-pill"
                                                    title="Editar" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEditar_product"
                                                    data-id="' . $reg->id . '"
                                                    data-qtd="' . $reg->qtd_estoque . '"
                                                    data-produto="' . $reg->nome_produto . '"
                                                    data-preco="' . $reg->vlr_unitario . '"
                                                    onclick="carregarDados(this)">
                                                    Editar
                                                </button>
                                            </td>
                                        </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center p-3">Nenhum produto cadastrado.</td></tr>';
                            }



                             
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>





    <!-- Modal de editar produtos -->


    <div class="modal fade" id="modalEditar_product" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Editar Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <form method="post">
                        <input type="hidden" name="id" id="id_edit">

                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold">NOME DO PRODUTO</label>
                            <input type="text" class="form-control bg-light" id="produto_edit" name="produto" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold">PREÇO UNITÁRIO</label>
                            <input type="number" step="0.01" class="form-control bg-light" id="preco_edit" name="preco" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold">QUANTIDADE</label>
                            <input type="number" class="form-control bg-light" id="qtd_edit" name="qtd" required>
                        </div>
                        <div class="modal-footer border-top-0 justify-content-center">
                            <button type="submit" name="editar_produto" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>








    <!-- Modal de cadastrar produtos -->


    <div class="modal fade" id="modalCadastrar_product" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Cadastrar produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <form method="post">
                        <input type="hidden" name="id" id="id_edit">

                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold" for="produto_edit">NOME DO PRODUTO</label>
                            <input placeholder="Digite o nome do produto" type="text" class="form-control bg-light" id="produto_edit" name="produto" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold" for="preco_edit">PREÇO UNITÁRIO</label>
                            <input placeholder="Digite o valor" type="number" step="0.01" class="form-control bg-light" id="preco_edit" name="preco" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small fw-bold" for="qtd_edit">QUANTIDADE</label>
                            <input placeholder="Quantidade em estoque" type="number" class="form-control bg-light" id="qtd_edit" name="qtd" required>
                        </div>
                        <div class="modal-footer border-top-0 justify-content-center">
                            <button type="submit" name="cadastrar_produto" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">Salvar Alterações</button>
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
    function carregarDados(botao) {
        var id = botao.getAttribute('data-id');
        var produto = botao.getAttribute('data-produto');
        var qtd = botao.getAttribute('data-qtd');
        var preco = botao.getAttribute('data-preco');

        document.getElementById('id_edit').value = id;
        document.getElementById('produto_edit').value = produto;
        document.getElementById('qtd_edit').value = qtd;
        document.getElementById('preco_edit').value = preco;
    }
</script>





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