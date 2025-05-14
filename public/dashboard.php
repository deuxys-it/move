<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$nome = $_SESSION['user']['nome'] ?? 'Usuário';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Morya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-black text-white min-vh-100 d-flex flex-column">
<?php include 'header.php'; ?>

<main class="flex-grow-1 d-flex justify-content-center align-items-start">
    <div class="container">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <a href="disparo.php" class="card bg-light text-dark text-center p-4 h-100 text-decoration-none">
                    <div class="card-body">
                        <h5 class="card-title">Disparo de E-mails</h5>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="certidoes.php" class="card bg-light text-dark text-center p-4 h-100 text-decoration-none">
                    <div class="card-body">
                        <h5 class="card-title">Certidões</h5>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="dash.php" class="card bg-light text-dark text-center p-4 h-100 text-decoration-none">
                    <div class="card-body">
                        <h5 class="card-title">Dashboard</h5>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="https://drive.google.com/drive/u/1/folders/1nJwPCvNdgY16r8XKjLtDCDt1nCLg2JJh" class="card bg-light text-dark text-center p-4 h-100 text-decoration-none" target="_blank">
                    <div class="card-body">
                        <h5 class="card-title">Nomeclatura</h5>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="https://drive.google.com/drive/folders/13p1pg9iW7s-gg354nSrgsT0l4OTe9BvG" class="card bg-light text-dark text-center p-4 h-100 text-decoration-none" target="_blank">
                    <div class="card-body">
                        <h5 class="card-title">Plano de Mídia</h5>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="#" class="card bg-light text-dark text-center p-4 h-100 text-decoration-none">
                    <div class="card-body">
                        <h5 class="card-title">Checagem de Orçamento</h5>
                    </div>
                </a>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
