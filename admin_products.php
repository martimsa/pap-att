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
                <li class="nav-item"><a href="logout.php" class="nav-link">Saír</a></li>
            </ul>
        </div>
    </header>
    
    <div class="admin-container">
        <h2>Gerir Produtos (Admin)</h2>
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

<?php include 'footer.php'; ?>