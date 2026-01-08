<?php
session_start();
require 'db_connect.php';
if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['cozinha', 'admin', 'configurador'])) { header('Location: index.php'); exit; }

if(isset($_GET['st']) && isset($_GET['oid'])) {
    $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$_GET['st'], $_GET['oid']]);
    header('Location: cozinha_orders.php');
}

// Cozinha vê tudo o que já foi confirmado pelo staff até ser pago
$orders = $pdo->query("SELECT * FROM orders WHERE status IN ('pendente', 'em_preparacao', 'entregue') ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head><link rel="stylesheet" href="style.css"><title>Cozinha - Pedidos</title></head>
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
                    <?php if($_SESSION['role'] === 'configurador'): ?>
                        <li><a href="configurador.php" class="nav-link" style="color:#f06aa6">Config</a></li>
                    <?php endif; ?>
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
                <?php if($_SESSION['role'] === 'configurador'): ?>
                    <li><a href="configurador.php" class="nav-link" style="color:#f06aa6">Config</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="nav-link">Sair</a></li>
            </ul>
        </div>
    </header>

    <div class="admin-container">
        <h2>Pedidos em Preparação</h2>
        <?php foreach($orders as $o): ?>
            <div class="admin-order-card" style="border-left: 10px solid #f06aa6;">
                <h3>Mesa <?= $o['table_number'] ?> | <span style="color:#f06aa6"><?= strtoupper($o['status']) ?></span></h3>
                <ul>
                    <?php 
                    $items = $pdo->prepare("SELECT p.name, oi.custom_ingredients FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE order_id=?");
                    $items->execute([$o['id']]);
                    foreach($items as $i) echo "<li>" . htmlspecialchars($i['name']) . " <br><small style='color:#aaa;'>Ingredientes: " . $i['custom_ingredients'] . "</small></li>"; 
                    ?>
                </ul>
                <div class="order-actions">
                    <?php if($o['status'] == 'pendente'): ?>
                        <a href="?oid=<?= $o['id'] ?>&st=em_preparacao" class="action-link action-btn">Começar a Preparar</a>
                    <?php elseif($o['status'] == 'em_preparacao'): ?>
                        <a href="?oid=<?= $o['id'] ?>&st=entregue" class="action-link action-btn" style="background:#3498db;">Marcar como Entregue</a>
                    <?php elseif($o['status'] == 'entregue'): ?>
                        <a href="?oid=<?= $o['id'] ?>&st=pago" class="action-link action-btn" style="background:#27ae60;">Pedido Pago / Finalizar</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
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