<?php
session_start();
require 'db_connect.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['u']);
    $pass = $_POST['p'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? OR phone_number=?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pass, $user['password_hash'])) {
        if ($user['is_verified'] == 0) {
            $err = "Account not verified. Check your phone.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php'); exit;
        }
    } else {
        $err = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if($err) echo "<p style='color:red'>$err</p>"; ?>
        <form method="post">
            <label>Username / Phone:</label><input type="text" name="u" required>
            <label>Password:</label><input type="password" name="p" required>
            <button type="submit">Login</button>
        </form>
        <p class="register-text">Without an account? <a href="registo.php">Register</a>.</p>
        <p class="register-text"><a href="index.php">Back to Menu</a></p>
    </div>
<?php include 'footer.php'; ?>