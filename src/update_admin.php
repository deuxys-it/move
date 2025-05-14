<?php
require_once 'db.php';

$senha = '123456';
$hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // Atualiza o usuário admin
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = 'admin@admin.com'");
    $stmt->execute([$hash]);
    
    // Se não existir, cria
    if ($stmt->rowCount() === 0) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, cargo, nivel_acesso_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Administrador', 'admin@admin.com', $hash, 'Administrador', 1]);
    }
    
    echo "Usuário admin atualizado com sucesso!\n";
    echo "Email: admin@admin.com\n";
    echo "Senha: " . $senha . "\n";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
} 