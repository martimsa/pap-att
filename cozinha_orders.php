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
    <?php require 'header.php'; ?>

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