<?php
session_start();
require 'db_connect.php';

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - Salt Flow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="cart-container">
        <h2>O Seu Carrinho</h2>
        <?php if(empty($_SESSION['cart'])): ?>
            <p style="text-align: center; font-size: 18px;">O seu carrinho está vazio. <a href="index.php" style="color: #f06aa6;">Ver o menu</a>.</p>
        <?php else: ?>
            <div class="cart-items">
                <?php 
                $gTotal = 0;
                foreach($_SESSION['cart'] as $idx => $item): 
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->execute([$item['pid']]);
                    $p = $stmt->fetch();
                    $gTotal += $p['price'];
                    
                    $displayIngs = "Padrão";
                    if(!empty($item['ings'])) {
                        $placeholders = str_repeat('?,', count($item['ings']) - 1) . '?';
                        $stmtIng = $pdo->prepare("SELECT name FROM ingredients WHERE id IN ($placeholders)");
                        $stmtIng->execute(array_values($item['ings']));
                        $displayIngs = implode(', ', $stmtIng->fetchAll(PDO::FETCH_COLUMN));
                    }
                ?>
                <div class="cart-item">
                    <div class="item-details">
                        <div class="item-name"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="item-desc"><?= htmlspecialchars($displayIngs) ?></div>
                    </div>
                    <div class="item-price"><?= number_format($p['price'], 2) ?>€</div>
                    <a href="remove_cart.php?idx=<?= $idx ?>" class="action-link" style="color:#f87171;">Remover</a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                Total: <?= number_format($gTotal, 2) ?>€
            </div>

            <form action="cart_actions.php" method="post" style="margin-top:30px; text-align:center; background:#1b1b1b; padding:20px; border-radius:10px;">
                <label for="table_number" style="font-size: 18px; display:block; margin-bottom:10px;">Número da sua Mesa:</label>
                <input type="text" id="table_number" name="table_number" required style="color:black; padding:10px; border-radius: 5px; border:none; width: 100px; text-align:center; font-size:18px;"><br>
                <button type="submit" name="checkout" class="checkout-button" style="margin-top:20px;">Confirmar Pedido</button>
            </form>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>