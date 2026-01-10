<?php
session_start();
require 'db_connect.php';

// Verifica permissão (Admin ou Configurador)
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header('Location: index.php'); 
    exit; 
}

// Lógica para atualizar o cargo
if(isset($_POST['update_role'])) {
    $uid = $_POST['uid'];
    $new_role = $_POST['role'];
    
    // Validação: Apenas permite atribuir cliente, staff ou cozinha
    if(in_array($new_role, ['cliente', 'staff', 'cozinha'])) {
        $stmt = $pdo->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->execute([$new_role, $uid]);
        $msg = "Cargo atualizado com sucesso!";
    }
}

// Busca utilizadores ativos
$users = $pdo->query("SELECT * FROM users WHERE is_deleted = 0 ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Gestão de Equipa - Salt Flow</title>
    <style>
        .role-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            background: #333;
        }
        .status-active { color: #4ade80; font-weight: bold; }
        .status-inactive { color: #f87171; font-weight: bold; }
        .action-btn {
            background: #f06aa6; 
            color: white; 
            border: none; 
            padding: 6px 12px; 
            cursor: pointer; 
            border-radius: 4px;
            font-family: 'Amatic SC', cursive;
            font-weight: bold;
            font-size: 18px;
        }
        .action-btn:hover { background: #d44e8a; }
        select {
            padding: 6px;
            border-radius: 4px;
            background: #333;
            color: white;
            border: 1px solid #555;
        }
    </style>
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="admin-container">
        <h2>Gerir Utilizadores</h2>

        <?php if(isset($msg)): ?>
            <p style="background: #2ecc71; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;"><?= $msg ?></p>
        <?php endif; ?>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Username</th>
                        <th>Função</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td data-label="ID"><?= $u['id'] ?></td>
                        <td data-label="Nome"><?= htmlspecialchars($u['full_name']) ?></td>
                        <td data-label="Username"><?= htmlspecialchars($u['username']) ?></td>
                        <td data-label="Função">
                            <span class="role-badge" style="color: <?= match($u['role']) { 'admin' => '#f06aa6', 'configurador' => '#a855f7', 'staff' => '#22d3ee', 'cozinha' => '#fbbf24', default => '#9ca3af' } ?>;">
                                <?= $u['role'] ?>
                            </span>
                        </td>
                        <td data-label="Estado"><?= $u['is_deleted'] ? '<span class="status-inactive">Eliminado</span>' : '<span class="status-active">Ativo</span>' ?></td>
                        <td data-label="Ações">
                            <?php if(!in_array($u['role'], ['admin', 'configurador'])): ?>
                                <form method="post" style="display:flex; gap: 5px; align-items: center;">
                                    <input type="hidden" name="uid" value="<?= $u['id'] ?>">
                                    <select name="role">
                                        <option value="cliente" <?= $u['role']=='cliente'?'selected':'' ?>>Cliente</option>
                                        <option value="staff" <?= $u['role']=='staff'?'selected':'' ?>>Staff</option>
                                        <option value="cozinha" <?= $u['role']=='cozinha'?'selected':'' ?>>Cozinha</option>
                                    </select>
                                    <button type="submit" name="update_role" class="action-btn">Salvar</button>
                                </form>
                            <?php else: ?>
                                <span style="color:#555; font-size:12px;">Sem permissão</span>
                            <?php endif; ?>
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