<?php
// Inicia a sessão para futura navegação
session_start();
require 'db_connect.php'; // Inclui a conexão à base de dados

// Verifica se o formulário foi submetido (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Capturar e Sanitizar Dados
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // 2. Validação Básica
    if ($password !== $confirm_password) {
        $errors[] = "As passwords não coincidem.";
    }
    if (strlen($password) < 6) {
        $errors[] = "A password deve ter pelo menos 6 caracteres.";
    }
    if (empty($fullname) || empty($email) || empty($username) || empty($password)) {
        $errors[] = "Todos os campos obrigatórios devem ser preenchidos.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Formato de email inválido.";
    }

    // 3. Verificação de Duplicidade (Email e Username)
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "O email ou nome de utilizador já está registado.";
        }
    }

    // 4. Inserção se não houver erros
    if (empty($errors)) {
        // Cifra a password antes de armazenar
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone_number, username, password_hash, role) VALUES (?, ?, ?, ?, ?, 'cliente')");
            $stmt->execute([$fullname, $email, $phone_number, $username, $password_hash]);

            // Registo bem-sucedido! O JavaScript tratará da animação e redirecionamento.
            // Para efeitos de código, vamos dar uma resposta de sucesso ao JS.
            echo json_encode(['success' => true]);
            exit;
            
        } catch (PDOException $e) {
            // Em caso de erro de DB (raro, mas possível), enviar erro genérico
            $errors[] = "Erro ao registar. Tente novamente mais tarde.";
        }
    }
    
    // Se houver erros, envia-os de volta ao JavaScript para exibição (usamos JSON)
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Salt Flow ≋ Beach Bar</title>
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="icon" type="image/x-icon" href="imagens/logo_menu.svg" />
  </head>
  <body>
    <header>
      <nav class="nav-bar">
        <div class="logo">
          <a href="index.php">
            <img class="logo-ft" src="imagens/logo_menu.jpg" width="40px" alt="Logo" />
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
      <h2>Register</h2>
      
      <div id="phpErrors" style="color: #ff7f3f; margin-bottom: 15px; text-align: left; font-weight: bold;">
          </div>

      <form id="registerForm" action="registo.php" method="post" novalidate>
        <label for="fullname">Full name:</label>
        <input type="text" id="fullname" name="fullname" required />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />

        <label for="phone_number">Phone number:</label>
        <input type="tel" id="phone_number" name="phone_number" />

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <label for="confirm_password">Confirm password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required />

        <button type="submit">Register</button>
      </form>

      <p class="register-text">Already have an account? <a href="login.php">Login here</a>.</p>
    </div>

    <div id="confirmOverlay" class="confirm-overlay" aria-hidden="true">
      <div class="confirm-card" role="status" aria-live="polite">
        <svg class="checkmark" viewBox="0 0 52 52" aria-hidden="true">
            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
            <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
        </svg>
        <p class="confirm-message">Registration Successful!</p>
      </div>
    </div>
    <script src="script.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const registerForm = document.getElementById('registerForm');
            const overlay = document.getElementById('confirmOverlay');
            const phpErrorsDiv = document.getElementById('phpErrors');

            if (registerForm && overlay) {
                // A sua lógica original de verificação de password é mantida, mas agora usa fetch()
                registerForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const pwd = registerForm.querySelector('#password')?.value || '';
                    const pwd2 = registerForm.querySelector('#confirm_password')?.value || '';

                    if (pwd !== pwd2) {
                        alert('The passwords do not match.');
                        return;
                    }

                    // Enviar dados para o PHP via AJAX
                    fetch('registo.php', {
                        method: 'POST',
                        body: new FormData(registerForm)
                    })
                    .then(response => response.json())
                    .then(data => {
                        phpErrorsDiv.innerHTML = ''; // Limpa erros antigos

                        if (data.success) {
                            // Sucesso: Ativar animação e redirecionar
                            const submitBtn = registerForm.querySelector('button[type="submit"]');
                            if (submitBtn) submitBtn.disabled = true;

                            overlay.classList.add('show');
                            overlay.setAttribute('aria-hidden', 'false');

                            setTimeout(function () {
                                window.location.href = 'index.php'; // Redireciona
                            }, 2000); 

                        } else {
                            // Erro: Mostrar mensagens de erro do PHP
                            if (data.errors) {
                                phpErrorsDiv.innerHTML = '<ul>' + data.errors.map(err => `<li>${err}</li>`).join('') + '</ul>';
                            } else {
                                alert('An unknown error occurred during registration.');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('An unexpected error occurred. Check the console for details.');
                    });
                });
            }
        });
    </script>
  </body>
</html>