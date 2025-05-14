<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/db.php';
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Importação do arquivo .xlsx
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
    header('Location: fornecedores.php?import=ok');
    exit;
}

// Cadastro manual de fornecedor (modal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_fornecedor_modal'])) {
    $campos = [
        'situacao_modal', 'codigo_modal', 'tipo_modal', 'nome_modal', 'razao_social_modal', 'rede_modal', 'praca_modal', 'cnpj_modal', 'estado_modal', 'grupo_pdi_modal', 'conta_passivo_modal', 'conta_despesa_modal', 'centro_custo_despesa_modal', 'email_modal', 'responsavel_modal', 'cidade_modal'
    ];
    $valores = [];
    foreach ($campos as $campo) {
        $valores[] = trim($_POST[$campo] ?? '');
    }
    $stmt = $pdo->prepare("INSERT INTO fornecedores (situacao, codigo, tipo, nome, razao_social, rede, praca, cnpj, estado, grupo_pdi, conta_passivo, conta_despesa, centro_custo_despesa, email, responsavel, cidade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute($valores);
    header('Location: fornecedores.php');
    exit;
}

// Edição de fornecedor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_fornecedor'])) {
    $id = intval($_POST['id']);
    $campos = [
        'situacao', 'codigo', 'tipo', 'nome', 'razao_social', 'rede', 'praca', 'cnpj', 'estado', 'grupo_pdi', 'conta_passivo', 'conta_despesa', 'centro_custo_despesa', 'email', 'responsavel', 'cidade'
    ];
    $valores = [];
    foreach ($campos as $campo) {
        $valores[] = trim($_POST[$campo] ?? '');
    }
    $valores[] = $id;
    $stmt = $pdo->prepare("UPDATE fornecedores SET situacao=?, codigo=?, tipo=?, nome=?, razao_social=?, rede=?, praca=?, cnpj=?, estado=?, grupo_pdi=?, conta_passivo=?, conta_despesa=?, centro_custo_despesa=?, email=?, responsavel=?, cidade=? WHERE id=?");
    $stmt->execute($valores);
    header('Location: fornecedores.php');
    exit;
}

// Ativar/desativar fornecedor
if (isset($_GET['toggle_ativo'])) {
    $id = intval($_GET['toggle_ativo']);
    $fornecedor = $pdo->query("SELECT ativo FROM fornecedores WHERE id = $id")->fetch();
    if ($fornecedor) {
        $novoStatus = $fornecedor['ativo'] ? 0 : 1;
        $pdo->prepare("UPDATE fornecedores SET ativo=? WHERE id=?")->execute([$novoStatus, $id]);
    }
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
        .table th, .table td {
            vertical-align: middle;
            font-size: 0.75rem;
            padding: 0.2rem 0.3rem;
        }
        .table {
            font-size: 0.75rem;
            white-space: normal;
        }
        .table-responsive {
            overflow-x: unset !important;
        }
        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        .switch input {display:none;}
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #888;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #28a745;
        }
        input:checked + .slider:before {
            transform: translateX(20px);
        }
    </style>
</head>
<body class="bg-black text-white min-vh-100 d-flex flex-column">
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Fornecedores</h1>
        <div class="d-flex gap-2 align-items-center">
            <form method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center mb-0">
                <input type="file" name="fornecedores_xlsx" accept=".xlsx" class="form-control" required>
                <button type="submit" class="btn btn-custom">Importar</button>
            </form>
            <button class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#adicionarFornecedorModal">Adicionar</button>
        </div>
    </div>
    <?php if (isset($_GET['import'])): ?>
        <div class="alert alert-success">Fornecedores importados com sucesso!</div>
    <?php endif; ?>
    <!-- Modal Adicionar Fornecedor -->
    <div class="modal fade" id="adicionarFornecedorModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
          <form method="POST">
            <input type="hidden" name="adicionar_fornecedor_modal" value="1">
            <div class="modal-header border-white">
              <h5 class="modal-title">Adicionar Fornecedor</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
              <div class="col-md-4 mb-2">
                <label class="form-label">Situação</label>
                <input type="text" name="situacao_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Código</label>
                <input type="text" name="codigo_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Tipo</label>
                <input type="text" name="tipo_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Nome</label>
                <input type="text" name="nome_modal" class="form-control bg-dark text-white" required>
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Razão Social</label>
                <input type="text" name="razao_social_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Rede</label>
                <input type="text" name="rede_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Praça</label>
                <input type="text" name="praca_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">CNPJ</label>
                <input type="text" name="cnpj_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Estado</label>
                <input type="text" name="estado_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Grupo de PDI</label>
                <input type="text" name="grupo_pdi_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Conta Passivo</label>
                <input type="text" name="conta_passivo_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Conta Despesa</label>
                <input type="text" name="conta_despesa_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Centro de Custo Despesa</label>
                <input type="text" name="centro_custo_despesa_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">E-mail</label>
                <input type="email" name="email_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Responsável</label>
                <input type="text" name="responsavel_modal" class="form-control bg-dark text-white">
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Cidade</label>
                <input type="text" name="cidade_modal" class="form-control bg-dark text-white">
              </div>
            </div>
            <div class="modal-footer border-white">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-custom">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="mb-4 p-3 bg-dark rounded" style="display:none;">
        <!-- Formulário antigo removido -->
    </div>
    <div class="table-responsive">
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Situação</th>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Nome</th>
                    <th>Razão Social</th>
                    <th>Rede</th>
                    <th>Praça</th>
                    <th>CNPJ</th>
                    <th>Estado</th>
                    <th>Grupo de PDI</th>
                    <th>Conta Passivo</th>
                    <th>Conta Despesa</th>
                    <th>Centro de Custo Despesas</th>
                    <th>Email</th>
                    <th>Responsável</th>
                    <th>Cidade</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedores as $fornecedor): ?>
                <tr>
                    <td><?= htmlspecialchars($fornecedor['situacao'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['codigo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['tipo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['nome'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['razao_social'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['rede'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['praca'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['cnpj'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['estado'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['grupo_pdi'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['conta_passivo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['conta_despesa'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['centro_custo_despesa'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['responsavel'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fornecedor['cidade'] ?? '') ?></td>
                    <td>
                        <form method="get" style="display:inline">
                            <input type="hidden" name="toggle_ativo" value="<?= $fornecedor['id'] ?>">
                            <label class="switch">
                                <input type="checkbox" onchange="this.form.submit()" <?= $fornecedor['ativo'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </form>
                    </td>
                    <td>
                        <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#editarFornecedorModal<?= $fornecedor['id'] ?>">Editar</button>
                    </td>
                </tr>
                <!-- Modal Editar Fornecedor -->
                <div class="modal fade" id="editarFornecedorModal<?= $fornecedor['id'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content bg-dark text-white">
                      <form method="POST">
                        <input type="hidden" name="editar_fornecedor" value="1">
                        <input type="hidden" name="id" value="<?= $fornecedor['id'] ?>">
                        <div class="modal-header border-white">
                          <h5 class="modal-title">Editar Fornecedor</h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body row g-2">
                          <?php $campos = ['situacao','codigo','tipo','nome','razao_social','rede','praca','cnpj','estado','grupo_pdi','conta_passivo','conta_despesa','centro_custo_despesa','email','responsavel','cidade'];
                          foreach ($campos as $campo): ?>
                            <div class="col-md-4 mb-2">
                              <label class="form-label mb-1 text-capitalize"><?= ucfirst(str_replace('_',' ',$campo)) ?></label>
                              <input type="text" name="<?= $campo ?>" class="form-control bg-dark text-white" value="<?= htmlspecialchars($fornecedor[$campo] ?? '') ?>">
                            </div>
                          <?php endforeach; ?>
                        </div>
                        <div class="modal-footer border-white">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                          <button type="submit" class="btn btn-custom">Salvar</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 