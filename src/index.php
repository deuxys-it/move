<?php
session_start();
require_once 'db.php';
require_once 'UserManager.php';

$userManager = new UserManager($pdo);

// Inclui o cabeçalho
require_once 'template/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1>Bem-vindo ao Sistema Morya</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Você está logado como: <?php echo htmlspecialchars($_SESSION['user_nome'] ?? 'Usuário'); ?></p>
        <?php else: ?>
            <p>Por favor, faça login para acessar o sistema.</p>
            <a href="login.php" class="btn btn-primary">Login</a>
        <?php endif; ?>
    </div>
</div>

<?php
// Inclui o rodapé
require_once 'template/footer.php';
?> 