<?php
session_start();
require 'db_connect.php';
if(!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

// CHECKOUT
if(isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    $total = 0;
    $uid = $_SESSION['user_id'];
    
    // Criar Encomenda
    $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, 0, 'pendente')")->execute([$uid]);
    $oid = $pdo->lastInsertId();

    foreach($_SESSION['cart'] as $item) {
        $stmt = $pdo->prepare("SELECT price, name FROM products WHERE id=?");
        $stmt->execute([$item['pid']]);
        $prod = $stmt->fetch();
        $total += $prod['price'];

        $ingNames = "Standard";
        if(!empty($item['ings'])) {
            // Garante que só ingredientes que existem (checkados no modal) são usados.
            $placeholders = str_repeat('?,', count($item['ings']) - 1) . '?';
            $ingNames = implode(', ', $pdo->prepare("SELECT name FROM ingredients WHERE id IN ($placeholders)")->execute($item['ings'])->fetchAll(PDO::FETCH_COLUMN) ?? []);
        } elseif ($prod['price'] > 5 && empty($item['ings'])) {
             // Lógica para comida que teve todos os ingredientes removidos.
             $ingNames = "No extra ingredients";
        }

        $pdo->prepare("INSERT INTO order_items (order_id, product_id, price_at_purchase, custom_ingredients) VALUES (?, ?, ?, ?)")
            ->execute([$oid, $item['pid'], $prod['price'], $ingNames]);
    }
    $pdo->prepare("UPDATE orders SET total_price=? WHERE id=?")->execute([$total, $oid]);
    unset($_SESSION['cart']);
    echo "<script>alert('Pedido enviado! Aguarde staff.'); window.location='index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><link rel="stylesheet" href="style.css"><title>Cart</title></head>
<body>
    <div class="admin-container">
        <h2>Your Cart</h2>
        <a href="index.php" style="color:#f06aa6">Back to Menu</a>
        <?php if(empty($_SESSION['cart'])): ?>
            <p style="color:#ccc; margin-top:15px;">Empty cart.</p>
        <?php else: ?>
            <table>
                <tr><th>Product</th><th>Details</th><th>Price</th></tr>
                <?php 
                $gTotal = 0;
                foreach($_SESSION['cart'] as $item): 
                    $p = $pdo->query("SELECT * FROM products WHERE id=".$item['pid'])->fetch();
                    $gTotal += $p['price'];
                ?>
                <tr>
                    <td><?= $p['name'] ?></td>
                    <td>IDs Ingredientes: <?= implode(', ', $item['ings']) ?: 'Standard/Nenhum' ?></td>
                    <td><?= number_format($p['price'], 2) ?>€</td>
                </tr>
                <?php endforeach; ?>
            </table>
            <h3>Total: <?= number_format($gTotal, 2) ?>€</h3>
            <form method="post"><button type="submit" name="checkout" class="checkout-button">Complete Order</button></form>
        <?php endif; ?>
    </div>
<?php include 'footer.php'; ?>