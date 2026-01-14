<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'configurador'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? null;
$ingredient = ['name' => '', 'is_deleted' => 0];
$title = "Novo Ingrediente";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM ingredients WHERE id = ?");
    $stmt->execute([$id]);
    $ingredient = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$ingredient) die("Ingrediente não encontrado");
    $title = "Editar Ingrediente";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $is_deleted = isset($_POST['is_deleted']) ? 1 : 0;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE ingredients SET name = ?, is_deleted = ? WHERE id = ?");
        $stmt->execute([$name, $is_deleted, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO ingredients (name, is_deleted) VALUES (?, ?)");
        $stmt->execute([$name, $is_deleted]);
    }
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_products.php');
    } else {
        header('Location: configurador.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Salt Flow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="login-container" style="margin-top: 40px;">
        <h2><?= $title ?></h2>
        <form method="post">
            <label>Nome do Ingrediente</label>
            <input type="text" name="name" value="<?= htmlspecialchars($ingredient['name']) ?>" required>

            <div style="margin-top: 15px; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="is_deleted" id="del" <?= $ingredient['is_deleted'] ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                <label for="del" style="margin:0; color: #f87171;">Eliminado</label>
            </div>

            <button type="submit" style="margin-top: 20px;">Guardar</button>
            <a href="<?= $_SESSION['role'] === 'admin' ? 'admin_products.php' : 'configurador.php' ?>" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancelar</a>
        </form>
    </div>
    <?php include 'footer.php'; ?>