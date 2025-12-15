<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - AquaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header("location: login.php");
            exit;
        }

        require_once "config/banco.php";
        require_once "config/function.php";
        require "header.php";
    ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
            <div>
                <h2 class="fw-bold mb-1" style="color: #0d6efd;">Usuários</h2>
                <p class="text-muted mb-0">Gerencie o acesso ao sistema</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCadastrar">
                <i class="fa-solid fa-plus me-2"></i>Novo Usuário
            </button>
        </div>
        <div class="card border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-4 py-3 text-secondary border-0">Usuário</th>
                                <th class="pe-4 py-3 text-secondary text-end border-0">Ações</th>
                            </tr>
                        </thead>
                        <?php
                            $q = "SELECT * FROM tabela_usuarios";
                            $busca = $banco->query($q);

                            if($busca->num_rows > 0){
                                echo '<tbody>'; 
                                while($reg = $busca->fetch_object()){
                                    echo '<tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fa-regular fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">' . $reg->usuario . '</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <button class="btn btn-sm btn-outline-primary border-0 rounded-circle me-1" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditar_func">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Excluir" onclick="confirmarExclusao(\'' . $reg->usuario . '\')">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>';
                                }
                                echo '</tbody>';
                            } else {
                                echo '<tbody><tr><td colspan="2" class="text-center">Nenhum usuário cadastrado.</td></tr></tbody>';
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>