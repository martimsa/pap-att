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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
    <title>Admin Products</title>
</head>
<body>
    <header>
        <nav class="nav-bar" style="justify-content:flex-end; padding: 1rem 3rem;">
            <a href="index.php" style="color:#f06aa6; font-size:18px; margin-right:20px;">Main Menu</a>
            <div class="mobile-menu-icon">
                <button onclick="menuShow()">
                    <img class="icon" src="imagens/menu_white_36dp.svg" />
                </button>
            </div>
        </nav>
        <div class="mobile-menu">
            <ul>
                <li class="nav-item"><a href="index.php" class="nav-link">Main Menu</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </header>
    
    <div class="admin-container">
        <h2>Manage Products (Admin)</h2>
        <table class="responsive-table">
            <thead>
                <tr><th>ID</th><th>Category</th><th>Name</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach($prods as $p): ?>
            <tr>
                <td data-label="ID"><?= $p['id'] ?></td>
                <td data-label="Category"><?= $p['cname'] ?></td>
                <td data-label="Name"><?= $p['name'] ?></td>
                <td data-label="Status"><span class="status-tag <?= $p['is_active']?'status-online':'status-offline' ?>"><?= $p['is_active']?'ONLINE':'OFFLINE' ?></span></td>
                <td data-label="Action"><a href="?toggle=<?= $p['id'] ?>" class="action-link btn-pedir" style="font-size:14px; padding: 6px 12px;">Toggle Status</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="script.js"></script>
</body>
</html>