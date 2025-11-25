<?php
$host = 'localhost';
$db   = 'saltflow_db';
$user = 'root';
$pass = ''; // Coloque a password do seu MySQL aqui (no XAMPP costuma ser vazio)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>