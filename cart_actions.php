<?php
session_start();
require 'db_connect.php';

// Verifica se o formulário de checkout foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    
    if (empty($_SESSION['cart'])) {
        header('Location: cart.php');
        exit;
    }

    $table_number = $_POST['table_number'];
    $user_id = $_SESSION['user_id'] ?? null;
    $total_price = 0;

    // 1. Calcular o preço total
    foreach ($_SESSION['cart'] as $item) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$item['pid']]);
        $product = $stmt->fetch();
        if ($product) {
            $total_price += $product['price'];
        }
    }

    // 2. Criar a encomenda (Order)
    // Se for Staff/Admin/Configurador, vai direto para a cozinha ('pendente')
    // Caso contrário (cliente ou sem conta), vai para aprovação ('aguardando_confirmacao')
    $status = 'aguardando_confirmacao';
    if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['staff', 'admin', 'configurador'])) {
        $status = 'pendente';
    }

    // Calcular número do pedido diário (reinicia a cada dia)
    $stmtSeq = $pdo->query("SELECT MAX(daily_order_number) FROM orders WHERE DATE(created_at) = CURDATE()");
    $daily_number = ($stmtSeq->fetchColumn() ?: 0) + 1;

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, table_number, total_price, status, daily_order_number) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $table_number, $total_price, $status, $daily_number]);
    $order_id = $pdo->lastInsertId();

    // Log da ação (se a função existir)
    if (function_exists('logAction')) {
        logAction($pdo, $user_id, 'Nova Encomenda', "Encomenda #$order_id criada para a mesa $table_number");
    }

    // 3. Inserir os itens da encomenda
    foreach ($_SESSION['cart'] as $item) {
        $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
        $stmt->execute([$item['pid']]);
        $product = $stmt->fetch();

        if ($product) {
            // Processar ingredientes personalizados para guardar como texto
            $custom_ings_str = "Padrão";
            if (!empty($item['ings'])) {
                $placeholders = str_repeat('?,', count($item['ings']) - 1) . '?';
                $stmtIng = $pdo->prepare("SELECT name FROM ingredients WHERE id IN ($placeholders)");
                $stmtIng->execute(array_values($item['ings']));
                $ing_names = $stmtIng->fetchAll(PDO::FETCH_COLUMN);
                $custom_ings_str = implode(', ', $ing_names);
            }

            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, custom_ingredients) VALUES (?, ?, 1, ?, ?)");
            $stmtItem->execute([$order_id, $item['pid'], $product['price'], $custom_ings_str]);
        }
    }

    // 4. Limpar o carrinho e redirecionar
    unset($_SESSION['cart']);
    header('Location: index.php');
    exit;
}
?>