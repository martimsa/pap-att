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
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin_products.php" class="nav-link">Produtos</a></li>
                    <?php endif; ?>
                    <?php if($_SESSION['role'] === 'admin'): ?>
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
                <a href="logout.php" class="header-logout-link" style="margin-left: 10px;">
                    <img src="imagens/user_icon_out.svg" class="user-icon-img" alt="Sair">
                </a>
            <?php else: ?>
                <div class="login-button">
                    <a href="login.php">
                        <img src="imagens/user_icon.svg" class="user-icon-img" alt="Login">
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="cart-icon">
                <a href="cart.php">
                    <img src="imagens/cart_icon.svg" class="cart-icon-img" alt="Carrinho" />
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
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin_products.php" class="nav-link">Produtos</a></li>
                <?php endif; ?>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin_users.php" class="nav-link">Utilizadores</a></li>
                <?php endif; ?>
                <?php if($_SESSION['role'] === 'configurador'): ?>
                    <li><a href="configurador.php" class="nav-link">Config</a></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php 
            if(isset($pdo)) {
                $menu_cats = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
                foreach($menu_cats as $mc): ?>
                    <li><a href="index.php#<?= $mc['slug'] ?>" class="nav-link" onclick="menuShow()"><?= htmlspecialchars($mc['name']) ?></a></li>
            <?php endforeach; } ?>

            <?php if(isset($_SESSION['role'])): ?>
                <li><a href="logout.php" class="log-out">Sair</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>
