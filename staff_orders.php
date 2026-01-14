<?php
session_start();
require 'db_connect.php';

// Apenas Staff, Admin ou Configurador
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin', 'configurador'])) {
    header('Location: index.php');
    exit;
}

// Processar ações (confirmar ou remover)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_order'])) {
        $oid = $_POST['order_id'];
        // Passa para 'pendente' (para a cozinha ver)
        $stmt = $pdo->prepare("UPDATE orders SET status = 'pendente' WHERE id = ?");
        $stmt->execute([$oid]);
        header("Location: staff_orders.php");
        exit;
    } elseif (isset($_POST['remove_order'])) {
        $oid = $_POST['order_id'];
        // Remover itens do pedido e o próprio pedido
        $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$oid]);
        $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$oid]);
        header("Location: staff_orders.php");
        exit;
    }
}

// Buscar pedidos aguardando confirmação
$orders = $pdo->query("
    SELECT * FROM orders 
    WHERE status = 'aguardando_confirmacao' 
    ORDER BY created_at ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff - Pedidos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }
        .order-card {
            background: #222;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #444;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .table-num {
            font-family: 'Permanent Marker', cursive;
            font-size: 24px;
            color: #f06aa6;
        }
        .order-time { font-size: 14px; color: #aaa; }
        .order-items { list-style: none; padding: 0; margin-bottom: 20px; }
        .order-items li {
            margin-bottom: 8px;
            border-bottom: 1px dashed #333;
            padding-bottom: 8px;
        }
        .item-qty { font-weight: bold; color: #f06aa6; margin-right: 5px; }
        .item-custom { display: block; font-size: 12px; color: #888; margin-top: 2px; }
        .btn-confirm {
            width: 100%;
            background: #f06aa6;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-family: 'Amatic SC', cursive;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-confirm:hover { background: #d44e8a; }
    </style>
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="admin-container">
        <h2>Pedidos Pendentes (Staff)</h2>
        
        <?php if(empty($orders)): ?>
            <p style="text-align:center; color:#777;">Sem novos pedidos para confirmar.</p>
        <?php else: ?>
            <div class="orders-grid">
                <?php foreach($orders as $o): ?>
                    <?php 
                        // Buscar itens deste pedido
                        $items = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $items->execute([$o['id']]);
                        $orderItems = $items->fetchAll();
                    ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span class="table-num">Mesa <?= $o['table_number'] ?></span>
                            <span class="order-time"><?= date('H:i', strtotime($o['created_at'])) ?></span>
                        </div>
                        <ul class="order-items">
                            <?php foreach($orderItems as $item): ?>
                                <li>
                                    <span class="item-qty"><?= $item['quantity'] ?>x</span>
                                    <?= htmlspecialchars($item['name']) ?>
                                    <?php if($item['custom_ingredients'] && $item['custom_ingredients'] !== 'Padrão'): ?>
                                        <span class="item-custom">Obs: <?= htmlspecialchars($item['custom_ingredients']) ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <form method="POST" style="display: flex; gap: 10px;">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <button type="submit" name="confirm_order" class="btn-confirm" style="flex: 2;">ENVIAR P/ COZINHA</button>
                            <button type="submit" name="remove_order" class="btn-confirm" style="flex: 1; background-color: #f87171;" onclick="return confirm('Tem a certeza que deseja remover este pedido?');">REMOVER</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="script.js"></script>
</body>
</html>