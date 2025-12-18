<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['usuario'])) {
        header("location: ../login/login.php");
        exit;
    }
    ?>

    <!-- navbar !-->

    <?php
    require "../includes/header.php";
    ?>

    <!-- Cards !-->

    <div class="container py-5">
        <section class="mb-5 text-center">
            <h2 class="fw-bold mb-3" style="color: #005c97;">Soluções Completas em Saneamento</h2>
            <p class="lead text-muted mx-auto mb-5" style="max-width: 800px;">
                Nosso compromisso é garantir segurança hídrica, eficiência operacional e inovação em todas as etapas do processo. Trabalhamos com tecnologias modernas, equipamentos certificados e equipes altamente qualificadas.</p>
            <div class="row g-5 mt-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-card p-3">
                        <div class="card-body">
                            <div class="mb-4">
                                <i class="fa-solid fa-droplet fa-4x" style="color: #4facfe;"></i>
                            </div>
                            <h5 class="card-title fw-bold" style="color: #005c97;">Abastecimento</h5>
                            <p class="card-text text-muted mt-3">Garantimos o fornecimento contínuo e seguro de água, desde a captação até a distribuição final, utilizando tecnologias eficientes que reduzem perdas e asseguram a qualidade do abastecimento para comunidades, indústrias e empreendimentos.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-card p-3">
                        <div class="card-body">
                            <div class="mb-4">
                                <i class="fa-solid fa-chart-line fa-4x" style="color: #4facfe;"></i>
                            </div>
                            <h5 class="card-title fw-bold" style="color: #005c97;">Monitoramento</h5>
                            <p class="card-text text-muted mt-3">Utilizamos sistemas inteligentes e monitoramento em tempo real para acompanhar redes, reservatórios e indicadores operacionais, permitindo decisões rápidas, prevenção de falhas e otimização dos recursos hídricos.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-card p-3">
                        <div class="card-body">
                            <div class="mb-4">
                                <i class="fa-solid fa-microscope fa-4x" style="color: #4facfe;"></i>
                            </div>
                            <h5 class="card-title fw-bold" style="color: #005c97;">Tratamento</h5>
                            <p class="card-text text-muted mt-3">Aplicamos processos avançados de tratamento e purificação da água, seguindo rigorosamente as normas ambientais e sanitárias, assegurando um recurso limpo, potável e confiável para todos os usos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="position-relative mt-5 pt-5 pb-5 text-center bg-white rounded-5 shadow-sm" style="overflow: hidden;">
            <div class="container position-relative z-1 py-5">
                <h2 class="display-6 fw-bold mb-4" style="color: #005c97;">Vamos começar?</h2>
                <figure class="mb-4">
                    <blockquote class="blockquote">
                        <p class="fst-italic text-muted">"O sucesso não pode ser herdado, o sucesso é o resultado de ações corretas."</p>
                    </blockquote>
                </figure>
                <a href="#" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">Agendar demonstração</a>
            </div>
        </section>

    </div>
</body>

</html>