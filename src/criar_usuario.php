<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/UserManager.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userManager = new UserManager($pdo);

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $cargo = $_POST['cargo'] ?? '';
        $nivelAcessoId = $_POST['nivel_acesso_id'] ?? '';
        $telefone = $_POST['telefone'] ?? '';

        if (empty($nome) || empty($email) || empty($senha) || empty($cargo) || empty($nivelAcessoId)) {
            throw new Exception('Todos os campos obrigatórios devem ser preenchidos');
        }

        if ($userManager->criarUsuario($nome, $email, $senha, $cargo, $nivelAcessoId, $telefone)) {
            $mensagem = 'Usuário criado com sucesso!';
        }
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}

$niveisAcesso = $userManager->getNiveisAcesso();
require_once __DIR__ . '/template/header.php';
?>
<body class="bg-black">
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-6">
        <div class="card bg-black text-white border-0">
            <div class="card-header bg-black text-white border-0">
                <h2 class="text-center">Criar Novo Usuário</h2>
            </div>
            <div class="card-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($mensagem); ?></div>
                <?php endif; ?>
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>
                <form method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control bg-dark text-white border-secondary" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control bg-dark text-white border-secondary" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control bg-dark text-white border-secondary" id="senha" name="senha" required>
                    </div>
                    <div class="mb-3">
                        <label for="cargo" class="form-label">Setor</label>
                        <select class="form-control bg-dark text-white border-secondary" id="cargo" name="cargo" required>
                            <option value="">Selecione...</option>
                            <option value="criação">Criação</option>
                            <option value="produção">Produção</option>
                            <option value="mídia">Mídia</option>
                            <option value="atendimento">Atendimento</option>
                            <option value="financeiro">Financeiro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="tel" class="form-control bg-dark text-white border-secondary" id="telefone" name="telefone">
                    </div>
                    <div class="mb-3">
                        <label for="nivel_acesso_id" class="form-label">Nível de Acesso</label>
                        <?php if (empty($niveisAcesso)): ?>
                            <div class="alert alert-danger">Nenhum nível de acesso cadastrado. Cadastre pelo menos um nível de acesso no banco de dados.</div>
                        <?php else: ?>
                            <select class="form-control bg-dark text-white border-secondary" id="nivel_acesso_id" name="nivel_acesso_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($niveisAcesso as $nivel): ?>
                                    <?php if ($nivel['nome'] !== 'administrador'): ?>
                                        <option value="<?php echo $nivel['id']; ?>">
                                            <?php echo htmlspecialchars(ucfirst($nivel['nome'])); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Criar Usuário</button>
                        <a href="index.php" class="btn btn-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
<?php require_once __DIR__ . '/template/footer.php'; ?> 