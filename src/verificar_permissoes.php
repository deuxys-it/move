<?php
require_once 'db.php';
require_once 'UserManager.php';

session_start();

$userManager = new UserManager($pdo);

function verificarPermissao($pagina) {
    global $userManager;
    
    switch ($pagina) {
        case 'criar_usuario':
            return $userManager->podeCriarUsuario();
            
        case 'deletar_registro':
            return $userManager->podeDeletar();
            
        case 'disparo_orcamentos':
            return $userManager->isOperador() || $userManager->isGestor() || $userManager->isAdmin();
            
        case 'gerenciar_sistema':
            return $userManager->isGestor() || $userManager->isAdmin();
            
        default:
            return false;
    }
}

function getMenuItems() {
    global $userManager;
    
    $menuItems = [];
    
    // Itens comuns a todos os usuários logados
    if (isset($_SESSION['user_id'])) {
        $menuItems[] = [
            'texto' => 'Minha Conta',
            'url' => 'minha_conta.php'
        ];
        
        // Itens específicos para administrador
        if ($userManager->isAdmin()) {
            $menuItems[] = [
                'texto' => 'Criar Usuário',
                'url' => 'criar_usuario.php'
            ];
        }
        
        // Itens para gestor e administrador
        if ($userManager->isGestor() || $userManager->isAdmin()) {
            $menuItems[] = [
                'texto' => 'Gerenciar Sistema',
                'url' => 'gerenciar_sistema.php'
            ];
        }
        
        // Itens para operador, gestor e administrador
        if ($userManager->isOperador() || $userManager->isGestor() || $userManager->isAdmin()) {
            $menuItems[] = [
                'texto' => 'Disparo de Orçamentos',
                'url' => 'disparo_orcamentos.php'
            ];
        }
    }
    
    return $menuItems;
} 