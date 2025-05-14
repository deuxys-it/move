<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav class='navbar navbar-expand-lg navbar-dark bg-black border-bottom border-white mb-4 p-4'>
    <div class="container">
        <a href='dashboard.php'><img src='logo.png' class='img-fluid' style='max-width: 100px;'></a>
        <div class='navbar-nav ms-auto'>
            <?php if (isset($_SESSION['user'])): ?>
                <?php 
                $current_page = basename($_SERVER['PHP_SELF']);
                if ($current_page === 'disparo.php' || $current_page === 'certidoes.php'): 
                ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/public/fornecedores.php">Fornecedores</a>
                </li>
                <?php if ($current_page === 'disparo.php'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/public/sent.php">Histórico</a>
                </li>
                <?php endif; ?>
                <?php endif; ?>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($_SESSION['user']['foto'])): ?>
                            <img src="<?= htmlspecialchars($_SESSION['user']['foto']) ?>" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" alt="Foto do perfil">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/32x32?text=User" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" alt="Foto do perfil">
                        <?php endif; ?>
                        <?= htmlspecialchars($_SESSION['user']['nome']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="minha_conta.php">Minha Conta</a></li>
                        <li><hr class="dropdown-divider"></li>
                                      <li><a class="dropdown-item" href="criar_usuario.php">Cadastre</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Sair</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav> 