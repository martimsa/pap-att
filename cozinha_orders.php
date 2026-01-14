<?php
session_start();
require 'db_connect.php';

// Apenas Cozinha, Admin ou Configurador
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['cozinha', 'admin', 'configurador'])) {
    header('Location: index.php');
    exit;
}

// Processar mudança de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oid = $_POST['order_id'];
    $action = $_POST['action'];
    
    if ($action === 'start') {
        $stmt = $pdo->prepare("UPDATE orders SET status = 'em_preparacao' WHERE id = ?");
        $stmt->execute([$oid]);
    } elseif ($action === 'finish') {
        $stmt = $pdo->prepare("UPDATE orders SET status = 'entregue' WHERE id = ?");
        $stmt->execute([$oid]);
    }
    header("Location: cozinha_orders.php");
    exit;
}

// Buscar pedidos para a cozinha (Pendente ou Em Preparação)
$orders = $pdo->query("
    SELECT * FROM orders 
    WHERE status IN ('pendente', 'em_preparacao') 
    ORDER BY FIELD(status, 'em_preparacao', 'pendente'), created_at ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cozinha - Salt Flow</title>
    <link rel="stylesheet" href="style.css">
    <!-- Refresh automático a cada 30 segundos -->
    <meta http-equiv="refresh" content="30">
    <style>
        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }
        .order-card {
            background: #222;
            border: 2px solid #333;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .order-card.prep { border-color: #fbbf24; background: #2a2515; } /* Amarelo para em preparação */
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .table-num { font-family: 'Permanent Marker', cursive; font-size: 28px; color: #fff; }
        .status-badge { 
            padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase;
        }
        .status-pendente { background: #f06aa6; color: white; }
        .status-prep { background: #fbbf24; color: black; }

        .order-items { list-style: none; padding: 0; margin-bottom: 20px; flex: 1; }
        .order-items li { margin-bottom: 10px; font-size: 18px; line-height: 1.4; }
        .item-qty { font-weight: bold; color: #f06aa6; margin-right: 8px; font-size: 20px; }
        .item-custom { display: block; font-size: 14px; color: #aaa; margin-top: 4px; font-style: italic; }

        .btn-action {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-family: 'Amatic SC', cursive;
            font-size: 26px;
            font-weight: bold;
            cursor: pointer;
            color: white;
        }
        .btn-start { background: #333; border: 1px solid #555; }
        .btn-start:hover { background: #444; }
        .btn-finish { background: #4ade80; color: #064e3b; }
        .btn-finish:hover { background: #22c55e; }
    </style>
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="admin-container">
        <h2>Monitor de Cozinha</h2>
        
        <?php if(empty($orders)): ?>
            <div style="text-align:center; padding: 40px; color:#555;">
                <h3>Tudo calmo por agora...</h3>
            </div>
        <?php else: ?>
            <div class="orders-grid">
                <?php foreach($orders as $o): ?>
                    <?php 
                        $items = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $items->execute([$o['id']]);
                        $orderItems = $items->fetchAll();
                        $isPrep = ($o['status'] === 'em_preparacao');
                    ?>
                    <div class="order-card <?= $isPrep ? 'prep' : '' ?>">
                        <div class="order-header">
                            <span class="table-num">#<?= $o['daily_order_number'] ?> (Mesa <?= $o['table_number'] ?>)</span>
                            <span class="status-badge <?= $isPrep ? 'status-prep' : 'status-pendente' ?>">
                                <?= $isPrep ? 'A Preparar' : 'Pendente' ?>
                            </span>
                        </div>
                        <ul class="order-items">
                            <?php foreach($orderItems as $item): ?>
                                <li>
                                    <span class="item-qty"><?= $item['quantity'] ?></span>
                                    <?= htmlspecialchars($item['name']) ?>
                                    <?php if($item['custom_ingredients'] && $item['custom_ingredients'] !== 'Padrão'): ?>
                                        <span class="item-custom">⚠️ <?= htmlspecialchars($item['custom_ingredients']) ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <?php if(!$isPrep): ?>
                                <button type="submit" name="action" value="start" class="btn-action btn-start">COMEÇAR</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="finish" class="btn-action btn-finish">PRONTO / ENTREGAR</button>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>