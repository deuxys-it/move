<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Morya - Envio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            background-color: white;
            color: black;
            border: 1px solid white;
        }
        .btn-custom:hover {
            background-color: black;
            color: white;
            border: 1px solid white;
        }
        .btn-danger-custom {
            background-color: black;
            color: white;
            border: 1px solid white;
        }
        .btn-danger-custom:hover {
            background-color: white;
            color: black;
            border: 1px solid white;
        }
        .alert-custom {
            background-color: black;
            color: white;
            border: 1px solid white;
        }
    </style>
</head>
<body class="bg-black text-white min-vh-100 d-flex flex-column">
<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/db.php';

require __DIR__ . '/../vendor/autoload.php';
use Morya\Mailer;

$msg = null;

// Buscar fornecedores do banco
$fornecedores = $pdo->query('SELECT nome, email FROM fornecedores ORDER BY nome')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['destinatarios'] ?? [];
    $subject = $_POST['titulo'] ?? '';
    $body = $_POST['body'] ?? '';
    $anexosIndividuais = $_FILES['anexos_individuais'] ?? [];
    $anexosGlobais = $_FILES['anexos_globais'] ?? [];

    $result = Mailer::send($to, $subject, $body, $anexosIndividuais, $anexosGlobais);

    file_put_contents(__DIR__ . '/../src/storage.json', json_encode([
        'title' => $subject,
        'destinatarios' => implode(', ', $to),
        'status' => $result ? 'Enviado' : 'Erro',
        'materias' => implode(', ', $anexosGlobais['name'] ?? [])
    ]));

    $msg = $result ? 'E-mail enviado com sucesso!' : 'Erro ao enviar e-mail.';
}
?>

<?php include 'header.php'; ?>

<main class="container py-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="h3 mb-4">Disparo de E-mails</h1>

        <?php if ($msg): ?>
            <div class="alert alert-custom mb-4"><?= $msg ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <!-- Título -->
            <div class="mb-4">
                <label class="form-label">Título do e-mail</label>
                <input type="text" name="titulo" class="form-control bg-dark text-white" required>
            </div>

            <!-- E-mail para notificações -->
            <div class="mb-4">
                <label class="form-label">E-mail que irá receber as notificações</label>
                <input type="email" name="notificacao"
                    value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>"
                    class="form-control bg-dark text-white" required>
            </div>

            <!-- Corpo -->
            <div class="mb-4">
                <label class="form-label">Corpo do e-mail</label>
                <textarea name="body" rows="5" class="form-control bg-dark text-white" required></textarea>
            </div>

            <!-- Destinatários -->
            <div class="mb-4">
                <label class="form-label">Destinatários</label>
                <div id="destinatariosContainer" class="space-y-4">
                    <div class="d-flex gap-3 align-items-center destinatario-line mb-3">
                        <select name="destinatarios[]" class="form-select bg-dark text-white" required>
                            <option value="">Selecione o fornecedor</option>
                            <?php foreach ($fornecedores as $fornecedor): ?>
                                <option value="<?= htmlspecialchars($fornecedor['email']) ?>">
                                    <?= htmlspecialchars($fornecedor['nome']) ?> (<?= htmlspecialchars($fornecedor['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="file" name="anexos_individuais[]" class="form-control bg-dark text-white">
                        <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger-custom">Remover</button>
                    </div>
                </div>
                <button type="button" onclick="addDestinatario()" class="btn btn-custom mt-2">+ Adicionar destinatário</button>
            </div>

            <!-- Matérias globais -->
            <div class="mb-4">
                <label class="form-label">Matérias (anexos que irão para todos)</label>
                <input type="file" name="anexos_globais[]" multiple class="form-control bg-dark text-white">
            </div>

            <!-- Enviar -->
            <div>
                <button type="submit" class="btn btn-custom w-100 py-3">Enviar</button>
            </div>
        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function addDestinatario() {
    const container = document.getElementById('destinatariosContainer');
    const newRow = document.createElement('div');
    newRow.className = 'd-flex gap-3 align-items-center destinatario-line mb-3';
    newRow.innerHTML = `
        <select name=\"destinatarios[]\" class=\"form-select bg-dark text-white\" required>
            <option value=\"\">Selecione o fornecedor</option>
            <?php foreach ($fornecedores as $fornecedor): ?>
                <option value=\"<?= htmlspecialchars($fornecedor['email']) ?>\"><?= htmlspecialchars($fornecedor['nome']) ?> (<?= htmlspecialchars($fornecedor['email']) ?>)</option>
            <?php endforeach; ?>
        </select>
        <input type=\"file\" name=\"anexos_individuais[]\" class=\"form-control bg-dark text-white\">
        <button type=\"button\" onclick=\"this.parentElement.remove()\" class=\"btn btn-danger-custom\">Remover</button>
    `;
    container.appendChild(newRow);
}
</script>

</body>
</html>
