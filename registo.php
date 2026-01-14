<?php
session_start();
require 'db_connect.php';

$err = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $us = trim($_POST['username'] ?? '');
    $pw = $_POST['password'] ?? '';
    $cp = $_POST['confirm_password'] ?? '';

    // Validações básicas
    if (empty($us) || empty($pw)) {
        $err = "Por favor, preencha todos os campos obrigatórios.";
    } elseif ($pw !== $cp) {
        $err = "As passwords não coincidem.";
    } else {
        // Verificar se username já existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$us]);
        if ($stmt->fetchColumn() > 0) {
            $err = "Este nome de utilizador já está em uso.";
        } else {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            
            // Inserção
            $sql = "INSERT INTO users (username, password_hash, role, is_deleted) 
                    VALUES (?, ?, 'cliente', 0)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$us, $hash])) {
                // Redireciona com mensagem de sucesso
                header("Location: login.php?registered=1");
                exit;
            } else {
                $err = "Erro ao criar conta. Tente novamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - Salt Flow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <?php include 'header.php'; ?>
    <div class="login-container">
        <div class="brand" style="text-align:center; margin-bottom:20px;">Salt Flow</div>
        <h2>Criar Conta</h2>

        <?php if ($err): ?>
            <div style="background: #e74c3c; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                <?= htmlspecialchars($err) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="registo.php">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Para fazer login">

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirmar Password</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" style="margin-top:20px;">Registar</button>
        </form>
        
        <p style="margin-top:20px; text-align:center;">
            Já tem conta? <a href="login.php" style="color:#f06aa6;">Faça Login</a>
        </p>
    </div>
    <?php include 'footer.php'; ?>