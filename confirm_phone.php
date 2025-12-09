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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Phone</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
</head>
<body>
    <div class="login-container">
        <h2>SMS Verification</h2>
        <div style="border:1px dashed #f06aa6; padding:15px; margin:20px 0; color:white; text-align:center;">
            <strong>SMS SIMULATION:</strong>
            <div style="font-family: 'Permanent Marker', cursive; font-size: 42px; margin-top: 5px; color: #f06aa6;"><?= htmlspecialchars($code_show) ?></div>
        </div>
        <?php if($msg) echo "<p style='color:#f87171; text-align:center; margin-bottom: 15px;'>" . htmlspecialchars($msg) . "</p>"; ?>
        <form method="post" action="confirm_phone.php">
            <label for="code">Enter the Code:</label>
            <input type="text" id="code" name="code" required autofocus inputmode="numeric" pattern="[0-9]*">
            <button type="submit">Confirm</button>
        </form>
    </div>
</body>
</html>