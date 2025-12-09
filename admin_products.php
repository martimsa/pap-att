<?php
session_start();
require 'db_connect.php';
if($_SESSION['role'] !== 'admin') { header('Location: index.php'); exit; }

if(isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE products SET is_active = NOT is_active WHERE id=?")->execute([$_GET['toggle']]);
    header('Location: admin_products.php');
}
$prods = $pdo->query("SELECT p.*, c.name as cname FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Produtos</title>
    <style>
        /* CSS Específico para adaptar texto à caixa nesta página */
        .admin-container table {
            width: 100%;
            table-layout: fixed; /* Força as colunas a respeitarem a largura disponível */
            border-collapse: collapse;
        }

        .admin-container th, 
        .admin-container td {
            /* O texto adapta-se: Mínimo 11px, Ideal 2vw (2% da largura do ecrã), Máximo 16px */
            font-size: clamp(11px, 2vw, 16px);
            
            /* Garante que o texto quebra de linha se não couber */
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            padding: 10px 5px; /* Padding reduzido nas laterais para mobile */
            vertical-align: middle;
        }

        /* Definição de larguras para colunas críticas para não ficarem esmagadas */
        .admin-container th:nth-child(1), .admin-container td:nth-child(1) { width: 10%; max-width: 40px; } /* ID */
        .admin-container th:last-child, .admin-container td:last-child { width: 25%; } /* Botão Ação */
        
        /* Ajuste do botão para acompanhar o tamanho do texto */
        .action-link.action-btn {
            font-size: clamp(10px, 1.8vw, 14px);
            padding: 6px 10px;
            white-space: nowrap; /* O texto do botão não deve quebrar */
        }
    </style>
</head>
<body>
    <div class="mobile-menu-overlay" onclick="menuShow()"></div>

    <header>
        <nav class="nav-bar" style="justify-content:flex-end; padding: 1rem 3rem;">
            <a href="index.php" style="color:#f06aa6; font-size:18px; margin-right:20px;" class="desktop-only">Menu Principal</a>
            
            <div class="mobile-menu-icon">
                <button onclick="menuShow()">
                    <img class="icon" src="imagens/menu_white_36dp.svg" alt="Menu" />
                </button>
            </div>
        </nav>

        <div class="mobile-menu">
            <div class="mobile-menu-header">
                <button class="mobile-menu-close" onclick="menuShow()">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <ul>
                <li class="nav-item"><a href="index.php" class="nav-link">Menu Principal</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">Sair</a></li>
            </ul>
        </div>
    </header>
    
    <div class="admin-container">
        <h2>Gerir Produtos (Admin)</h2>
        <div class="table-responsive-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Categoria</th>
                        <th>Nome</th>
                        <th>Estado</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($prods as $p): ?>
                    <tr>
                        <td data-label="ID"><?= $p['id'] ?></td>
                        <td data-label="Categoria"><?= htmlspecialchars($p['cname']) ?></td>
                        <td data-label="Nome"><?= htmlspecialchars($p['name']) ?></td>
                        <td data-label="Estado">
                            <span class="status-tag <?= $p['is_active']?'status-online':'status-offline' ?>">
                                <?= $p['is_active']?'ONLINE':'OFFLINE' ?>
                            </span>
                        </td>
                        <td data-label="Ação">
                            <a href="?toggle=<?= $p['id'] ?>" class="action-link action-btn">Trocar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function menuShow() {
            let menuMobile = document.querySelector('.mobile-menu');
            let overlay = document.querySelector('.mobile-menu-overlay');
            
            if (menuMobile.classList.contains('open')) {
                menuMobile.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            } else {
                menuMobile.classList.add('open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
    </script>
</body>
</html>