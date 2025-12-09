<?php
session_start();
require 'db_connect.php';
if(!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

// CHECKOUT
if(isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    $total = 0;
    $uid = $_SESSION['user_id'];
    
    // Create Order
    $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, 0, 'pending')")->execute([$uid]);
    $oid = $pdo->lastInsertId();

    foreach($_SESSION['cart'] as $item) {
        $stmt = $pdo->prepare("SELECT price, name FROM products WHERE id=?");
        $stmt->execute([$item['pid']]);
        $prod = $stmt->fetch();
        $total += $prod['price'];

        $ingNames = "Standard";
        
        // ERROR FIX HERE
        if(!empty($item['ings'])) {
            $placeholders = str_repeat('?,', count($item['ings']) - 1) . '?';
            
            // 1. Prepare
            $stmtIng = $pdo->prepare("SELECT name FROM ingredients WHERE id IN ($placeholders)");
            // 2. Execute
            $stmtIng->execute($item['ings']);
            // 3. Fetch
            $ingList = $stmtIng->fetchAll(PDO::FETCH_COLUMN);
            
            $ingNames = implode(', ', $ingList);
            
        } elseif ($prod['price'] > 5 && empty($item['ings'])) {
             $ingNames = "No extra ingredients";
        }

        $pdo->prepare("INSERT INTO order_items (order_id, product_id, price_at_purchase, custom_ingredients) VALUES (?, ?, ?, ?)")
            ->execute([$oid, $item['pid'], $prod['price'], $ingNames]);
    }
    $pdo->prepare("UPDATE orders SET total_price=? WHERE id=?")->execute([$total, $oid]);
    unset($_SESSION['cart']);
    echo "<script>alert('Pedido enviado! Por favor, aguarde por um funcionário.'); window.location='index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
    <title>Cart</title>
</head>
<body>
    <header>
        <nav class="nav-bar" style="justify-content: center;">
            <div class="logo" style="font-family: 'Permanent Marker', cursive; font-size: 28px; color: #f06aa6;">
                Salt Flow
            </div>
        </nav>
    </header>

    <div class="cart-container">
        <h2>Your Cart</h2>
        <div style="text-align:center; margin-bottom:20px;">
            <a href="index.php" style="color:#f06aa6; font-family:'Amatic SC'; font-size:22px; text-decoration:none;">&larr; Back to Menu</a>
        </div>
        
        <?php if(empty($_SESSION['cart'])): ?>
            <p style="color:#ccc; text-align:center; padding:40px;">Your cart is empty.</p>
        <?php else: ?>
            <div class="cart-items">
                <?php 
                $gTotal = 0;
                foreach($_SESSION['cart'] as $k => $item): 
                    $p = $pdo->query("SELECT * FROM products WHERE id=".$item['pid'])->fetch();
                    $gTotal += $p['price'];
                    
                    // Fetch ingredient names to display
                    $displayIngs = "Standard";
                    if(!empty($item['ings'])) {
                        $placeholders = str_repeat('?,', count($item['ings']) - 1) . '?';
                        $stmtIng = $pdo->prepare("SELECT name FROM ingredients WHERE id IN ($placeholders)");
                        $stmtIng->execute($item['ings']);
                        $displayIngs = implode(', ', $stmtIng->fetchAll(PDO::FETCH_COLUMN));
                    }
                ?>
                <div class="cart-item">
                    <div class="item-details">
                        <div class="item-name"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="item-desc" style="color:#aaa; font-size:14px;"><?= htmlspecialchars($displayIngs) ?></div>
                    </div>
                    <div class="item-price"><?= number_format($p['price'], 2) ?>€</div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                Total: <?= number_format($gTotal, 2) ?>€
            </div>
            
            <form method="post" style="text-align:center;">
                <button type="submit" name="checkout" class="checkout-button">Complete Order</button>
            </form>
        <?php endif; ?>
    </div>
<?php include 'footer.php'; ?>
}