<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'configurador'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? null;
$category = ['name' => '', 'slug' => ''];
$title = "Nova Categoria";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$category) die("Categoria não encontrada");
    $title = "Editar Categoria";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = $_POST['slug'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
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
            <label>Nome da Categoria</label>
            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required placeholder="Ex: Bebidas">

            <label>Slug (ID para links, sem espaços)</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($category['slug']) ?>" required placeholder="Ex: bebidas">

            <button type="submit">Guardar</button>
            <a href="<?= $_SESSION['role'] === 'admin' ? 'admin_products.php' : 'configurador.php' ?>" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancelar</a>
        </form>
    </div>
    <?php include 'footer.php'; ?>