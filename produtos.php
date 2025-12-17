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
                            <thead class="bg-lightborder-bottom">
                                <tr>
                                    <th class="ps-4 py-3 text-secondary border-0">Produtos</th>
                                    <th class="pe-4 py-3 text-secondary text-end border-0">Ações</th>
                                </tr>
                            </thead>

                            <?php 
                                $q = "SELECT * FROM tabela_produtos";

                                $busca = $banco->query($q);

                                if($busca->num_rows > 0){
                                    echo "<tbody>";
                                    while($reg = $busca->fetch_object()){
                                        echo '<tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="m-3">
                                                    <h6 class="m-0 fw-semibold"> x' . $reg->qtd_estoque . '</h6>
                                                </div>
                                                <div>
                                                    <h6 class="m-3 fw-semibold">' .    $reg->nome_produto . '</h6>
                                                </div>
                                                <div>
                                                   <h6 class="m-3 fw-semibold">R$' .    $reg->vlr_unitario . '</h6>
                                                </div>
                                            </div>
                                     </td> 
                                     <td class="pe-4 text-end">
                                            <input type="button" 
                                                class="btn btn-sm btn-outline-primary rounded-pill me-1" 
                                                value="Editar" 
                                                title="Editar" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditar"
                                                data-qtd="' . $reg->qtd_estoque . '"
                                                data-produto="' . $reg->nome_produto . '"
                                                data-preco="' . $reg->vlr_unitario . '"
                                                onclick="carregarDados(this)">';
                                    }{


                                }
                                }else{
                                    echo '<tbody><tr><td colspan="2" class="text-center">Nenhum produto cadastrado. </td></tr></tbody>';
                                }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>


</body>
</html>

<script>
    function carregarDados(botao){
        var produto = botao.getAttribute('data-produto');
        var qtd = botao.getAttribute('data-qtd');
        var preco = botao.getAttribute('data-preco');
    }
</script>