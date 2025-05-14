<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/db.php';

// Busca todos os envios, mais recentes primeiro, junto com o nome do usuário
$envios = $pdo->query("
    SELECT h.*, u.nome as usuario_nome
    FROM historico_envios h
    JOIN usuarios u ON h.usuario_id = u.id
    ORDER BY h.data_envio DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Histórico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class='bg-black text-white'>
<?php include 'header.php'; ?>
<div class="container py-4">
    <h1 class="h2 mb-4">Histórico de E-mails Enviados</h1>
    <div class="table-responsive">
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Destinatários</th>
                    <th>Status</th>
                    <th>Matérias</th>
                    <th>Usuário</th>
                    <th>Data/Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($envios as $envio): ?>
                <tr>
                    <td><?= htmlspecialchars($envio['titulo']) ?></td>
                    <td><?= htmlspecialchars($envio['destinatarios']) ?></td>
                    <td><?= htmlspecialchars($envio['status']) ?></td>
                    <td><?= htmlspecialchars($envio['materias']) ?></td>
                    <td><?= htmlspecialchars($envio['usuario_nome']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($envio['data_envio'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
