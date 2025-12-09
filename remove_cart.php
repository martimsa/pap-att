<?php
session_start();
if(isset($_GET['idx']) && isset($_SESSION['cart'][$_GET['idx']])) {
    unset($_SESSION['cart'][$_GET['idx']]);
    // Re-index the array to prevent issues
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}
header('Location: cart.php');
exit;