<?php
session_start();
require 'db_connect.php';

// CORREÇÃO: Permitir apenas admin
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header('Location: index.php'); 
    exit; 
}

// Buscar categorias
$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

// Buscar ingredientes
$ingredients = $pdo->query("SELECT * FROM ingredients ORDER BY id DESC")->fetchAll();

// Buscar produtos (igual ao configurador)
$prods = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Gestão de Produtos</title>
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
        @media (max-width: 600px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .btn-add { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>
    <?php require 'header.php'; ?>

    <div class="admin-container">
        
        <!-- 1. Categorias -->
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
                            <td data-label="ID"><?= $c['id'] ?></td>
                            <td data-label="Nome"><?= htmlspecialchars($c['name']) ?></td>
                            <td data-label="Slug"><?= htmlspecialchars($c['slug']) ?></td>
                            <td data-label="Ações"><a href="category_form.php?id=<?= $c['id'] ?>" class="action-link">Editar</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Ingredientes -->
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
                            <td data-label="ID"><?= $i['id'] ?></td>
                            <td data-label="Nome"><?= htmlspecialchars($i['name']) ?></td>
                            <td data-label="Estado"><?= $i['is_deleted'] ? '<span class="status-inactive">Eliminado</span>' : '<span class="status-active">Ativo</span>' ?></td>
                            <td data-label="Ações"><a href="ingredient_form.php?id=<?= $i['id'] ?>" class="action-link">Editar</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Produtos -->
        <div class="config-section">
        <div class="section-header">
            <h3 class="section-title">Gestão de Produtos</h3>
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
                <?php foreach($prods as $p): ?>
                <tr>
                    <td data-label="ID"><?= $p['id'] ?></td>
                    <td data-label="Nome"><?= htmlspecialchars($p['name']) ?></td>
                    <td data-label="Categoria"><?= htmlspecialchars($p['category_name'] ?? 'N/A') ?></td>
                    <td data-label="Preço"><?= number_format($p['price'], 2) ?> €</td>
                    <td data-label="Visibilidade">
                        <?php if($p['is_deleted']): ?>
                            <span class="status-inactive">Eliminado</span>
                        <?php elseif($p['is_active']): ?>
                            <span class="status-active">Visível</span>
                        <?php else: ?>
                            <span style="color:orange; font-weight:bold;">Oculto</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Ações"><a href="product_form.php?id=<?= $p['id'] ?>" class="action-link">Editar</a></td>
                </tr>
                <?php endforeach; ?>
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