<?php
session_start();
require 'db_connect.php';

// Verificação de permissão: Apenas 'configurador' pode aceder
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'configurador') {
    header('Location: index.php');
    exit;
}

// Buscar dados das tabelas
// 1. Utilizadores
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();

// 2. Categorias
$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

// 3. Produtos (com join para obter nome da categoria)
$products = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
")->fetchAll();

// 4. Ingredientes
$ingredients = $pdo->query("SELECT * FROM ingredients ORDER BY id DESC")->fetchAll();

// 5. Logs
$logs = [];
try {
    $logs = $pdo->query("
        SELECT l.*, u.username 
        FROM logs l 
        LEFT JOIN users u ON l.user_id = u.id 
        ORDER BY l.created_at DESC 
        LIMIT 50
    ")->fetchAll();
} catch (Exception $e) {
    // Tabela logs pode não existir
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração - Salt Flow</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .config-section { margin-bottom: 50px; }
        .section-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .section-title { 
            font-family: 'Amatic SC', cursive; 
            font-size: 32px; 
            color: #f06aa6; 
            margin: 0;
        }
        .btn-add {
            background: #f06aa6;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-add:hover { background: #d44e8a; }
        .status-active { color: #4ade80; font-weight: bold; }
        .status-inactive { color: #f87171; font-weight: bold; }
        .role-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            background: #333;
        }
    </style>
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="admin-container">
        <h2 style="margin-bottom: 40px;">Painel de Configuração</h2>

        <!-- 1. Utilizadores -->
        <div class="config-section">
            <div class="section-header">
                <h3 class="section-title">Utilizadores</h3>
                <!-- <a href="user_edit.php" class="btn-add">+ Novo</a> -->
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['full_name']) ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td>
                                <span class="role-badge" style="color: <?= match($u['role']) { 'admin' => '#f06aa6', 'configurador' => '#a855f7', 'staff' => '#22d3ee', 'cozinha' => '#fbbf24', default => '#9ca3af' } ?>;">
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td><?= $u['is_deleted'] ? '<span class="status-inactive">Eliminado</span>' : '<span class="status-active">Ativo</span>' ?></td>
                            <td><a href="user_edit.php?id=<?= $u['id'] ?>" class="action-link">Editar</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Categorias -->
        <div class="config-section">
            <div class="section-header">
                <h3 class="section-title">Categorias</h3>
                <a href="category_form.php" class="btn-add">+ Nova Categoria</a>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Slug</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= htmlspecialchars($c['slug']) ?></td>
                            <td><a href="category_form.php?id=<?= $c['id'] ?>" class="action-link">Editar</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Produtos -->
        <div class="config-section">
            <div class="section-header">
                <h3 class="section-title">Produtos</h3>
                <a href="product_form.php" class="btn-add">+ Novo Produto</a>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Visibilidade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['category_name'] ?? 'N/A') ?></td>
                            <td><?= number_format($p['price'], 2) ?> €</td>
                            <td>
                                <?php if($p['is_deleted']): ?>
                                    <span class="status-inactive">Eliminado</span>
                                <?php elseif($p['is_active']): ?>
                                    <span class="status-active">Visível</span>
                                <?php else: ?>
                                    <span style="color:orange; font-weight:bold;">Oculto</span>
                                <?php endif; ?>
                            </td>
                            <td><a href="product_form.php?id=<?= $p['id'] ?>" class="action-link">Editar</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Ingredientes -->
        <div class="config-section">
            <div class="section-header">
                <h3 class="section-title">Ingredientes</h3>
                <a href="ingredient_form.php" class="btn-add">+ Novo Ingrediente</a>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ingredients as $i): ?>
                        <tr>
                            <td><?= $i['id'] ?></td>
                            <td><?= htmlspecialchars($i['name']) ?></td>
                            <td><?= $i['is_deleted'] ? '<span class="status-inactive">Eliminado</span>' : '<span class="status-active">Ativo</span>' ?></td>
                            <td><a href="ingredient_form.php?id=<?= $i['id'] ?>" class="action-link">Editar</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Logs -->
        <div class="config-section">
            <div class="section-header">
                <h3 class="section-title">Logs do Sistema</h3>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Utilizador</th>
                            <th>Ação</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logs)): ?>
                            <tr><td colspan="4" style="text-align:center; color:#aaa;">Sem registos recentes.</td></tr>
                        <?php else: ?>
                            <?php foreach($logs as $l): ?>
                            <tr>
                                <td style="white-space:nowrap; font-size:14px;"><?= $l['created_at'] ?></td>
                                <td><?= htmlspecialchars($l['username'] ?? 'Sistema') ?></td>
                                <td><?= htmlspecialchars($l['action']) ?></td>
                                <td style="font-size:14px; color:#ccc;"><?= htmlspecialchars($l['details']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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