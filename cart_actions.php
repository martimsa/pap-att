<?php
session_start();
if(!isset($_SESSION['user_id'])) exit;
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][] = ['pid' => $_POST['pid'], 'ings' => $_POST['ing'] ?? []];
    echo json_encode(['msg' => 'Added to cart!']);
}
?>