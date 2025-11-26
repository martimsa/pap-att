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
            $ids = implode(',', $item['ings']);
            $ingNames = implode(', ', $pdo->query("SELECT name FROM ingredients WHERE id IN ($ids)")->fetchAll(PDO::FETCH_COLUMN));
        } elseif ($prod['price'] > 5 && empty($item['ings'])) {
             // Lógica para comida que teve tudo removido
             $ingNames = "Sem ingredientes extra";
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
<head><meta charset="UTF-8"><link rel="stylesheet" href="style.css"><title>Carrinho</title></head>
<body>
    <div class="admin-container">
        <h2>O seu Carrinho</h2>
        <a href="index.php" style="color:#f06aa6">Voltar ao Menu</a>
        <?php if(empty($_SESSION['cart'])): ?>
            <p>Carrinho vazio.</p>
        <?php else: ?>
            <table>
                <tr><th>Produto</th><th>Detalhes</th><th>Preço</th></tr>
                <?php 
                $gTotal = 0;
                foreach($_SESSION['cart'] as $item): 
                    $p = $pdo->query("SELECT * FROM products WHERE id=".$item['pid'])->fetch();
                    $gTotal += $p['price'];
                ?>
                <tr>
                    <td><?= $p['name'] ?></td>
                    <td>IDs Ingredientes: <?= implode(', ', $item['ings']) ?: 'Standard/Nenhum' ?></td>
                    <td><?= $p['price'] ?>€</td>
                </tr>
                <?php endforeach; ?>
            </table>
            <h3>Total: <?= $gTotal ?>€</h3>
            <form method="post"><button type="submit" name="checkout" class="checkout-button">Finalizar Pedido</button></form>
        <?php endif; ?>
    </div>
</body>
</html>