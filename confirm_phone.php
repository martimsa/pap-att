<?php
session_start();
require 'db_connect.php';
if(!isset($_SESSION['pending_uid'])) { header('Location: registo.php'); exit; }

$code_show = $_SESSION['simulated_sms'] ?? 'ERROR';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id=? AND verification_code=?");
    $stmt->execute([$_SESSION['pending_uid'], $code]);
    if ($stmt->fetch()) {
        $pdo->prepare("UPDATE users SET is_verified=1, verification_code=NULL WHERE id=?")->execute([$_SESSION['pending_uid']]);
        unset($_SESSION['pending_uid']); unset($_SESSION['simulated_sms']);
        header('Location: login.php'); exit;
    } else $msg = "Invalid code.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><link rel="stylesheet" href="style.css"><title>Confirm Phone</title></head>
<body>
    <div class="login-container">
        <h2>SMS Verification</h2>
        <div style="border:1px dashed #f06aa6; padding:15px; margin:20px 0; color:white;">
            <strong>SMS SIMULATION:</strong> Your code is <h1><?= $code_show ?></h1>
        </div>
        <?php if($msg) echo "<p style='color:red'>$msg</p>"; ?>
        <form method="post">
            <label>Enter Code:</label><input type="text" name="code" required>
            <button type="submit">Confirm</button>
        </form>
    </div>
</body>
</html>