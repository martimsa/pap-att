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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
    <title>Staff Orders</title>
</head>
<body>
    <header>
        <nav class="nav-bar" style="justify-content:flex-end; padding: 1rem 3rem;">
            <a href="index.php" style="color:#f06aa6; font-size:18px; margin-right:20px;">Menu Principal</a>
            <div class="mobile-menu-icon">
                <button onclick="menuShow()">
                    <img class="icon" src="imagens/menu_white_36dp.svg" />
                </button>
            </div>
        </nav>
        <div class="mobile-menu">
            <ul>
                <li class="nav-item"><a href="index.php" class="nav-link">Menu Principal</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">Sair</a></li>
            </ul>
        </div>
    </header>

    <div class="admin-container">
        <h2>Pedidos Pendentes (Staff)</h2>
        <?php foreach($orders as $o): ?>
            <div class="staff-card">
                <h3>
                    <span>Pedido #<?= $o['id'] ?> - <?= htmlspecialchars($o['username']) ?></span>
                    <span class="status-tag status-online"><?= $o['status'] ?></span>
                </h3>
                <ul>
                    <?php 
                    $items = $pdo->prepare("SELECT p.name, oi.custom_ingredients FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE order_id=?");
                    $items->execute([$o['id']]);
                    foreach($items as $i) echo "<li>{$i['name']} <small style='color:#f06aa6'>({$i['custom_ingredients']})</small></li>"; 
                    ?>
                </ul>
                <div class="staff-actions">
                    <a href="?oid=<?= $o['id'] ?>&st=em_preparacao" class="btn-action primary">Preparar</a>
                    <a href="?oid=<?= $o['id'] ?>&st=entregue" class="btn-action success">Entregar</a>
                    <a href="?oid=<?= $o['id'] ?>&st=pago" class="btn-action finish">Pago (Fechar)</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="script.js"></script>
</body>
</html>