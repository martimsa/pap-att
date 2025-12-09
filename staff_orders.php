<?php
session_start();
require 'db_connect.php';
if($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin') { header('Location: index.php'); exit; }

if(isset($_GET['st']) && isset($_GET['oid'])) {
    $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$_GET['st'], $_GET['oid']]);
    header('Location: staff_orders.php');
}

$orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id=u.id WHERE status != 'pago' ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Staff Orders</title>
</head>
<body>
    <div class="mobile-menu-overlay" onclick="menuShow()"></div>

    <header>
        <nav class="nav-bar" style="justify-content:flex-end; padding: 1rem 3rem;">
            <a href="index.php" style="color:#f06aa6; font-size:18px; margin-right:20px;" class="desktop-only">Menu Principal</a>
            <div class="mobile-menu-icon">
                <button onclick="menuShow()">
                    <img class="icon" src="imagens/menu_white_36dp.svg" alt="Menu" />
                </button>
            </div>
        </nav>
        
        <div class="mobile-menu">
            <div class="mobile-menu-header">
                <button class="mobile-menu-close" onclick="menuShow()">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <ul>
                <li class="nav-item"><a href="index.php" class="nav-link">Menu Principal</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">Sair</a></li>
            </ul>
        </div>
    </header>

    <div class="admin-container">
        <h2>Pedidos Pendentes (Staff)</h2>
        <?php foreach($orders as $o): ?>
            <div class="admin-order-card">
                <h3>
                    <span>Pedido #<?= $o['id'] ?> - <small><?= htmlspecialchars($o['username']) ?></small></span>
                    <span class="status-tag status-online"><?= htmlspecialchars($o['status']) ?></span>
                </h3>
                <ul>
                    <?php 
                    $items = $pdo->prepare("SELECT p.name, oi.custom_ingredients FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE order_id=?");
                    $items->execute([$o['id']]);
                    foreach($items as $i) echo "<li>" . htmlspecialchars($i['name']) . " <small style='color:#f06aa6'>(" . htmlspecialchars($i['custom_ingredients']) . ")</small></li>"; 
                    ?>
                </ul>
                <div class="order-actions">
                    <a href="?oid=<?= $o['id'] ?>&st=em_preparacao" class="action-link action-btn">Preparar</a>
                    <a href="?oid=<?= $o['id'] ?>&st=entregue" class="action-link action-btn">Entregar</a>
                    <a href="?oid=<?= $o['id'] ?>&st=pago" class="action-link action-btn action-btn--close">Pago (Fechar)</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function menuShow() {
            let menuMobile = document.querySelector('.mobile-menu');
            let overlay = document.querySelector('.mobile-menu-overlay');
            
            if (menuMobile.classList.contains('open')) {
                menuMobile.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            } else {
                menuMobile.classList.add('open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
    </script>
</body>
</html>