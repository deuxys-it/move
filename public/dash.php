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
       <h1>Dashboard</h1>
       <iframe title="Piloto_Agencias" width="600" height="373.5" src="https://app.powerbi.com/view?r=eyJrIjoiNGYyMGZkYWQtMDg0YS00YmNhLTg3MTQtYzM2ZTk1N2JjMmVkIiwidCI6IjYzNDU2YzdjLWQ5MjYtNDEwMC04ZTY5LTIzMjUyYTkzN2U3YSJ9" frameborder="0" allowFullScreen="true"></iframe>

    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
