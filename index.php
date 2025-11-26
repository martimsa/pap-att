<?php
session_start();
require 'db_connect.php';

$cats = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

function getProducts($pdo, $catId) {
    // Se for Admin, vê tudo. Se for Cliente/Visitante, só vê is_active = 1
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
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Salt Flow ≋ Beach Bar</title>
    <link rel="icon" type="image/x-icon" href="imagens/logo_menu.svg" />
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
    <style>
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 2000; justify-content: center; align-items: center; }
        .modal-box { background: #1b1b1b; padding: 25px; border: 2px solid #f06aa6; max-width: 400px; width: 90%; border-radius: 12px; color:white; }
        .ing-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #333; }
        .ing-row input { width: 20px; height: 20px; accent-color: #f06aa6; }
        .modal-btns { margin-top: 20px; display: flex; gap: 10px; justify-content: center; }
        .btn-cancel { background: #444; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-family: 'Amatic SC'; font-size: 20px; cursor: pointer; }
    </style>
</head>
<body>
    <header>
        <nav class="nav-bar">
            <div class="logo"><a href="index.php"><img class="logo-ft" src="imagens/logo_menu.jpg" width="40px" alt="Logo"/></a></div>
            <div class="nav-list">
                <ul><?php foreach($cats as $c): ?><li class="nav-item"><a href="#<?= $c['slug'] ?>" class="nav-link"><?= $c['name'] ?></a></li><?php endforeach; ?></ul>
            </div>
            <div class="header-icons">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span style="color:#f06aa6; font-family:'Amatic SC'; font-size:18px;">Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <?php if($_SESSION['role'] === 'admin'): ?><a href="admin_products.php" style="color:red; font-weight:bold;">[ADMIN]</a><?php endif; ?>
                    <?php if($_SESSION['role'] === 'staff'): ?><a href="staff_orders.php" style="color:cyan; font-weight:bold;">[STAFF]</a><?php endif; ?>
                    <a href="logout.php" style="font-size:14px; color:#aaa;">Logout</a>
                    <div class="cart-icon"><a href="cart.php"><img src="imagens/cart_icon.svg" class="cart-icon-img" /><span style="color:#f06aa6; font-weight:bold; margin-left:-5px;"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span></a></div>
                <?php else: ?>
                    <div class="login-button"><a href="login.php"><img src="imagens/user_icon.svg" class="user-icon-img" /></a></div>
                <?php endif; ?>
                <div class="mobile-menu-icon"><button onclick="menuShow()"><img src="imagens/menu_white_36dp.svg" class="icon" /></button></div>
            </div>
        </nav>
        <div class="mobile-menu"><ul><li class="nav-item"><a href="index.php" class="nav-link">Home</a></li></ul></div>
    </header>

    <div class="menu-board">
        <div class="brand">Salt Flow Bar</div>
        <?php foreach($cats as $c): ?>
            <div class="category" id="<?= $c['slug'] ?>"><?= $c['name'] ?></div>
            <?php foreach(getProducts($pdo, $c['id']) as $p): ?>
                <div class="item" style="<?= $p['is_active'] ? '' : 'opacity: 0.5;' ?>">
                    <div>
                        <div class="item-name"><?= htmlspecialchars($p['name']) ?> <?php if(!$p['is_active']) echo '<span style="color:red; font-size:12px; border:1px solid red;">OFFLINE</span>'; ?></div>
                        <div class="item-desc"><?= $p['i_names'] ? htmlspecialchars($p['i_names']) : htmlspecialchars($p['description']) ?></div>
                    </div>
                    <div class="actions">
                        <span class="price"><?= number_format($p['price'], 2) ?>€</span>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'cliente'): ?>
                            <button class="btn-pedir" onclick="openModal(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', '<?= $p['i_ids'] ?>', '<?= addslashes($p['i_names'] ?? '') ?>')">Order</button>
                        <?php elseif(isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff')): ?>
                             <button class="btn-pedir" disabled style="background:#555">Staff</button>
                        <?php else: ?>
                            <button class="btn-pedir" disabled style="background:#333; color:#777" title="Login required">Login</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <div id="ingModal" class="modal-overlay">
        <form id="cartForm" class="modal-box">
            <h3 id="mTitle" style="font-family:'Permanent Marker'; font-size:24px;"></h3>
            <p style="color:#aaa; font-size:14px; margin-bottom:15px;">Uncheck ingredients to remove:</p>
            <input type="hidden" name="pid" id="mPid">
            <div id="mList" style="margin-bottom:20px; max-height:200px; overflow-y:auto;"></div>
            <div class="modal-btns">
                <button type="button" class="btn-cancel" onclick="document.getElementById('ingModal').style.display='none'">Cancel</button>
                <button type="submit" class="checkout-button" style="padding:10px 20px;">Add to Cart</button>
            </div>
        </form>
    </div>

    <script src="script.js"></script>
    <script>
        function openModal(id, name, ids, names) {
            document.getElementById('ingModal').style.display = 'flex';
            document.getElementById('mTitle').innerText = name;
            document.getElementById('mPid').value = id;
            const list = document.getElementById('mList');
            list.innerHTML = '';
            if(!ids) list.innerHTML = '<p style="text-align:center; color:#777">Sem ingredientes personalizáveis.</p>';
            else {
                const idArr = ids.split(',');
                const nameArr = names.split(', ');
                idArr.forEach((iid, i) => {
                    list.innerHTML += `<div class="ing-row"><label>${nameArr[i]}</label><input type="checkbox" name="ing[]" value="${iid}" checked></div>`;
                });
            }
        }
        document.getElementById('cartForm').addEventListener('submit', function(e){
            e.preventDefault();
            const fd = new FormData(this);
            fetch('cart_actions.php', { method:'POST', body:fd }).then(r=>r.json()).then(d=>{ alert(d.msg); location.reload(); });
        });
    </script>
</body>
</html>