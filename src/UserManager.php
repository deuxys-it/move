<?php

class UserManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function criarUsuario($nome, $email, $senha, $cargo, $nivelAcessoId, $telefone = null) {
        // Remover a verificação de admin para liberar o cadastro para todos
        // if (!$this->isAdmin()) {
        //     throw new Exception('Apenas administradores podem criar usuários');
        // }

        // Verifica se o email já existe
        $stmt = $this->pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception('Email já cadastrado');
        }

        // Cria o novo usuário
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('
            INSERT INTO usuarios (nome, email, senha, cargo, nivel_acesso_id, telefone)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        
        return $stmt->execute([$nome, $email, $senhaHash, $cargo, $nivelAcessoId, $telefone]);
    }

    public function isAdmin() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $stmt = $this->pdo->prepare('
            SELECT na.nome 
            FROM usuarios u 
            JOIN niveis_acesso na ON u.nivel_acesso_id = na.id 
            WHERE u.id = ?
        ');
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();

        return $result && $result['nome'] === 'administrador';
    }

    public function isGestor() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $stmt = $this->pdo->prepare('
            SELECT na.nome 
            FROM usuarios u 
            JOIN niveis_acesso na ON u.nivel_acesso_id = na.id 
            WHERE u.id = ?
        ');
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();

        return $result && ($result['nome'] === 'gestor' || $result['nome'] === 'administrador');
    }

    public function isOperador() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $stmt = $this->pdo->prepare('
            SELECT na.nome 
            FROM usuarios u 
            JOIN niveis_acesso na ON u.nivel_acesso_id = na.id 
            WHERE u.id = ?
        ');
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();

        return $result && $result['nome'] === 'operador';
    }

    public function podeDeletar() {
        return $this->isAdmin();
    }

    public function podeCriarUsuario() {
        return $this->isAdmin();
    }

    public function getNiveisAcesso() {
        $stmt = $this->pdo->query('SELECT * FROM niveis_acesso');
        return $stmt->fetchAll();
    }
} 