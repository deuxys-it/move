<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/db.php';

// Adicionar novo fornecedor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_fornecedor'])) {
    $stmt = $pdo->prepare('INSERT INTO fornecedores (nome, email, responsavel, cidade, estado) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([
        $_POST['nome'],
        $_POST['email'],
        $_POST['responsavel'],
        $_POST['cidade'],
        $_POST['estado']
    ]);
    header('Location: fornecedores.php');
    exit;
}

// Excluir fornecedor
if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare('DELETE FROM fornecedores WHERE id = ?');
    $stmt->execute([$_GET['excluir']]);
    header('Location: fornecedores.php');
    exit;
}

// Listar fornecedores
$fornecedores = $pdo->query('SELECT * FROM fornecedores ORDER BY nome')->fetchAll();
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Fornecedores - Morya</title>
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
        .modal-content {
            background-color: black;
            color: white;
            border: 1px solid white;
        }
        .form-control, .form-select {
            background-color: black;
            color: white;
            border: 1px solid white;
        }
        .form-control:focus, .form-select:focus {
            background-color: black;
            color: white;
            border: 1px solid white;
            box-shadow: none;
        }
    </style>
</head>
<body class="bg-black text-white min-vh-100 d-flex flex-column">
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Fornecedores</h1>
        <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#novoFornecedorModal">
            Novo Fornecedor
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Responsável</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedores as $fornecedor): ?>
                <tr>
                    <td><?= htmlspecialchars($fornecedor['nome']) ?></td>
                    <td><?= htmlspecialchars($fornecedor['email']) ?></td>
                    <td><?= htmlspecialchars($fornecedor['responsavel']) ?></td>
                    <td><?= htmlspecialchars($fornecedor['cidade']) ?></td>
                    <td><?= htmlspecialchars($fornecedor['estado']) ?></td>
                    <td>
                        <!-- Botão de editar pode ser implementado depois -->
                        <a href="?excluir=<?= $fornecedor['id'] ?>" class="btn btn-danger-custom" onclick="return confirm('Excluir este fornecedor?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Novo Fornecedor -->
<div class="modal fade" id="novoFornecedorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-white">
                <h5 class="modal-title">Novo Fornecedor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="novo_fornecedor" value="1">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Responsável</label>
                        <input type="text" name="responsavel" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="cidade" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <input type="text" name="estado" class="form-control">
                    </div>
                    <div class="modal-footer border-white">
                        <button type="button" class="btn btn-danger-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-custom">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 