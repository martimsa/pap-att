<?php
session_start();
require 'db_connect.php';

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Capturar dados (o campo pode ser username ou phone_number, conforme o seu HTML)
    $login_identifier = trim($_POST['username_phonenumber'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login_identifier) || empty($password)) {
        $login_error = "Preencha o nome de utilizador/telefone e a password.";
    } else {
        // Tenta encontrar o utilizador pelo username ou phone_number
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? OR phone_number = ?");
        $stmt->execute([$login_identifier, $login_identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login bem-sucedido! Cria variáveis de sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 
            
            // Redireciona para a página principal
            header('Location: index.php');
            exit;
            
        } else {
            $login_error = "Credenciais inválidas.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Salt Flow ≋ Beach Bar</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link rel="icon" type="image/x-icon" href="imagens/logo_menu.svg" />
  </head>
  <body>
    <header>
      <nav class="nav-bar">
        <div class="logo">
          <a href="index.php">
            <img class="logo-ft" src="imagens/logo_menu.jpg" width="40px" alt="Logo"/>
          </a>
        </div>

        <div class="nav-list">
          <ul>
            <li class="nav-item">
              <a href="index.php" class="nav-link">Home</a>
            </li>
          </ul>
        </div>

        <div class="header-icons">
            <div class="mobile-menu-icon">
              <button onclick="menuShow()">
                <img class="icon" src="imagens/menu_white_36dp.svg" />
              </button>
            </div>
        </div>
      </nav>
    </header>

    <div class="login-container">
      <h2>Login</h2>
      
      <?php if ($login_error): ?>
          <p class="error-message"><?= htmlspecialchars($login_error) ?></p>
      <?php endif; ?>

      <form action="login.php" method="post">
        <label for="username_phonenumber">Username/Phone number:</label>
        <input type="text" id="username_phonenumber" name="username_phonenumber" required 
               value="<?= htmlspecialchars($login_identifier ?? '') ?>" />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <button type="submit">Login</button>
      </form>

      <p class="register-text">Don't have an account? <a href="registo.php">Register here</a>.</p>
    </div>

    <script src="script.js"></script>
  </body>
</html>