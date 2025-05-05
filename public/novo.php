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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white min-h-screen flex flex-col">
<nav class='flex justify-between items-center border-b border-white mb-6 p-4'>
        <img src='logo.png' class='h-10'>
        <div class='flex gap-4'>
            <a href='dashboard.php'>Início</a>
            <a href='disparo.php'>Enviar E-mails</a>
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

</body>
</html>
