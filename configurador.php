<?php
session_start();
require 'db_connect.php';
if($_SESSION['role'] !== 'configurador') { header('Location: index.php'); exit; }

// Ver produtos apagados pelo Admin
$deletedProds = $pdo->query("SELECT * FROM products WHERE is_deleted = 1")->fetchAll();
// Ver utilizadores excluídos
$deletedUsers = $pdo->query("SELECT * FROM users WHERE is_deleted = 1")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head><link rel="stylesheet" href="style.css"><title>Painel do Configurador</title></head>
<body>
    <header>
        <nav class="nav-bar">
            <div class="logo">
                <a href="index.php"><img src="imagens/logo.png" width="40px" alt="Logo"/></a>
            </div>
            <div class="nav-list">
                <ul>
                    <li><a href="index.php" class="nav-link">Menu Principal</a></li>
                    <li><a href="staff_orders.php" class="nav-link">Staff</a></li>
                    <li><a href="cozinha_orders.php" class="nav-link">Cozinha</a></li>
                    <li><a href="admin_products.php" class="nav-link">Produtos</a></li>
                    <li><a href="admin_users.php" class="nav-link">Utilizadores</a></li>
                    <li><a href="configurador.php" class="nav-link" style="color:#f06aa6">Config</a></li>
                </ul>
            </div>
            <div class="header-icons">
                <span class="header-greeting">Olá, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php" class="header-logout-link" style="font-size: 14px; margin-left: 10px;">(Sair)</a>
                <div class="mobile-menu-icon">
                    <button onclick="menuShow()"><img class="icon" src="imagens/menu_white_36dp.svg" alt="Menu"></button>
                </div>
            </div>
        </nav>
        <div class="mobile-menu">
            <ul>
                <li><a href="index.php" class="nav-link">Menu Principal</a></li>
                <li><a href="staff_orders.php" class="nav-link">Staff</a></li>
                <li><a href="cozinha_orders.php" class="nav-link">Cozinha</a></li>
                <li><a href="admin_products.php" class="nav-link">Produtos</a></li>
                <li><a href="admin_users.php" class="nav-link">Utilizadores</a></li>
                <li><a href="configurador.php" class="nav-link" style="color:#f06aa6">Config</a></li>
                <li><a href="logout.php" class="nav-link">Sair</a></li>
            </ul>
        </div>
    </header>

    <div class="admin-container">
        <h1>Painel Mestre (Configurador)</h1>
        
        <h3>Produtos Apagados (Soft Deleted)</h3>
        <ul>
            <?php foreach($deletedProds as $dp) echo "<li>" . $dp['name'] . "</li>"; ?>
        </ul>

        <h3>Utilizadores Inativos/Excluídos</h3>
        <ul>
            <?php foreach($deletedUsers as $du) echo "<li>" . $du['username'] . " (" . $du['email'] . ")</li>"; ?>
        </ul>
    </div>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">Salt Flow Bar</div>
            <div class="footer-links">
                <span>© <?= date('Y') ?> Salt Flow Beach Bar</span>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>