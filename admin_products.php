<?php
session_start();
require 'db_connect.php';
if($_SESSION['role'] !== 'admin') { header('Location: index.php'); exit; }

if(isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE products SET is_active = NOT is_active WHERE id=?")->execute([$_GET['toggle']]);
    header('Location: admin_products.php');
}
$prods = $pdo->query("SELECT p.*, c.name as cname FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><link rel="stylesheet" href="style.css"><title>Admin Produtos</title></head>
<body>
    <div class="admin-container">
        <h2>Gerir Produtos (Admin)</h2>
        <a href="index.php" class="action-link">Voltar ao Site</a>
        <table>
            <tr><th>ID</th><th>Categoria</th><th>Nome</th><th>Estado</th><th>Ação</th></tr>
            <?php foreach($prods as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= $p['cname'] ?></td>
                <td><?= $p['name'] ?></td>
                <td><span class="status-tag <?= $p['is_active']?'status-online':'status-offline' ?>"><?= $p['is_active']?'ONLINE':'OFFLINE' ?></span></td>
                <td><a href="?toggle=<?= $p['id'] ?>" class="action-link">Trocar Estado</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>