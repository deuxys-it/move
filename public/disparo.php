<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Morya - Envio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet" />
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
        .select2-container--bootstrap-5 .select2-selection {
            background-color: #212529 !important;
            color: white !important;
            border-color: #6c757d !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single {
            height: 38px !important;
            padding: 5px !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: white !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            background-color: #212529 !important;
            border-color: #6c757d !important;
        }
        .select2-container--bootstrap-5 .select2-results__option {
            color: white !important;
        }
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #0d6efd !important;
            color: white !important;
        }
        .select2-container--bootstrap-5 .select2-search__field {
            background-color: #212529 !important;
            color: white !important;
            border-color: #6c757d !important;
        }
    </style>
</head>
<body class="bg-black text-white min-vh-100 d-flex flex-column">
<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/db.php';

require __DIR__ . '/../vendor/autoload.php';
use Morya\Mailer;

$msg = null;

// Buscar fornecedores do banco (nome e email)
$fornecedores = $pdo->query('SELECT nome, email FROM fornecedores WHERE email IS NOT NULL AND email != "" ORDER BY nome')->fetchAll();

// Handler para erros fatais
function fatalErrorHandler() {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
        echo '<!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <title>Erro no Disparo</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-black text-white d-flex align-items-center justify-content-center" style="min-height:100vh;">
            <div class="container">
                <div class="alert alert-danger mt-5">
                    <h2 class="mb-3">Ocorreu um erro ao processar o disparo</h2>
                    <pre class="bg-dark text-danger p-3 rounded">'.htmlspecialchars($error['message']).'</pre>
                    <p><strong>Arquivo:</strong> '.htmlspecialchars($error['file']).'</p>
                    <p><strong>Linha:</strong> '.htmlspecialchars($error['line']).'</p>
                    <a href="disparo.php" class="btn btn-secondary mt-3">Voltar</a>
                </div>
            </div>
        </body>
        </html>';
        ob_end_flush();
        exit;
    }
}
register_shutdown_function('fatalErrorHandler');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['destinatarios'] ?? [];
    $subject = $_POST['titulo'] ?? '';
    if (empty($subject)) {
        $subject = 'MORYA';
    }
    $body = $_POST['body'] ?? '';
    $anexosIndividuais = $_FILES['anexos_individuais'] ?? [];
    $anexosGlobais = $_FILES['anexos_globais'] ?? [];
    $usuarioId = $_SESSION['user']['id'] ?? null;

    // Monta títulos personalizados para cada destinatário
    $subjects = [];
    foreach ($to as $idx => $email) {
        if (isset($anexosIndividuais['name'][$idx]) && $anexosIndividuais['name'][$idx]) {
            $subjects[] = $subject . ' - ' . $anexosIndividuais['name'][$idx];
        } else {
            $subjects[] = $subject;
        }
    }

    $result = Mailer::send($to, $subjects, $body, $anexosIndividuais, $anexosGlobais);

    // Salva no banco de dados
    $stmt = $pdo->prepare("INSERT INTO historico_envios (usuario_id, titulo, corpo, destinatarios, status, materias) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $usuarioId,
        $subject,
        $body,
        implode(', ', $to),
        $result ? 'Enviado' : 'Erro',
        implode(', ', $anexosGlobais['name'] ?? [])
    ]);

    header('Location: sent.php');
    exit;
}
?>

<?php include 'header.php'; ?>

<main class="container py-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="h3 mb-4">Disparo de E-mails</h1>
        <p class="mb-3 text-warning">Caso o disparo falhe, o motivo do erro será exibido logo abaixo deste aviso, acima do formulário.</p>

        <?php if (isset($erroEnvio) && $erroEnvio): ?>
            <div class="alert alert-danger mb-4"><?= htmlspecialchars($erroEnvio) ?></div>
        <?php endif; ?>

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
                        <input type="text" name="destinatarios[]" class="form-control bg-dark text-white destinatario-autocomplete" placeholder="Digite o email ou selecione um fornecedor" autocomplete="off">
                        <input type="file" name="anexos_individuais[]" class="form-control bg-dark text-white">
                        <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger-custom">Remover</button>
                    </div>
                </div>
                <button type="button" onclick="addDestinatario()" class="btn btn-custom mt-2">+ Adicionar destinatário</button>
                <input type="file" id="csvInput" accept=".csv" style="display:none">
                <button type="button" onclick="document.getElementById('csvInput').click()" class="btn btn-secondary mt-2 ms-2">Importar CSV</button>
                <span class="text-muted ms-2">(Apenas uma coluna de emails)</span>
            </div>

            <!-- Matérias globais -->
            <div class="mb-4">
                <label class="form-label">Matérias (anexos que irão para todos)</label>
                <input type="file" id="anexosGlobaisInput" class="form-control bg-dark text-white" multiple style="display:none;">
                <button type="button" id="addMaisAnexos" class="btn btn-custom">Adicionar arquivos</button>
                <ul id="anexosGlobaisLista" class="mt-2" style="list-style: disc inside; color: #fff;"></ul>
            </div>

            <!-- Enviar -->
            <div>
                <button type="submit" class="btn btn-custom w-100 py-3">Enviar</button>
            </div>
        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
const fornecedores = [
    <?php foreach ($fornecedores as $fornecedor): ?>
        { label: "<?= htmlspecialchars($fornecedor['nome']) ?> (<?= htmlspecialchars($fornecedor['email']) ?>)", value: "<?= htmlspecialchars($fornecedor['email']) ?>" },
    <?php endforeach; ?>
];

function initAutocomplete() {
    $(".destinatario-autocomplete").autocomplete({
        source: fornecedores,
        minLength: 1,
        select: function(event, ui) {
            $(this).val(ui.item.value);
            $(this).attr('data-nome', ui.item.label);
            return false;
        },
        focus: function(event, ui) {
            $(this).val(ui.item.value);
            return false;
        }
    });
}

$(document).ready(function() {
    initAutocomplete();
});

function addDestinatario() {
    const container = document.getElementById('destinatariosContainer');
    const newRow = document.createElement('div');
    newRow.className = 'd-flex gap-3 align-items-center destinatario-line mb-3';
    newRow.innerHTML = `
        <input type="text" name="destinatarios[]" class="form-control bg-dark text-white destinatario-autocomplete" placeholder="Digite o email ou selecione um fornecedor" autocomplete="off">
        <input type="file" name="anexos_individuais[]" class="form-control bg-dark text-white">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger-custom">Remover</button>
    `;
    container.appendChild(newRow);
    initAutocomplete();
}

document.getElementById('csvInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(event) {
        const lines = event.target.result.split(/\r?\n/);
        lines.forEach(email => {
            email = email.trim();
            if (email && validateEmail(email)) {
                addDestinatarioComValor(email);
            }
        });
    };
    reader.readAsText(file);
    // Limpa o input para permitir importar o mesmo arquivo novamente se necessário
    e.target.value = '';
});

function validateEmail(email) {
    // Validação simples de email
    return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email);
}

function addDestinatarioComValor(valor) {
    const container = document.getElementById('destinatariosContainer');
    const newRow = document.createElement('div');
    newRow.className = 'd-flex gap-3 align-items-center destinatario-line mb-3';
    newRow.innerHTML = `
        <input type="text" name="destinatarios[]" class="form-control bg-dark text-white destinatario-autocomplete" placeholder="Digite o email ou selecione um fornecedor" autocomplete="off" value="${valor}">
        <input type="file" name="anexos_individuais[]" class="form-control bg-dark text-white">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger-custom">Remover</button>
    `;
    container.appendChild(newRow);
    initAutocomplete();
}

let anexosGlobaisArray = [];

function atualizarListaAnexos() {
    const lista = document.getElementById('anexosGlobaisLista');
    lista.innerHTML = '';
    anexosGlobaisArray.forEach((file, idx) => {
        const li = document.createElement('li');
        li.textContent = file.name;
        // Botão para remover arquivo
        const btn = document.createElement('button');
        btn.textContent = 'Remover';
        btn.type = 'button';
        btn.className = 'btn btn-danger btn-sm ms-2';
        btn.onclick = function() {
            anexosGlobaisArray.splice(idx, 1);
            atualizarListaAnexos();
        };
        li.appendChild(btn);
        lista.appendChild(li);
    });
}

document.getElementById('anexosGlobaisInput').addEventListener('change', function(e) {
    for (const file of e.target.files) {
        anexosGlobaisArray.push(file);
    }
    atualizarListaAnexos();
    // Limpa o input para permitir adicionar o mesmo arquivo novamente se quiser
    e.target.value = '';
});

document.getElementById('addMaisAnexos').addEventListener('click', function() {
    document.getElementById('anexosGlobaisInput').click();
});

// Antes de enviar o formulário, atualiza o input file com todos os arquivos acumulados
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    if (anexosGlobaisArray.length > 0) {
        const dataTransfer = new DataTransfer();
        anexosGlobaisArray.forEach(file => dataTransfer.items.add(file));
        document.getElementById('anexosGlobaisInput').files = dataTransfer.files;
    }
});
</script>

<?php ob_end_flush(); ?>
</body>
</html>
