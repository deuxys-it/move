<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Morya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #000 !important; }
    </style>
</head>
<body class="text-white min-vh-100">
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav class='navbar navbar-expand-lg navbar-dark bg-black border-bottom border-white mb-4 p-4'>
    <div class="container">
        <a href='/public/dashboard.php'><img src='/public/logo.png' class='img-fluid' style='max-width: 100px;'></a>
        <ul class="navbar-nav ms-auto flex-row align-items-center gap-4">
            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center" href="/public/fornecedores.php">Fornecedores</a>
            </li>
            <?php if (isset($_SESSION['user'])): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php if (!empty($_SESSION['user']['foto'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['user']['foto']) ?>" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" alt="Foto do perfil">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/32x32?text=User" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" alt="Foto do perfil">
                    <?php endif; ?>
                    <?= htmlspecialchars($_SESSION['user']['nome']) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="minha_conta.php">Minha Conta</a></li>
                    <li><a class="dropdown-item" href="criar_usuario.php">Cadastre</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Sair</a></li>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav> 
</body>
</html> 