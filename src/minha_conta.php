<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/db.php';

$user = $_SESSION['user'];

// Salvar alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $user['id'];
    $foto = $user['foto'] ?? null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoPath = 'assinaturas/' . uniqid('foto_') . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/' . $fotoPath);
        $foto = $fotoPath;
    }
    $stmt = $pdo->prepare('UPDATE usuarios SET nome=?, email=?, telefone=?, cargo=?, foto=?, smtp_host=?, smtp_port=?, smtp_user=?, smtp_pass=?, smtp_secure=? WHERE id=?');
    $stmt->execute([
        $_POST['nome'],
        $_POST['email'],
        $_POST['telefone'],
        $_POST['cargo'],
        $foto,
        $_POST['smtp_host'],
        $_POST['smtp_port'],
        $_POST['smtp_user'],
        $_POST['smtp_pass'],
        $_POST['smtp_secure'],
        $id
    ]);
    // Atualiza sessão
    $user = $pdo->query('SELECT * FROM usuarios WHERE id = ' . intval($id))->fetch();
    $_SESSION['user'] = $user;
    header('Location: minha_conta.php?ok=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha Conta - Morya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid white;
        }
        .btn-custom {
            background-color: white!important;
            color: black;
            border: 1px solid white;
        }
        .btn-custom:hover {
            background-color: black;
            color: white;
            border: 1px solid white;
        }
    </style>
</head>
<body class="bg-black text-white min-vh-100 d-flex flex-column">
<?php include __DIR__ . '/template/header.php'; ?>
<main class="container py-5">
    <h1 class="mb-4">Minha Conta</h1>
    <div class="alert alert-info">Para que o disparo de e-mails funcione corretamente, preencha todos os campos de SMTP abaixo com os dados do seu provedor de e-mail. Caso algum campo esteja incorreto, o envio de e-mails não funcionará.</div>
    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success">Dados atualizados com sucesso!</div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="row g-4" style="max-width: 600px;">
        <div class="col-12 text-center mb-4">
            <?php if (!empty($user['foto'])): ?>
                <img src="<?= htmlspecialchars($user['foto']) ?>" class="profile-img mb-2" alt="Foto do perfil">
            <?php else: ?>
                <img src="https://via.placeholder.com/120x120?text=Foto" class="profile-img mb-2" alt="Foto do perfil">
            <?php endif; ?>
            <div>
                <input type="file" name="foto" class="form-control bg-dark text-white mt-2" accept="image/*">
            </div>
        </div>
        <div class="col-12">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control bg-dark text-white" value="<?= htmlspecialchars($user['nome'] ?? '') ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control bg-dark text-white" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control bg-dark text-white" value="<?= htmlspecialchars($user['telefone'] ?? '') ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Cargo</label>
            <input type="text" name="cargo" class="form-control bg-dark text-white" value="<?= htmlspecialchars($user['cargo'] ?? '') ?>">
        </div>
        <div class="col-12">
            <label class="form-label">SMTP Host</label>
            <input type="text" name="smtp_host" class="form-control bg-dark text-white" value="<?= htmlspecialchars($user['smtp_host'] ?? '') ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">SMTP Porta</label>
            <input type="number" name="smtp_port" class="form-control bg-dark text-white" value="<?= htmlspecialchars($user['smtp_port'] ?? '') ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">SMTP Usuário</label>
            <input type="text" name="smtp_user" class="form-control bg-dark text-white" value="<?= htmlspecialchars($user['smtp_user'] ?? '') ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">SMTP Senha</label>
            <input type="text" name="smtp_pass" class="form-control bg-dark text-white" value="<?= htmlspecialchars($user['smtp_pass'] ?? '') ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">SMTP Segurança</label>
            <select name="smtp_secure" class="form-select bg-dark text-white" required>
                <option value="tls" <?= (isset($user['smtp_secure']) && $user['smtp_secure'] === 'tls') ? 'selected' : '' ?>>TLS</option>
                <option value="ssl" <?= (isset($user['smtp_secure']) && $user['smtp_secure'] === 'ssl') ? 'selected' : '' ?>>SSL</option>
                <option value="" <?= (empty($user['smtp_secure'])) ? 'selected' : '' ?>>Nenhuma</option>
            </select>
        </div>
        <div class="col-12 d-flex justify-content-between mt-4">
            <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
            <button type="submit" class="btn btn-custom">Salvar Alterações</button>
        </div>
    </form>
</main>
<?php include __DIR__ . '/template/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewFoto(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const preview = document.querySelector('.profile-img');
        preview.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

document.querySelector('input[name="foto"]').addEventListener('change', previewFoto);
</script>
</body>
</html> 