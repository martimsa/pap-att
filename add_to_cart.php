{
type: uploaded file
fileName: add_to_cart.php
fullContent:
<?php
session_start();
require 'db_connect.php'; // Adicionei require do db_connect, caso seja usado mais tarde
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Se a sessão do carrinho não existir, ela é criada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $produto_id = $_POST['pid']; // Ajustado para 'pid', conforme o formulário em index.php
    
    // Captura os ingredientes que continuam marcados (checked)
    // O nome do campo do formulário é 'ing[]'
    $ingredientes_ativos = isset($_POST['ing']) ? $_POST['ing'] : [];

    // Adiciona item ao array de sessão
    $_SESSION['cart'][] = [
        'pid' => $produto_id, // Alterado para 'pid' para consistência com cart.php
        'qty' => 1,
        'ings' => $ingredientes_ativos // Alterado para 'ings' para consistência com cart.php
    ];

    echo json_encode(['success' => true, 'message' => 'Produto adicionado ao carrinho!']);
}
?>
}