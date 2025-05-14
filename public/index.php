<?php
session_start();
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/UserManager.php';

$userManager = new UserManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user'] = $user;
        header('Location: dashboard.php');
        exit;
    }

    $erro = "Credenciais inválidas.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Morya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background-color: rgba(0, 0, 0, 0.8);
            border: 1px solid white;
            border-radius: 10px;
        }
        .form-control {
            background-color: black;
            color: white;
            border: 1px solid white;
            padding: 0.75rem;
        }
        .form-control:focus {
            background-color: black;
            color: white;
            border-color: white;
            box-shadow: none;
        }
        .btn-custom {
            background-color: white;
            color: black;
            border: 1px solid white;
            padding: 0.75rem;
            font-weight: 500;
        }
        .btn-custom:hover {
            background-color: black;
            color: white;
            border: 1px solid white;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="bg-black text-white min-vh-100 d-flex justify-content-center align-items-center">
    <div class="login-container">
        <div class="text-center mb-4">
            <img src="logo.png" alt="Logo Morya" class="logo">
        </div>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger mb-4"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-4">
                <input type="password" name="password" class="form-control" placeholder="Senha" required>
            </div>
            <button type="submit" class="btn btn-custom w-100">Entrar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

