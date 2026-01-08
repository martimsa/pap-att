<?php
session_start();
require 'db_connect.php';

// Verifica permissão (Admin ou Configurador)
if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'configurador'])) { 
    header('Location: index.php'); 
    exit; 
}

// Lógica para atualizar o cargo
if(isset($_POST['update_role'])) {
    $stmt = $pdo->prepare("UPDATE users SET role=? WHERE id=?");
    $stmt->execute([$_POST['role'], $_POST['uid']]);
    // Feedback visual opcional
    $msg = "Cargo atualizado com sucesso!";
}

// Busca todos os utilizadores que não foram "apagados"
$users = $pdo->query("SELECT * FROM users WHERE is_deleted = 0 ORDER BY role DESC, username ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Gestão de Equipa - Salt Flow</title>
</head>
<body>
    <header>
        <nav class="nav-bar">
            <div class="logo">
                <a href="index.php"><img src="imagens/logo.png" width="40px" alt="Logo"/></a>
            </div>

            <div class="nav-list">
                <ul>
                    <li><a href="index.php" class="nav-link">Menu Principal</a></li>
                    <li><a href="staff_orders.php" class="nav-link">Staff</a></li>
                    <li><a href="cozinha_orders.php" class="nav-link">Cozinha</a></li>
                    <li><a href="admin_products.php" class="nav-link">Produtos</a></li>
                    <li><a href="admin_users.php" class="nav-link">Utilizadores</a></li>
                    <?php if($_SESSION['role'] === 'configurador'): ?>
                        <li><a href="configurador.php" class="nav-link" style="color:#f06aa6">Config</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="header-icons">
                <span class="header-greeting">Olá, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php" class="header-logout-link" style="font-size: 14px; margin-left: 10px;">(Sair)</a>
                
                <div class="mobile-menu-icon">
                    <button onclick="menuShow()"><img class="icon" src="imagens/menu_white_36dp.svg" alt="Menu"></button>
                </div>
            </div>
        </nav>

        <div class="mobile-menu">
            <ul>
                <li><a href="index.php" class="nav-link">Menu Principal</a></li>
                <li><a href="staff_orders.php" class="nav-link">Staff</a></li>
                <li><a href="cozinha_orders.php" class="nav-link">Cozinha</a></li>
                <li><a href="admin_products.php" class="nav-link">Produtos</a></li>
                <li><a href="admin_users.php" class="nav-link">Utilizadores</a></li>
                <?php if($_SESSION['role'] === 'configurador'): ?>
                    <li><a href="configurador.php" class="nav-link" style="color:#f06aa6">Config</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="nav-link">Sair</a></li>
            </ul>
        </div>
    </header>

    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Gerir Utilizadores e Cargos</h2>
        </div>

        <?php if(isset($msg)): ?>
            <p style="background: #2ecc71; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;"><?= $msg ?></p>
        <?php endif; ?>

        <div style="overflow-x: auto;">
            <table style="width:100%; text-align:left; border-collapse:collapse; background: #1b1b1b; border-radius: 10px;">
                <thead>
                    <tr style="border-bottom: 2px solid #f06aa6; background: #252525;">
                        <th style="padding: 15px;">Utilizador</th>
                        <th>Email</th>
                        <th>Cargo Atual</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr style="border-bottom: 1px solid #333;">
                        <td style="padding:15px;">
                            <strong><?= htmlspecialchars($u['username']) ?></strong><br>
                            <small style="color: #aaa;"><?= htmlspecialchars($u['full_name']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="status-tag" style="background: #444; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                <?= strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="post" style="display:flex; gap: 5px; align-items: center;">
                                <input type="hidden" name="uid" value="<?= $u['id'] ?>">
                                <select name="role" style="color:black; padding: 5px; border-radius: 4px;">
                                    <option value="cliente" <?= $u['role']=='cliente'?'selected':'' ?>>Cliente</option>
                                    <option value="staff" <?= $u['role']=='staff'?'selected':'' ?>>Staff</option>
                                    <option value="cozinha" <?= $u['role']=='cozinha'?'selected':'' ?>>Cozinha</option>
                                    <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                                </select>
                                <button type="submit" name="update_role" class="action-btn" style="background: #f06aa6; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px;">Atualizar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">Salt Flow Bar</div>
            <div class="footer-links">
                <span>© <?= date('Y') ?> Salt Flow Beach Bar</span>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>