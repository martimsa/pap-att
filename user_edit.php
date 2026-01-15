<?php
session_start();
require 'db_connect.php';

// Apenas configurador
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'configurador') {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: configurador.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) { echo "Utilizador não encontrado."; exit; }

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    // Checkbox envia 'on' se marcado, ou nada se não marcado
    $is_deleted = isset($_POST['is_deleted']) ? 1 : 0;
    $new_pass = trim($_POST['new_password'] ?? '');

    // Prevenir auto-sabotagem (apagar a própria conta ou remover admin)
    if ($id == $_SESSION['user_id'] && ($is_deleted == 1 || $role !== 'configurador')) {
        $error = "Não pode eliminar a sua própria conta nem alterar o seu cargo de Configurador.";
    } else {
        if (!empty($new_pass)) {
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET role = ?, is_deleted = ?, password_hash = ? WHERE id = ?");
            $stmt->execute([$role, $is_deleted, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET role = ?, is_deleted = ? WHERE id = ?");
            $stmt->execute([$role, $is_deleted, $id]);
        }
        header('Location: configurador.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Utilizador - Salt Flow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="login-container" style="margin-top: 40px;">
        <h2>Editar Utilizador</h2>
        <p style="text-align:center; color:#aaa; margin-bottom:20px;">
            <?= htmlspecialchars($user['username']) ?>
        </p>

        <?php if ($error): ?>
            <div style="background: #e74c3c; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Cargo (Role)</label>
            <select name="role" style="width: 100%; padding: 12px; background: #222; color: white; border: 2px solid #333; border-radius: 8px; font-size: 16px;">
                <option value="cliente" <?= $user['role'] == 'cliente' ? 'selected' : '' ?>>Cliente</option>
                <option value="staff" <?= $user['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                <option value="cozinha" <?= $user['role'] == 'cozinha' ? 'selected' : '' ?>>Cozinha</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="configurador" <?= $user['role'] == 'configurador' ? 'selected' : '' ?>>Configurador</option>
            </select>

            <label style="margin-top: 15px; display:block;">Nova Password (opcional)</label>
            <input type="password" name="new_password" placeholder="Deixe em branco para manter">

            <div style="margin-top: 20px; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="is_deleted" id="del" <?= $user['is_deleted'] ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                <label for="del" style="margin:0; color: #f87171;">Marcar como Eliminado</label>
            </div>

            <button type="submit">Guardar Alterações</button>
            <a href="configurador.php" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancelar</a>
        </form>
    </div>
    <?php include 'footer.php'; ?>