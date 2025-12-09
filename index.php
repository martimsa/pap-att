<?php
session_start();
require 'db_connect.php';

$cats = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

function getProducts($pdo, $catId) {
    $statusCheck = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? "" : " AND p.is_active = 1";
    
    $sql = "SELECT p.*, GROUP_CONCAT(i.id) as i_ids, GROUP_CONCAT(i.name SEPARATOR ', ') as i_names 
            FROM products p 
            LEFT JOIN product_ingredients pi ON p.id = pi.product_id
            LEFT JOIN ingredients i ON pi.ingredient_id = i.id
            WHERE p.category_id = ? $statusCheck
            GROUP BY p.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes" />
    <meta name="theme-color" content="#1b1b1b" />
    <title>Salt Flow ≋ Beach Bar</title>
    <link rel="icon" type="jpg" href="imagens/logo.png" />
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="nav-bar">
            <div class="logo"><a href="index.php"><img class="logo-ft" src="imagens/logo.png" width="40px" alt="Logo"/></a></div>
            <div class="nav-list">
                <ul>
                    <?php foreach($cats as $c): ?><li class="nav-item"><a href="#<?= $c['slug'] ?>" class="nav-link"><?= $c['name'] ?></a></li><?php endforeach; ?>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?><li class="nav-item"><a href="admin_products.php" class="nav-link header-role-link--admin">Admin</a></li><?php endif; ?>
                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] === 'staff' || $_SESSION['role'] === 'admin')): ?><li class="nav-item"><a href="staff_orders.php" class="nav-link header-role-link--staff">Staff</a></li><?php endif; ?>
                </ul>
            </div>
            <div class="header-icons">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="header-greeting">Oi, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="logout.php" class="header-logout-link">(Sair)</a>
                    <div class="cart-icon">
                        <a href="cart.php">
                            <img src="imagens/cart_icon.svg" class="cart-icon-img" />
                            <span class="cart-count"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="login-button"><a href="login.php"><img src="imagens/user_icon.svg" class="user-icon-img" /></a></div>
                <?php endif; ?>
                <div class="mobile-menu-icon"><button onclick="menuShow()"><img src="imagens/menu_white_36dp.svg" class="icon" /></button></div>
            </div>
        </nav>
    </header>

    <div class="mobile-menu-overlay" onclick="menuShow()"></div>
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <button class="mobile-menu-close" onclick="menuShow()" aria-label="Fechar menu">
                <img src="imagens/close_white_36dp.svg" alt="Fechar" />
            </button>
        </div>
        <ul>
            <li class="nav-item"><a href="index.php" class="nav-link" onclick="menuShow()">Home</a></li>
            
            <?php foreach($cats as $c): ?>
                <li class="nav-item"><a href="#<?= $c['slug'] ?>" class="nav-link" onclick="menuShow()"><?= $c['name'] ?></a></li>
            <?php endforeach; ?>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <li class="nav-item mobile-menu-separator"></li>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a href="admin_products.php" class="nav-link" style="color:red !important;">Painel Admin</a></li>
                <?php endif; ?>
                <?php if($_SESSION['role'] === 'staff' || $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a href="staff_orders.php" class="nav-link" style="color:cyan !important;">Pedidos Staff</a></li>
                <?php endif; ?>
                <li class="nav-item"><a href="cart.php" class="nav-link">🛒 Carrinho (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">Sair (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
            <?php else: ?>
                <li class="nav-item mobile-menu-separator"><a href="login.php" class="nav-link">Login / Registo</a></li>
            <?php endif; ?>
        </ul>
    </div>
    
    <div class="menu-board">
        <div class="brand">Salt Flow ≋ Beach Bar</div>
        <?php foreach($cats as $c): ?>
            <div class="category" id="<?= $c['slug'] ?>"><?= $c['name'] ?></div>
            <?php foreach(getProducts($pdo, $c['id']) as $p): ?>
                <div class="item <?= $p['is_active'] ? '' : 'item--inactive' ?>">
                    <div>
                        <div class="item-name">
                            <?= htmlspecialchars($p['name']) ?>
                            <?php if(!$p['is_active']) echo '<span class="item-offline-label">OFFLINE</span>'; ?>
                        </div>
                        <div class="item-desc"><?= $p['i_names'] ? htmlspecialchars($p['i_names']) : htmlspecialchars($p['description']) ?></div>
                    </div>
                    <div class="actions">
                        <span class="price"><?= number_format($p['price'], 2) ?>€</span>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'cliente'): ?>
                            <button class="btn-pedir" onclick="openModal(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', '<?= $p['i_ids'] ?>', '<?= addslashes($p['i_names'] ?? '') ?>')">Pedir</button>
                        <?php elseif(isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff')): ?>
                             <button class="btn-pedir btn-pedir--staff" disabled>Staff</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <div id="ingModal" class="modal-overlay">
        <form id="cartForm" class="modal-box">
            <h3 id="mTitle" class="modal-title"></h3>
            <p class="modal-subtitle">Desmarque ingredientes para remover:</p>
            <input type="hidden" name="pid" id="mPid">
            <div id="mList" class="modal-ingredients-list"></div>
            <div class="modal-btns">
                <button type="button" class="btn-cancel" onclick="document.getElementById('ingModal').style.display='none'">Cancelar</button>
                <button type="submit" class="checkout-button checkout-button--modal">Adicionar ao Carrinho</button>
            </div>
        </form>
    </div>

<?php include 'footer.php'; ?>