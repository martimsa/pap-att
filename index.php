<?php
session_start();
require 'db_connect.php';

$cats = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

function getProducts($pdo, $catId) {
    $sql = "SELECT p.*, GROUP_CONCAT(i.id) as i_ids, GROUP_CONCAT(i.name SEPARATOR ', ') as i_names 
            FROM products p 
            LEFT JOIN product_ingredients pi ON p.id = pi.product_id
            LEFT JOIN ingredients i ON pi.ingredient_id = i.id
            WHERE p.category_id = ? AND p.is_active = 1 AND p.is_deleted = 0
            GROUP BY p.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Salt Flow ≋ Beach Bar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="menu-board">
        <div class="brand">Salt Flow ≋ Beach Bar</div>
        <?php foreach($cats as $c): ?>
            <div class="category" id="<?= $c['slug'] ?>"><?= $c['name'] ?></div>
            <?php foreach(getProducts($pdo, $c['id']) as $p): ?>
                <div class="item">
                    <div>
                        <div class="item-name"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="item-desc"><?= $p['i_names'] ?: htmlspecialchars($p['description']) ?></div>
                    </div>
                    <div class="actions">
                        <span class="price"><?= number_format($p['price'], 2) ?>€</span>
                        <button class="btn-pedir" onclick="openModal(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', '<?= $p['i_ids'] ?>', '<?= addslashes($p['i_names'] ?? '') ?>')">Pedir</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <div id="ingModal" class="modal-overlay" style="display:none;">
        <form id="cartForm" class="modal-box">
            <h3 id="mTitle" class="modal-title"></h3>
            <p class="modal-subtitle">Personalize os seus ingredientes:</p>
            <input type="hidden" name="pid" id="mPid">
            <div id="mList" class="modal-ingredients-list"></div>
            <div class="modal-btns">
                <button type="button" class="btn-cancel" onclick="document.getElementById('ingModal').style.display='none'">Cancelar</button>
                <button type="submit" class="checkout-button checkout-button--modal">Adicionar ao Carrinho</button>
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?>