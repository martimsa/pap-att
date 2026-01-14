<?php
// 1. Ativar exibição de erros para diagnóstico (podes remover isto quando estiver em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db_connect.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização básica
    $login = isset($_POST['u']) ? trim($_POST['u']) : '';
    $pass = isset($_POST['p']) ? $_POST['p'] : '';
    
    if (empty($login) || empty($pass)) {
        $err = "Por favor, preencha todos os campos.";
    } else {
        try {
            // Verifica se o utilizador existe e não foi apagado
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_deleted = 0");
            $stmt->execute([$login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($pass, $user['password_hash'])) {
                // Login bem-sucedido: Regenerar ID da sessão por segurança
                session_regenerate_id();
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                header('Location: index.php'); 
                exit;
            } else {
                $err = "Utilizador ou password incorretos.";
            }
        } catch (PDOException $e) {
            $err = "Erro na base de dados: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Salt Flow</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <?php include 'header.php'; ?>
    <div class="login-container">
        <div class="brand" style="text-align:center; margin-bottom:20px;">Salt Flow</div>
        <h2>Entrar</h2>
        
        <?php if ($err): ?>
            <div style="background: #e74c3c; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                <?= htmlspecialchars($err) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label>Utilizador</label>
            <input type="text" name="u" required placeholder="O seu username" autofocus>
            
            <label>Password</label>
            <input type="password" name="p" required placeholder="A sua password">
            
            <button type="submit" style="margin-top:20px;">Entrar</button>
        </form>
        
        <p style="margin-top:20px; text-align:center;">
            Ainda não tem conta? <a href="registo.php" style="color:#f06aa6;">Registe-se aqui</a>
        </p>
    </div>
    <?php include 'footer.php'; ?>