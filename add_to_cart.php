<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // If the cart session does not exist, it is created
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $produto_id = $_POST['product_id'];
    
    // Captura os ingredientes que continuam marcados (checked)
    // Se o array vier vazio, significa que o user removeu tudo ou não tinha ingredientes
    $ingredientes_ativos = isset($_POST['ingredientes']) ? $_POST['ingredientes'] : [];

    // Adiciona item ao array de sessão
    $_SESSION['cart'][] = [
        'product_id' => $produto_id,
        'quantity' => 1,
        'ingredients' => $ingredientes_ativos
    ];

    echo json_encode(['success' => true, 'message' => 'Product added to cart!']);
}
?>