<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/db.php';
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

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

// Importação do arquivo .xlsx de fornecedores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fornecedores_xlsx'])) {
    $filePath = $_FILES['fornecedores_xlsx']['tmp_name'];
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // Limpa a tabela de fornecedores antes de importar
    $pdo->exec("DELETE FROM fornecedores");

    // Pula o cabeçalho (primeira linha)
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        $stmt = $pdo->prepare("INSERT INTO fornecedores (situacao, codigo, tipo, nome, razao_social, rede, praca, cnpj, estado, grupo_pdi, conta_passivo, conta_despesa, centro_custo_despesa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $row[0], // Situação
            $row[1], // Código
            $row[2], // Tipo
            $row[3], // Nome
            $row[4], // Razão Social
            $row[5], // Rede
            $row[6], // Praça
            $row[7], // CNPJ
            $row[8], // Estado
            $row[9], // Grupo de PDI
            $row[10], // Conta Passivo
            $row[11], // Conta Despesa
            $row[12], // Centro de Custo Despesas
        ]);
    }
    header('Location: certidoes.php?import=ok');
    exit;
}

// Buscar todos os fornecedores
$fornecedores = $pdo->query('SELECT id, nome FROM fornecedores ORDER BY nome')->fetchAll();
// Buscar status das certidões
$statusCertidoes = [];
foreach ($fornecedores as $f) {
    $statusCertidoes[$f['id']] = $pdo->query('SELECT * FROM status_certidoes WHERE fornecedor_id = ' . intval($f['id']))->fetch();
}

// Função para verificar se o fornecedor tem cada certidão
function temCertidao($certidao) {
    return (!empty($certidao) && $certidao !== '-' && $certidao !== null);
}

// Adicionar fornecedor manualmente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_fornecedor'])) {
    $nome = trim($_POST['novo_nome'] ?? '');
    $email = trim($_POST['novo_email'] ?? '');
    if ($nome) {
        $stmt = $pdo->prepare('INSERT INTO fornecedores (nome, email) VALUES (?, ?)');
        $stmt->execute([$nome, $email]);
        header('Location: certidoes.php');
        exit;
    }
}
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
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-black text-white min-vh-100 d-flex flex-column">
<?php include 'header.php'; ?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Certidões dos Fornecedores</h1>
    </div>
    <div class="table-responsive">
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Fornecedor</th>
                    <th>Federal</th>
                    <th>Estadual</th>
                    <th>Municipal</th>
                    <th>Trabalhista</th>
                    <th>FGTS</th>
                    <th>SICAF</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedores as $fornecedor): 
                    $status = $statusCertidoes[$fornecedor['id']] ?? [];
                ?>
                <tr>
                    <td><?= htmlspecialchars($fornecedor['nome']) ?></td>
                    <td><?= !empty($status) && $status['certidao_federal'] ? '<span style="color: #0f0; font-size:1.2em;">&#10004;</span>' : '<span style="color: #f00; font-size:1.2em;">&#10008;</span>' ?></td>
                    <td><?= !empty($status) && $status['certidao_estadual'] ? '<span style="color: #0f0; font-size:1.2em;">&#10004;</span>' : '<span style="color: #f00; font-size:1.2em;">&#10008;</span>' ?></td>
                    <td><?= !empty($status) && $status['certidao_municipal'] ? '<span style="color: #0f0; font-size:1.2em;">&#10004;</span>' : '<span style="color: #f00; font-size:1.2em;">&#10008;</span>' ?></td>
                    <td><?= !empty($status) && $status['certidao_trabalhista'] ? '<span style="color: #0f0; font-size:1.2em;">&#10004;</span>' : '<span style="color: #f00; font-size:1.2em;">&#10008;</span>' ?></td>
                    <td><?= !empty($status) && $status['certidao_fgts'] ? '<span style="color: #0f0; font-size:1.2em;">&#10004;</span>' : '<span style="color: #f00; font-size:1.2em;">&#10008;</span>' ?></td>
                    <td></td>
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
