<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
use Morya\Mailer;

$msg = null;

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

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Morya - Envio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white min-h-screen">
<nav class='flex justify-between items-center border-b border-white mb-6 p-4'>
        <img src='logo.png' class='h-10'>
        <div class='flex gap-4'>
            <a href='dashboard.php'>Enviar</a>
            <a href='sent.php'>Histórico</a>
            <a href='logout.php' class='text-red-500'>Sair</a>
        </div>
    </nav>

        <!-- Grid de opções -->
        <main class="flex-grow flex justify-center items-start">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 p-6 max-w-4xl w-full">
            <a href="disparo.php" class="bg-gray-300 text-black text-center py-16 rounded-lg hover:bg-gray-400 transition">Disparo de<br>E-mails</a>
            <a href="certidoes.php" class="bg-gray-300 text-black text-center py-16 rounded-lg hover:bg-gray-400 transition">Certidões</a>
            <a href="orcamentos.php" class="bg-gray-300 text-black text-center py-16 rounded-lg hover:bg-gray-400 transition">Orçamentos</a>
            <a href="nomeclatura.php" class="bg-gray-300 text-black text-center py-16 rounded-lg hover:bg-gray-400 transition">Nomeclatura</a>
            <a href="midia.php" class="bg-gray-300 text-black text-center py-16 rounded-lg hover:bg-gray-400 transition">Plano de<br>Mídia</a>
            <a href="novo.php" class="bg-gray-300 text-black text-center py-16 rounded-lg hover:bg-gray-400 transition text-3xl">+</a>
        </div>
</main> 
<div class="max-w-4xl mx-auto py-8 px-6">
    <h1 class="text-3xl font-bold mb-6">Disparo de E-mails</h1>

    <?php if ($msg): ?>
        <div class="mb-4 p-4 rounded bg-gray-800"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <!-- Título -->
        <div>
            <label class="block mb-1 font-medium">Título do e-mail</label>
            <input type="text" name="titulo" class="w-full p-2 text-black rounded" required>
        </div>

        <!-- E-mail para notificações (não usado no backend ainda) -->
        <div>
            <label class="block mb-1 font-medium">E-mail que irá receber as notificações</label>
            <input type="email" name="notificacao"
                value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>"
                class="w-full p-2 text-black rounded" required>
        </div>

        <!-- Corpo -->
        <div>
            <label class="block mb-1 font-medium">Corpo do e-mail</label>
            <textarea name="body" rows="5" class="w-full p-2 text-black rounded" required></textarea>
        </div>

        <!-- Destinatários -->
        <div>
            <label class="block mb-2 font-medium">Destinatários</label>
            <div id="destinatariosContainer" class="space-y-4">
                <div class="flex gap-4 items-center destinatario-line">
                    <input type="email" name="destinatarios[]" placeholder="email@exemplo.com" class="flex-1 p-2 text-black rounded" required>
                    <input type="file" name="anexos_individuais[]" class="text-sm text-gray-300">
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:underline text-sm">Remover</button>
                </div>
            </div>
            <button type="button" onclick="addDestinatario()" class="mt-2 px-4 py-1 bg-white text-black rounded">+ Adicionar destinatário</button>
        </div>

        <!-- Matérias globais -->
        <div>
            <label class="block mb-1 font-medium">Matérias (anexos que irão para todos)</label>
            <input type="file" name="anexos_globais[]" multiple class="text-sm text-gray-300">
        </div>

        <!-- Enviar -->
        <div>
            <button type="submit" class="w-full py-3 bg-white text-black text-lg rounded hover:bg-gray-200 transition">Enviar</button>
        </div>
    </form>
</div>

<script>
function addDestinatario() {
    const container = document.getElementById('destinatariosContainer');
    const newRow = document.createElement('div');
    newRow.className = 'flex gap-4 items-center destinatario-line';
    newRow.innerHTML = `
        <input type="email" name="destinatarios[]" placeholder="email@exemplo.com" class="flex-1 p-2 text-black rounded" required>
        <input type="file" name="anexos_individuais[]" class="text-sm text-gray-300">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:underline text-sm">Remover</button>
    `;
    container.appendChild(newRow);
}
</script>

</body>
</html>
