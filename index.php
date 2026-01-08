<?php
session_start();
require 'db_connect.php';

$cats = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

function getProducts($pdo, $catId) {
    // Admin e Configurador veem produtos desativados. Outros não.
    $viewAll = (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'configurador']));
    $sql = "SELECT p.*, GROUP_CONCAT(i.id) as i_ids, GROUP_CONCAT(i.name SEPARATOR ', ') as i_names 
            FROM products p 
            LEFT JOIN product_ingredients pi ON p.id = pi.product_id
            LEFT JOIN ingredients i ON pi.ingredient_id = i.id
            WHERE p.category_id = ? " . ($viewAll ? "" : " AND p.is_active = 1 AND p.is_deleted = 0") . "
            GROUP BY p.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Salt Flow ≋ Beach Bar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="nav-bar">
            <div class="logo">
                <a href="index.php"><img src="imagens/logo.png" width="40px" alt="Logo"/></a>
            </div>

            <div class="nav-list">
                <ul>
                    <?php if(isset($_SESSION['role'])): ?>
                        <?php if(in_array($_SESSION['role'], ['staff', 'admin', 'configurador'])): ?>
                            <li><a href="staff_orders.php" class="nav-link">Staff</a></li>
                        <?php endif; ?>
                        <?php if(in_array($_SESSION['role'], ['cozinha', 'admin', 'configurador'])): ?>
                            <li><a href="cozinha_orders.php" class="nav-link">Cozinha</a></li>
                        <?php endif; ?>
                        <?php if(in_array($_SESSION['role'], ['admin', 'configurador'])): ?>
                            <li><a href="admin_products.php" class="nav-link">Produtos</a></li>
                            <li><a href="admin_users.php" class="nav-link">Utilizadores</a></li>
                        <?php endif; ?>
                        <?php if($_SESSION['role'] === 'configurador'): ?>
                            <li><a href="configurador.php" class="nav-link" style="color:#f06aa6">Config</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="header-icons">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="header-greeting">Olá, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="logout.php" class="header-logout-link" style="font-size: 14px; margin-left: 10px;">(Sair)</a>
                <?php else: ?>
                    <div class="login-button"><a href="login.php">Login</a></div>
                <?php endif; ?>
                
                <div class="cart-icon">
                    <a href="cart.php">
                        <img src="imagens/cart_icon.svg" width="25px" />
                        <span class="cart-count"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span>
                    </a>
                </div>

                <div class="mobile-menu-icon">
                    <button onclick="menuShow()"><img class="icon" src="imagens/menu_white_36dp.svg" alt="Menu"></button>
                </div>
            </div>
        </nav>

        <div class="mobile-menu">
            <ul>
                <li><a href="index.php" class="nav-link">Menu Principal</a></li>
                <?php if(isset($_SESSION['role'])): ?>
                    <?php if(in_array($_SESSION['role'], ['staff', 'admin', 'configurador'])): ?>
                        <li><a href="staff_orders.php" class="nav-link">Staff</a></li>
                    <?php endif; ?>
                    <?php if(in_array($_SESSION['role'], ['cozinha', 'admin', 'configurador'])): ?>
                        <li><a href="cozinha_orders.php" class="nav-link">Cozinha</a></li>
                    <?php endif; ?>
                    <?php if(in_array($_SESSION['role'], ['admin', 'configurador'])): ?>
                        <li><a href="admin_products.php" class="nav-link">Produtos</a></li>
                        <li><a href="admin_users.php" class="nav-link">Utilizadores</a></li>
                    <?php endif; ?>
                    <?php if($_SESSION['role'] === 'configurador'): ?>
                        <li><a href="configurador.php" class="nav-link" style="color:#f06aa6">Config</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="nav-link">Sair</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="nav-link">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <div class="menu-board">
        <div class="brand">Salt Flow ≋ Beach Bar</div>
        <?php foreach($cats as $c): ?>
            <div class="category" id="<?= $c['slug'] ?>"><?= $c['name'] ?></div>
            <?php foreach(getProducts($pdo, $c['id']) as $p): ?>
                <div class="item">
                    <div>
                        <div class="item-name"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="item-desc"><?= $p['i_names'] ?: htmlspecialchars($p['description']) ?></div>
                    </div>
                    <div class="actions">
                        <span class="price"><?= number_format($p['price'], 2) ?>€</span>
                        <button class="btn-pedir" onclick="openModal(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', '<?= $p['i_ids'] ?>', '<?= addslashes($p['i_names'] ?? '') ?>')">Pedir</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <div id="ingModal" class="modal-overlay" style="display:none;">
        <form id="cartForm" class="modal-box">
            <h3 id="mTitle" class="modal-title"></h3>
            <p class="modal-subtitle">Personalize os seus ingredientes:</p>
            <input type="hidden" name="pid" id="mPid">
            <div id="mList" class="modal-ingredients-list"></div>
            <div class="modal-btns">
                <button type="button" class="btn-cancel" onclick="document.getElementById('ingModal').style.display='none'">Cancelar</button>
                <button type="submit" class="checkout-button checkout-button--modal">Adicionar ao Carrinho</button>
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>