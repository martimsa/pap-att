<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'configurador') {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? null;
$product = [
    'name' => '', 'description' => '', 'price' => '', 'category_id' => '', 
    'is_active' => 1, 'is_deleted' => 0
];
$product_ings = [];
$title = "Novo Produto";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$product) die("Produto não encontrado");
    $title = "Editar Produto";

    // Buscar ingredientes associados
    $stmt = $pdo->prepare("SELECT ingredient_id FROM product_ingredients WHERE product_id = ?");
    $stmt->execute([$id]);
    $product_ings = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$all_ingredients = $pdo->query("SELECT * FROM ingredients WHERE is_deleted = 0 ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat_id = $_POST['category_id'];
    $is_deleted = isset($_POST['is_deleted']) ? 1 : 0;

    // Garante que se estiver eliminado, não pode estar ativo (visível)
    $is_active = ($is_deleted) ? 0 : (isset($_POST['is_active']) ? 1 : 0);

    $selected_ings = $_POST['ingredients'] ?? [];

    if ($id) {
        $sql = "UPDATE products SET name=?, description=?, price=?, category_id=?, is_active=?, is_deleted=? WHERE id=?";
        $pdo->prepare($sql)->execute([$name, $desc, $price, $cat_id, $is_active, $is_deleted, $id]);
        $pid = $id;
    } else {
        $sql = "INSERT INTO products (name, description, price, category_id, is_active, is_deleted) VALUES (?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$name, $desc, $price, $cat_id, $is_active, $is_deleted]);
        $pid = $pdo->lastInsertId();
    }

    // Atualizar ingredientes (Remove todos e insere os selecionados)
    $pdo->prepare("DELETE FROM product_ingredients WHERE product_id = ?")->execute([$pid]);
    if (!empty($selected_ings)) {
        $insert = $pdo->prepare("INSERT INTO product_ingredients (product_id, ingredient_id) VALUES (?, ?)");
        foreach ($selected_ings as $iid) {
            $insert->execute([$pid, $iid]);
        }
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

    <div class="login-container" style="margin-top: 40px; max-width: 600px;">
        <h2><?= $title ?></h2>
        <form method="post">
            <label>Nome do Produto</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label>Descrição</label>
            <input type="text" name="description" value="<?= htmlspecialchars($product['description']) ?>">

            <div style="display:flex; gap:15px;">
                <div style="flex:1;">
                    <label>Preço (€)</label>
                    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                </div>
                <div style="flex:1;">
                    <label>Categoria</label>
                    <select name="category_id" style="width: 100%; padding: 12px; background: #222; color: white; border: 2px solid #333; border-radius: 8px; font-size: 16px; height: 52px; margin-top: 8px;">
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <label style="margin-top: 15px;">Ingredientes (Personalizáveis)</label>
            <div style="max-height: 150px; overflow-y: auto; background: #222; padding: 10px; border-radius: 8px; border: 2px solid #333;">
                <?php foreach($all_ingredients as $ing): ?>
                    <div style="margin-bottom: 5px;">
                        <label style="display:inline-flex; align-items:center; gap:8px; font-size:14px; margin:0;">
                            <input type="checkbox" name="ingredients[]" value="<?= $ing['id'] ?>" <?= in_array($ing['id'], $product_ings) ? 'checked' : '' ?> style="width:16px; height:16px;">
                            <?= htmlspecialchars($ing['name']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 20px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_active" id="isActive" <?= $product['is_active'] ? 'checked' : '' ?> style="width:20px; height:20px;" onchange="if(this.checked) document.getElementById('isDeleted').checked = false;">
                    Visível no Menu
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; color:#f87171;">
                    <input type="checkbox" name="is_deleted" id="isDeleted" <?= $product['is_deleted'] ? 'checked' : '' ?> style="width:20px; height:20px;" onchange="if(this.checked) document.getElementById('isActive').checked = false;">
                    Eliminado
                </label>
            </div>

            <button type="submit" style="margin-top: 20px;">Guardar Produto</button>
            <a href="<?= $_SESSION['role'] === 'admin' ? 'admin_products.php' : 'configurador.php' ?>" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancelar</a>
        </form>
    </div>
</body>
</html>