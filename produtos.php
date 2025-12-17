<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Gerenciar Produtos - AquaFlow</title>
</head>
<body>

    <?php 
        session_start();
        if(!isset($_SESSION['usuario'])){
            header('location:login.php');
            exit();
        }

        require_once "config/banco.php";
        require_once "config/function.php";
        require "header.php";
    ?>


    
        <?php 
            if(isset($_POST['editar_produto'])){

                    $id    = $_POST['id'];
                    $name  = $_POST['produto'];
                    $preco = $_POST['preco'];
                    $qtd   = $_POST['qtd'];

                    
                    $sql = "SELECT qtd_estoque FROM tabela_produtos WHERE id = '$id'";
                    $res = $banco->query($sql);
                    $reg = $res->fetch_object();

                    if($qtd < $reg->qtd_estoque){
                        echo "pode nao";
                    } else {
                        $sql = "UPDATE tabela_produtos 
                                SET nome_produto = '$name',
                                    vlr_unitario = '$preco',
                                    qtd_estoque = '$qtd'
                                WHERE id = '$id'";
                        $banco->query($sql);
                    }
            }

        ?>


        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
                <div>
                    <h2 class="fw-bold mb-1" style="color: #0d6efd;">Produtos</h2>
                    <p class="text-muted mb-0">Gerenciamento de produtos</p>
                </div>
                <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
                <i class="fa-solid fa-plus me-2"></i>Cadastrar produto
                </button>
            </div>

        <div class="card border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">                            
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="ps-4 py-3 text-secondary border-0">Produtos</th>
                                    <th class="pe-4 py-3 text-secondary text-end border-0">Ações</th>
                                </tr>
                            </thead>
                            <tbody> <?php 
                                $q = "SELECT * FROM tabela_produtos";
                                $busca = $banco->query($q);

                                if($busca->num_rows > 0){
                                    while($reg = $busca->fetch_object()){
                                        echo '<tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="m-3">
                                                    <h6 class="m-0 fw-semibold"> x' . $reg->qtd_estoque . '</h6>
                                                </div>
                                                <div>
                                                    <h6 class="m-3 fw-semibold">' . $reg->nome_produto . '</h6>
                                                </div>
                                                <div>
                                                <h6 class="m-3 fw-semibold">R$' . $reg->vlr_unitario . '</h6>
                                                </div>
                                            </div>
                                        </td> 
                                        <td class="pe-4 text-end">
                                            <input type="button" 
                                                class="btn btn-sm btn-outline-primary rounded-pill me-1" 
                                                value="Editar" 
                                                title="Editar" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditar_product"
                                                data-id="' . $reg->id . '"
                                                data-qtd="' . $reg->qtd_estoque . '"
                                                data-produto="' . $reg->nome_produto . '"
                                                data-preco="' . $reg->vlr_unitario . '"
                                                onclick="carregarDados(this)">
                                        </td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="2" class="text-center">Nenhum produto cadastrado. </td></tr>';
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<script>
    function carregarDados(botao){
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