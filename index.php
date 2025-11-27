<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Salt Flow ≋ Beach Bar</title>

    <link rel="icon" type="image/x-icon" href="imagens/logo_menu.svg" />

    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&family=Permanent+Marker&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"/>

    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <header>
      <nav class="nav-bar">
        <div class="logo">
          <a href="index.html">
            <img class="logo-ft" src="imagens/logo_menu.jpg" width="40px" alt="Logo"/>
          </a>
        </div>

        <div class="nav-list">
          <ul>
            <li class="nav-item">
              <a href="index.html" class="nav-link">Home</a>
            </li>
            </ul>
        </div>

        <div class="header-icons">
          <a href="cart.html" class="cart-icon">
            <img src="imagens/cart_icon.svg" alt="Shopping Cart" class="cart-icon-img" />
          </a>
          <a href="login.html" class="login-button">
            <img src="imagens/user_icon.svg" alt="User Login" class="user-icon-img" />
          </a>
          <div class="mobile-menu-icon">
            <button onclick="menuShow()">
              <img class="icon" src="imagens/menu_white_36dp.svg" alt="Menu Icon" />
            </button>
          </div>
        </div>
      </nav>

      <div class="mobile-menu">
        <ul>
          <li class="nav-item">
            <a href="index.html" class="nav-link">Home</a>
          </li>
          </ul>
      </div>
    </header>

    <div class="menu-board">
      <div class="brand">Salt Flow Menu</div>

      <div class="category" id="snacks">Snacks</div>
      <div class="item">
        <div>
          <div class="item-name">Toast</div>
          <div class="item-desc">Toast with cheese and ham.</div>
        </div>
        <div class="actions">
          <span class="price">4,50€</span>
          <button class="btn-pedir" onclick="addOrder('Toast')">Order</button>
        </div>
      </div>
      
      <div class="item">
        <div>
          <div class="item-name">Salty Biscuit</div>
          <div class="item-desc"></div>
        </div>
        <div class="actions">
          <span class="price">1€</span>
          <button class="btn-pedir" onclick="addOrder('Salty Biscuit')">Order</button>
        </div>
      </div>

      <div class="category" id="drinks">Drinks</div>
      <div class="item">
        <div>
          <div class="item-name">Water</div>
          <div class="item-desc"></div>
        </div>
        <div class="actions">
          <span class="price">1€</span>
          <button class="btn-pedir" onclick="addOrder('Water')">Order</button>
        </div>
      </div>
      
      <div class="item">
        <div>
          <div class="item-name">Coke</div>
          <div class="item-desc"></div>
        </div>
        <div class="actions">
          <span class="price">1,50€</span>
          <button class="btn-pedir" onclick="addOrder('Coke')">Order</button>
        </div>
      </div>

      <div class="item">
        <div>
          <div class="item-name">Pedras</div>
        </div>
        <div class="actions">
          <span class="price">2€</span>
          <button class="btn-pedir" onclick="addOrder('Pedras')">Order</button>
        </div>
      </div>

      <div class="category" id="coffee">Coffee</div>
      <div class="item">
        <div>
          <div class="item-name">coffe</div>  
        </div>
        <div class="actions">
          <span class="price">1,20€</span>
          <button class="btn-pedir" onclick="addOrder('Coffee')">Order</button>
        </div>
      </div>

      <div class="category" id="wine">Wine</div>
      <div class="item">
        <div>
          <div class="item-name">Glass of wine</div>
        </div>
        <div class="actions">
          <span class="price">5€</span>
          <button class="btn-pedir" onclick="addOrder('Glass of wine')">Order</button>
        </div>
      </div>
    </div>

      <footer class="site-footer">
        <div class="footer-inner">
          <div class="footer-brand">Salt Flow Bar</div>
          <div class="footer-links">
            <span>© 2025 Salt Flow Beach Bar</span>
            <span class="sep">|</span>
            <a href="privacy.html">Privacy</a>
            <span class="sep">|</span>
            <a href="terms.html">Terms</a>
          </div>
        </div>
      </footer>
    
    <div id="orderConfirmOverlay" class="confirm-overlay" aria-hidden="true">
      <div class="confirm-card" role="status" aria-live="polite">
        <svg class="checkmark" viewBox="0 0 52 52" aria-hidden="true">
          <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
          <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
        </svg>
        <p class="confirm-text" id="orderConfirmMessage">Pedido efetuado com sucesso!</p>
      </div>
    </div>
    
    <script src="script.js"></script>
  </body>
</html>