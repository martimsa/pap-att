<?php
session_start();
require 'db_connect.php';

// CORREÇÃO: Permitir admin OU configurador
if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'configurador'])) { 
    header('Location: index.php'); 
    exit; 
}

if(isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE products SET is_active = NOT is_active WHERE id=?")->execute([$_GET['toggle']]);
    header('Location: admin_products.php');
}

$prods = $pdo->query("SELECT p.*, c.name as cname FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_deleted = 0 ORDER BY p.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Gestão de Produtos</title>
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="admin-container">
        <h2>Gestão de Produtos</h2>
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background:#f06aa6; color:white;">
                    <th>ID</th><th>Categoria</th><th>Nome</th><th>Preço</th><th>Estado</th><th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($prods as $p): ?>
                <tr style="border-bottom: 1px solid #333;">
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['cname']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= number_format($p['price'], 2) ?>€</td>
                    <td>
                        <span class="status-tag <?= $p['is_active'] ? 'status-online' : 'status-offline' ?>">
                            <?= $p['is_active'] ? 'ATIVO' : 'INATIVO' ?>
                        </span>
                    </td>
                    <td>
                        <a href="?toggle=<?= $p['id'] ?>" class="action-link">Alternar Estado</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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