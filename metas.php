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
<body>
    <?php  
        session_start();
        if(!isset($_SESSION['usuario'])){
            header('location:login.php');
            exit();
        }

        require_once "config/banco.php";
        require "header.php";
    ?>




        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
                <div>
                    <h2 class="fw-bold mb-1" style="color: #0d6efd;">Metas</h2>
                    <p class="text-muted mb-0">Gerenciamento de metas</p>
                </div>
                <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrar_product">
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
                                
                                <th class="py-3 text-secondary border-0 " style="width: 15%;" >Mês</th>

                                <th class="py-3 text-secondary border-0 " >Valor R$</th>
                                
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

                                if($busca->num_rows > 0){
                                    while($reg = $busca->fetch_object()){
                                        echo '<tr>
                                                <td class="align-middle ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                            <i class="fa-regular fa-user"></i>
                                                        </div>
                                                        <div class="ms-3">
                                                            <span class="d-block fw-bold text-dark">' . $reg->nome . '</span>
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

                                                <td class="align-middle text-end pe-4">
                                                    <button type="button" 
                                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                                        title="Editar" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEditar_meta"
                                                        data-id="' . $reg->id . '"
                                                        data-funcionario="' . $reg->funcionario_meta . '"
                                                        data-mes="' . $reg->mes_meta . '"
                                                        data-valor="' . $reg->vlr_meta . '"
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
</body>
</html>