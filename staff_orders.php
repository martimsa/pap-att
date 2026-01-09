<?php
session_start();
require 'db_connect.php';
if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin', 'configurador'])) { header('Location: index.php'); exit; }

if(isset($_GET['confirmar'])) {
    $pdo->prepare("UPDATE orders SET status='pendente' WHERE id=?")->execute([$_GET['confirmar']]);
    header('Location: staff_orders.php');
}

$orders = $pdo->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id=u.id WHERE o.status = 'aguardando_confirmacao' ORDER BY o.id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head><link rel="stylesheet" href="style.css"><title>Staff - Validar Pedidos</title></head>
<body>
    <?php require 'header.php'; ?>

    <div class="admin-container">
        <h2>Novos Pedidos para Validar</h2>
        <?php if(!$orders): ?><p>Não há pedidos pendentes de validação.</p><?php endif; ?>
        
        <?php foreach($orders as $o): ?>
            <div class="admin-order-card">
                <h3>Mesa: <?= $o['table_number'] ?> | Pedido #<?= $o['id'] ?></h3>
                <ul>
                    <?php 
                    $items = $pdo->prepare("SELECT p.name, oi.custom_ingredients FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE order_id=?");
                    $items->execute([$o['id']]);
                    foreach($items as $i) echo "<li>" . htmlspecialchars($i['name']) . " <small>(" . $i['custom_ingredients'] . ")</small></li>"; 
                    ?>
                </ul>
                <div class="order-actions">
                    <a href="?confirmar=<?= $o['id'] ?>" class="action-link action-btn" style="background:#2ecc71; color:white;">CONFIRMAR E ENVIAR COZINHA</a>
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