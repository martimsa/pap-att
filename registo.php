<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fn = trim($_POST['fullname']);
    $em = trim($_POST['email']);
    $ph = trim($_POST['phone_number']);
    $us = trim($_POST['username']);
    $pw = $_POST['password'];
    $cp = $_POST['confirm_password'];
    $errors = [];

    if ($pw !== $cp) $errors[] = "Passwords do not match.";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email=? OR username=? OR phone_number=?");
    $stmt->execute([$em, $us, $ph]);
    if ($stmt->fetchColumn() > 0) $errors[] = "Email/User/Phone number already exists.";

    if (empty($errors)) {
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $code = strval(mt_rand(100000, 999999));
        
        $pdo->prepare("INSERT INTO users (full_name, email, phone_number, username, password_hash, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)")
            ->execute([$fn, $em, $ph, $us, $hash, $code]);
            
        $_SESSION['pending_uid'] = $pdo->lastInsertId();
        $_SESSION['simulated_sms'] = $code; 
        echo json_encode(['success' => true]); exit;
    } else {
        echo json_encode(['success' => false, 'err' => implode(', ', $errors)]); exit;
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
    <title>Register</title>
</head>
<body>
    <div class="login-container">
        <h2>Register</h2>
        <form id="regForm">
            <label>Full Name:</label><input type="text" name="fullname" required>
            <label>Email:</label><input type="email" name="email" required>
            <label>Phone:</label><input type="tel" name="phone_number" required>
            <label>Username:</label><input type="text" name="username" required>
            <label>Password:</label><input type="password" name="password" required>
            <label>Confirm:</label><input type="password" name="confirm_password" required>
            <button type="submit">Register</button>
        </form>
        <p class="register-text">Already have an account? <a href="login.php">Login.</a></p>
    </div>
    <script>
        document.getElementById('regForm').addEventListener('submit', function(e){
            e.preventDefault();
            fetch('registo.php', { method:'POST', body:new FormData(this) })
            .then(r=>r.json()).then(d=>{
                if(d.success) window.location.href='confirm_phone.php';
                else alert(d.err);
            });
        });
    </script>
<?php include 'footer.php'; ?>