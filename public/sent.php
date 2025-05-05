<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$data = file_exists(__DIR__ . '/../src/storage.json')
    ? json_decode(file_get_contents(__DIR__ . '/../src/storage.json'), true)
    : [];

if (!empty($data) && isset($data['title'])) {
    $data = [$data];
}

// Captura filtros
$usuarioFiltro = $_GET['usuario'] ?? '';
$statusFiltro = $_GET['status'] ?? '';
$busca = strtolower(trim($_GET['busca'] ?? ''));

// Aplica filtros
$data = array_filter($data, function ($item) use ($usuarioFiltro, $statusFiltro, $busca) {
    $match = true;

    if ($usuarioFiltro && (!isset($item['usuario']) || $item['usuario'] !== $usuarioFiltro)) {
        $match = false;
    }

    if ($statusFiltro && (!isset($item['status']) || strtolower($item['status']) !== strtolower($statusFiltro))) {
        $match = false;
    }

    if ($busca) {
        $haystack = strtolower(
            ($item['destinatario'] ?? '') . ' ' .
            ($item['title'] ?? '') . ' ' .
            ($item['materias'] ?? '')
        );
        if (strpos($haystack, $busca) === false) {
            $match = false;
        }
    }

    return $match;
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Histórico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleDetails(index) {
            const details = document.getElementById('details-' + index);
            details.classList.toggle('d-none');
        }
    </script>
</head>
<body class='bg-black text-white p-4'>
    <?php include 'header.php'; ?>

    <h1 class='h2 mb-4'>E-mails Enviados</h1>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="usuario" placeholder="Filtrar por usuário" value="<?= htmlspecialchars($usuarioFiltro) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Todos os Status</option>
                <option value="Enviado" <?= $statusFiltro === 'Enviado' ? 'selected' : '' ?>>Enviado</option>
                <option value="Erro" <?= $statusFiltro === 'Erro' ? 'selected' : '' ?>>Erro</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="busca" placeholder="Buscar título, email ou matéria" value="<?= htmlspecialchars($busca) ?>" class="form-control">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <?php if (!empty($data) && is_array($data)): ?>
        <div class="list-group">
            <?php foreach ($data as $index => $item): ?>
                <div class="list-group-item list-group-item-action bg-black text-white border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <button onclick="toggleDetails(<?= $index ?>)" class="btn btn-link text-white me-2">+</button>
                            <div>
                                <div class="small text-muted">Destinatário</div>
                                <div><?= htmlspecialchars($item['destinatario'] ?? $item['destinatarios'] ?? 'N/A') ?></div>
                            </div>
                            <div class="ms-4">
                                <div class="small text-muted">Título</div>
                                <div><?= htmlspecialchars($item['title'] ?? 'Sem título') ?></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div><?= htmlspecialchars($item['status'] ?? 'Sem status') ?></div>
                            <?php if (in_array($item['status'] ?? '', ['ABRIU O ANEXO', 'ABRIU O E-MAIL'])): ?>
                                <span class="text-success ms-2">✔️</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div id="details-<?= $index ?>" class="d-none bg-secondary p-3 rounded mt-2">
                    <strong>Detalhes:</strong><br>
                    Data/Hora: <?= htmlspecialchars($item['data'] ?? $item['timestamp'] ?? 'N/A') ?><br>
                    Usuário: <?= htmlspecialchars($item['usuario'] ?? 'N/A') ?><br>
                    Status: <?= htmlspecialchars($item['status'] ?? 'N/A') ?><br>
                    Matérias: <?= htmlspecialchars($item['materias'] ?? 'N/A') ?><br>
                    Tracking: <?= htmlspecialchars($item['tracking_id'] ?? '-') ?><br>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center text-muted">Nenhum e-mail encontrado com os filtros atuais.</div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
