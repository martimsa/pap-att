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
<head><meta charset="UTF-8"><link rel="stylesheet" href="style.css"><title>Staff Orders</title></head>
<body>
    <div class="admin-container">
        <h2>Pedidos Pendentes (Staff)</h2>
        <a href="index.php" class="action-link">Voltar ao Site</a>
        <?php foreach($orders as $o): ?>
            <div style="background:#222; margin:15px 0; padding:15px; border:1px solid #444;">
                <h3>Pedido #<?= $o['id'] ?> - Cliente: <?= $o['username'] ?> <span class="status-tag status-online"><?= $o['status'] ?></span></h3>
                <ul>
                    <?php 
                    $items = $pdo->prepare("SELECT p.name, oi.custom_ingredients FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE order_id=?");
                    $items->execute([$o['id']]);
                    foreach($items as $i) echo "<li>{$i['name']} <small style='color:#f06aa6'>({$i['custom_ingredients']})</small></li>"; 
                    ?>
                </ul>
                <p>
                    Ações: 
                    <a href="?oid=<?= $o['id'] ?>&st=em_preparacao" class="action-link">Preparar</a> | 
                    <a href="?oid=<?= $o['id'] ?>&st=entregue" class="action-link">Entregar</a> | 
                    <a href="?oid=<?= $o['id'] ?>&st=pago" class="action-link">Pago (Fechar)</a>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>