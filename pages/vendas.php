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
                                    ORDER BY tabela_vendas.data_venda DESC";

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




    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
</body>
</html>