<?php
session_start();
require 'db_connect.php';
if(!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

include 'header.php'; // Assumindo que tem um header.php ou inclua o HTML básico
?>
<div class="admin-container">
    <h2>O Seu Carrinho</h2>
    <?php if(empty($_SESSION['cart'])): ?>
        <p>O carrinho está vazio. <a href="index.php">Voltar ao menu.</a></p>
    <?php else: ?>
        <div class="cart-items">
            <?php 
            $gTotal = 0;
            foreach($_SESSION['cart'] as $idx => $item): 
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$item['pid']]);
                $p = $stmt->fetch();
                $gTotal += $p['price'];
                
                // Buscar nomes dos ingredientes
                $displayIngs = "Padrão";
                if(!empty($item['ings'])) {
                    $placeholders = str_repeat('?,', count($item['ings']) - 1) . '?';
                    $stmtIng = $pdo->prepare("SELECT name FROM ingredients WHERE id IN ($placeholders)");
                    $stmtIng->execute($item['ings']);
                    $displayIngs = implode(', ', $stmtIng->fetchAll(PDO::FETCH_COLUMN));
                }
            ?>
            <div class="cart-item" style="border-bottom: 1px solid #333; padding: 10px 0; display: flex; justify-content: space-between;">
                <div>
                    <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                    <small style="color:#aaa;"><?= htmlspecialchars($displayIngs) ?></small>
                </div>
                <div>
                    <span><?= number_format($p['price'], 2) ?>€</span>
                    <a href="remove_cart.php?idx=<?= $idx ?>" style="color:#f06aa6; margin-left:10px;">Remover</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary" style="margin-top:20px; text-align:right; font-size:1.2rem;">
            <strong>Total: <?= number_format($gTotal, 2) ?>€</strong>
        </div>

        <form action="cart_actions.php" method="post" style="margin-top:30px; text-align:center; background:#1b1b1b; padding:20px; border-radius:10px;">
            <label>Número da Mesa:</label><br>
            <input type="number" name="table_number" required style="color:black; padding:8px; margin:10px 0; width:80px;"><br>
            <button type="submit" name="checkout" class="checkout-button">Confirmar Pedido</button>
        </form>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>