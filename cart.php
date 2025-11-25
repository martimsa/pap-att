<?php
session_start();
require 'db_connect.php';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart - Salt Flow</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
</head>
<body>
    <header>
      <nav class="nav-bar">
        <div class="logo"><a href="index.php"><img class="logo-ft" src="imagens/logo_menu.jpg" width="40px"/></a></div>
        <div class="nav-list"><ul><li class="nav-item"><a href="index.php" class="nav-link">Home</a></li></ul></div>
      </nav>
    </header>

    <main class="cart-container">
        <h2>Shopping Cart</h2>
        <div class="cart-items">
            
            <?php if(empty($cart)): ?>
                <p class="empty-cart-message">Your cart is empty.</p>
            <?php else: ?>
                
                <?php foreach($cart as $index => $item): 
                    // 1. Buscar info do produto
                    $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
                    $stmt->execute([$item['product_id']]);
                    $prod = $stmt->fetch();
                    
                    // 2. Buscar nomes dos ingredientes escolhidos
                    $ing_names = "Standard";
                    if(!empty($item['ingredients'])) {
                        // Converte array [1, 3] em string "1,3" para a query
                        $ids = implode(',', array_map('intval', $item['ingredients']));
                        $stmtIng = $pdo->query("SELECT name FROM ingredients WHERE id IN ($ids)");
                        $ing_rows = $stmtIng->fetchAll(PDO::FETCH_COLUMN);
                        $ing_names = implode(', ', $ing_rows);
                    } elseif ($prod['price'] > 5 && empty($item['ingredients'])) {
                        // Lógica visual: se for caro e não tem ingredientes, talvez tenha removido tudo
                         $ing_names = "No extra ingredients";
                    }

                    $subtotal = $prod['price'];
                    $total += $subtotal;
                ?>

                <div class="cart-item">
                    <div class="item-details">
                        <span class="item-name"><?= $prod['name'] ?></span>
                        <div class="item-desc" style="font-size: 13px; color: #f06aa6;">
                            Include: <?= $ing_names ?>
                        </div>
                    </div>
                    <div class="item-price"><?= number_format($prod['price'], 2) ?>€</div>
                    <a href="remove_cart.php?idx=<?= $index ?>" class="remove-item-btn">Remove</a>
                </div>

                <?php endforeach; ?>
            <?php endif; ?>

        </div>
        <div class="cart-summary">
            <h3>Total: <span><?= number_format($total, 2) ?>€</span></h3>
            <?php if(!empty($cart)): ?>
                <button class="checkout-button">Proceed to Checkout</button>
            <?php else: ?>
                <button class="checkout-button" disabled>Proceed to Checkout</button>
            <?php endif; ?>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>