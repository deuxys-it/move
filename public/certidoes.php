<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/db.php';

// Adicionar nova certidão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_certidao'])) {
    $stmt = $pdo->prepare('UPDATE certidoes SET cnpj=?, certidao_federal=?, certidao_estadual=?, certidao_municipal=?, certidao_trabalhista=?, certidao_fgts=? WHERE id=?');
    $stmt->execute([
        $_POST['cnpj'],
        $_POST['certidao_federal'],
        $_POST['certidao_estadual'],
        $_POST['certidao_municipal'],
        $_POST['certidao_trabalhista'],
        $_POST['certidao_fgts'],
        $_POST['certidao_id']
    ]);
    header('Location: certidoes.php');
    exit;
}

// Listar fornecedores para o select
$fornecedores = $pdo->query('SELECT id, nome FROM fornecedores ORDER BY nome')->fetchAll();
// Listar certidões com nome do fornecedor
$certidoes = $pdo->query('SELECT c.*, f.nome as fornecedor_nome FROM certidoes c JOIN fornecedores f ON c.fornecedor_id = f.id ORDER BY f.nome')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Certidões - Morya</title>
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
        .btn-outline-custom {
            background-color: black;
            color: white;
            border: 1px solid white;
        }
        .btn-outline-custom:hover {
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
<?php include 'header.php'; ?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Certidões</h1>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Fornecedor</th>
                    <th>CNPJ</th>
                    <th>Certidão Federal</th>
                    <th>Certidão Estadual</th>
                    <th>Certidão Municipal</th>
                    <th>Certidão Trabalhista</th>
                    <th>Certidão FGTS</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($certidoes as $certidao): ?>
                <tr>
                    <td><?= htmlspecialchars($certidao['fornecedor_nome']) ?></td>
                    <td><?= htmlspecialchars($certidao['cnpj']) ?></td>
                    <td>
                        <a href="<?= htmlspecialchars($certidao['certidao_federal']) ?>" target="_blank" class="btn btn-outline-custom">
                            Acessar
                        </a>
                    </td>
                    <td>
                        <a href="<?= htmlspecialchars($certidao['certidao_estadual']) ?>" target="_blank" class="btn btn-outline-custom">
                            Acessar
                        </a>
                    </td>
                    <td>
                        <a href="<?= htmlspecialchars($certidao['certidao_municipal']) ?>" target="_blank" class="btn btn-outline-custom">
                            Acessar
                        </a>
                    </td>
                    <td>
                        <a href="<?= htmlspecialchars($certidao['certidao_trabalhista']) ?>" target="_blank" class="btn btn-outline-custom">
                            Acessar
                        </a>
                    </td>
                    <td>
                        <a href="<?= htmlspecialchars($certidao['certidao_fgts']) ?>" target="_blank" class="btn btn-outline-custom">
                            Acessar
                        </a>
                    </td>
                    <td>
                        <button class="btn btn-custom me-2" data-bs-toggle="modal" data-bs-target="#editarCertidaoModal" 
                                data-id="<?= $certidao['id'] ?>"
                                data-cnpj="<?= htmlspecialchars($certidao['cnpj']) ?>"
                                data-federal="<?= htmlspecialchars($certidao['certidao_federal']) ?>"
                                data-estadual="<?= htmlspecialchars($certidao['certidao_estadual']) ?>"
                                data-municipal="<?= htmlspecialchars($certidao['certidao_municipal']) ?>"
                                data-trabalhista="<?= htmlspecialchars($certidao['certidao_trabalhista']) ?>"
                                data-fgts="<?= htmlspecialchars($certidao['certidao_fgts']) ?>">
                            Editar
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Editar Certidão -->
<div class="modal fade" id="editarCertidaoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-white">
                <h5 class="modal-title">Editar Certidão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="editar_certidao" value="1">
                    <input type="hidden" name="certidao_id" id="certidao_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CNPJ</label>
                            <input type="text" name="cnpj" id="cnpj" class="form-control" placeholder="00.000.000/0000-00" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Certidão Federal</label>
                            <input type="url" name="certidao_federal" id="certidao_federal" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Certidão Estadual</label>
                            <input type="url" name="certidao_estadual" id="certidao_estadual" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Certidão Municipal</label>
                            <input type="url" name="certidao_municipal" id="certidao_municipal" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Certidão Trabalhista</label>
                            <input type="url" name="certidao_trabalhista" id="certidao_trabalhista" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Certidão FGTS</label>
                        <input type="url" name="certidao_fgts" id="certidao_fgts" class="form-control">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editarModal = document.getElementById('editarCertidaoModal');
    if (editarModal) {
        editarModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const cnpj = button.getAttribute('data-cnpj');
            const federal = button.getAttribute('data-federal');
            const estadual = button.getAttribute('data-estadual');
            const municipal = button.getAttribute('data-municipal');
            const trabalhista = button.getAttribute('data-trabalhista');
            const fgts = button.getAttribute('data-fgts');

            document.getElementById('certidao_id').value = id;
            document.getElementById('cnpj').value = cnpj;
            document.getElementById('certidao_federal').value = federal;
            document.getElementById('certidao_estadual').value = estadual;
            document.getElementById('certidao_municipal').value = municipal;
            document.getElementById('certidao_trabalhista').value = trabalhista;
            document.getElementById('certidao_fgts').value = fgts;
        });
    }
});
</script>
</body>
</html>
