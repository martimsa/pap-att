{
type: uploaded file
fileName: add_to_cart.php
fullContent:
<?php
session_start();
require 'db_connect.php'; // Added db_connect require, in case it's used later
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Se a sessão do carrinho não existir, ela é criada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $produto_id = $_POST['pid']; // Adjusted to 'pid', as per the form in index.php
    
    // Captures the ingredients that remain checked
    // The form field name is 'ing[]'
    $ingredientes_ativos = isset($_POST['ing']) ? $_POST['ing'] : [];

    // Adds item to the session array
    $_SESSION['cart'][] = [
        'pid' => $produto_id, // Changed to 'pid' for consistency with cart.php
        'qty' => 1,
        'ings' => $ingredientes_ativos // Changed to 'ings' for consistency with cart.php
    ];

    echo json_encode(['success' => true, 'message' => 'Product added to cart!']);
}
?>
}