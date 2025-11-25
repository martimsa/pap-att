<?php
session_start();
require 'db_connect.php';

// 1. Buscar Categorias
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para buscar produtos e seus ingredientes
function getProducts($pdo, $catId) {
    $sql = "SELECT p.*, 
            GROUP_CONCAT(i.id) as ing_ids, 
            GROUP_CONCAT(i.name SEPARATOR ', ') as ing_names 
            FROM products p
            LEFT JOIN product_ingredients pi ON p.id = pi.product_id
            LEFT JOIN ingredients i ON pi.ingredient_id = i.id
            WHERE p.category_id = ? 
            GROUP BY p.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Salt Flow ≋ Beach Bar</title>
    <link rel="icon" type="image/x-icon" href="imagens/logo_menu.svg" />
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css" />
    
    <style>
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; justify-content: center; align-items: center; }
        .modal-box { background: #1b1b1b; padding: 25px; border-radius: 12px; max-width: 400px; width: 90%; border: 2px solid #f06aa6; text-align: center; }
        .ing-option { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #333; font-family: 'Roboto'; }
        .ing-option input { width: 20px; height: 20px; accent-color: #f06aa6; }
        .modal-btns { margin-top: 20px; display: flex; gap: 10px; justify-content: center; }
        .btn-cancel { background: #555; border: none; padding: 10px 20px; color: white; border-radius: 5px; cursor: pointer; font-family: "Amatic SC"; font-size: 20px;}
    </style>
</head>
<body>
    <header>
      <nav class="nav-bar">
        <div class="logo"><a href="index.php"><img class="logo-ft" src="imagens/logo_menu.jpg" width="40px" alt="Logo"/></a></div>
        <div class="nav-list">
            <ul>
                <?php foreach($categories as $cat): ?>
                    <li class="nav-item"><a href="#<?= $cat['slug'] ?>" class="nav-link"><?= $cat['name'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="header-icons">
          <div class="login-button"><a href="login.html"><img src="imagens/user_icon.svg" class="user-icon-img" /></a></div>
          
          <div class="cart-icon">
            <a href="cart.php">
                <img src="imagens/cart_icon.svg" class="cart-icon-img" />
                <span style="color: #f06aa6; font-weight:bold; position: relative; top:-10px;">
                    <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                </span>
            </a>
          </div>
          
          <div class="mobile-menu-icon"><button onclick="menuShow()"><img class="icon" src="imagens/menu_white_36dp.svg" /></button></div>
        </div>
      </nav>
      <div class="mobile-menu">
        <ul>
            <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
             <?php foreach($categories as $cat): ?>
                <li class="nav-item"><a href="#<?= $cat['slug'] ?>" class="nav-link"><?= $cat['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>        
      </div>
    </header>

    <div class="menu-board">
      <div class="brand">Salt Flow Bar</div>

      <?php foreach($categories as $cat): ?>
        <div class="category" id="<?= $cat['slug'] ?>"><?= $cat['name'] ?></div>
        
        <?php 
            $products = getProducts($pdo, $cat['id']);
            foreach($products as $prod): 
        ?>
            <div class="item">
                <div>
                    <div class="item-name"><?= $prod['name'] ?></div>
                    <div class="item-desc"><?= $prod['ing_names'] ? $prod['ing_names'] : $prod['description'] ?></div>
                </div>
                <div class="actions">
                    <span class="price"><?= number_format($prod['price'], 2) ?>€</span>
                    <button class="btn-pedir" onclick="abrirModal(
                        <?= $prod['id'] ?>, 
                        '<?= addslashes($prod['name']) ?>', 
                        '<?= $prod['ing_ids'] ?>', 
                        '<?= addslashes($prod['ing_names']) ?>'
                    )">Order</button>
                </div>
            </div>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </div>

    <div id="modalIngredientes" class="modal-overlay">
        <form id="formAdicionarCarrinho" class="modal-box" onsubmit="adicionarAoCarrinho(event)">
            <h3 id="modalTitulo" style="font-family: 'Permanent Marker'; font-size: 26px; color:#fff; margin-bottom:15px;"></h3>
            <p style="color:#ccc; font-size:14px; margin-bottom:15px;">Uncheck to remove ingredients:</p>
            
            <input type="hidden" name="product_id" id="modalProdId">
            <div id="listaIngredientes"></div>

            <div class="modal-btns">
                <button type="button" class="btn-cancel" onclick="fecharModal()">Cancel</button>
                <button type="submit" class="checkout-button" style="padding: 5px 20px; font-size:20px;">Add to Cart</button>
            </div>
        </form>
    </div>

    <footer class="site-footer">
        <div class="footer-inner">
          <div class="footer-brand">Salt Flow Bar</div>
          <div class="footer-links"><span>© 2025 Salt Flow Beach Bar</span></div>
        </div>
    </footer>
    
    <script src="script.js"></script>
    <script>
        // Lógica do Modal
        const modal = document.getElementById('modalIngredientes');
        const lista = document.getElementById('listaIngredientes');

        function abrirModal(id, nome, idsStr, nomesStr) {
            document.getElementById('modalTitulo').innerText = nome;
            document.getElementById('modalProdId').value = id;
            lista.innerHTML = '';

            if(!idsStr) {
                lista.innerHTML = '<p style="color:#777">No customizable ingredients.</p>';
            } else {
                const ids = idsStr.split(',');
                const nomes = nomesStr.split(', ');
                
                ids.forEach((ingId, index) => {
                    // Cria checkboxes marcadas por defeito
                    lista.innerHTML += `
                        <div class="ing-option">
                            <label>${nomes[index]}</label>
                            <input type="checkbox" name="ingredientes[]" value="${ingId}" checked>
                        </div>
                    `;
                });
            }
            modal.style.display = 'flex';
        }

        function fecharModal() {
            modal.style.display = 'none';
        }

        function adicionarAoCarrinho(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('formAdicionarCarrinho'));

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                fecharModal();
                location.reload(); // Atualiza a página para mudar o número do carrinho
            });
        }
    </script>
</body>
</html>