// --- Funções do Modal de Ingredientes (index.php) ---

function openModal(id, name, ids, names) {
  const modal = document.getElementById('ingModal');
  if (!modal) return;

  modal.style.display = 'flex';
  document.getElementById('mTitle').innerText = name;
  document.getElementById('mPid').value = id;
  const list = document.getElementById('mList');
  list.innerHTML = '';
  if (!ids || ids.trim() === '') {
    list.innerHTML = '<p style="text-align:center; color:#777">Sem ingredientes personalizáveis.</p>';
  } else {
    const idArr = ids.split(',');
    const nameArr = names.split(', ');
    idArr.forEach((iid, i) => {
      list.innerHTML += `<div class="ing-row"><label>${nameArr[i]}</label><input type="checkbox" name="ing[]" value="${iid.trim()}" checked></div>`;
    });
  }
}

// Função para alternar sidebar (se houver)
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("open");
}

// Exibe overlay de confirmação
function showOrderConfirmation(message) {
  const overlay = document.getElementById('orderConfirmOverlay'); 
  const confirmMessage = document.getElementById('orderConfirmMessage'); 

  if (overlay && confirmMessage) {
    confirmMessage.textContent = message;
    
    overlay.classList.add('show');
    overlay.setAttribute('aria-hidden', 'false');

    setTimeout(function() {
      overlay.classList.remove('show');
      overlay.setAttribute('aria-hidden', 'true');
    }, 2500); 
  } else {
    alert(message); 
  }
}

// Simula adição de item
function addOrder(itemName) {
  showOrderConfirmation("Order for " + itemName + " sent!");
}

// Função para exibir/ocultar menu mobile
function menuShow() {
  let menuMobile = document.querySelector(".mobile-menu");
  const menuOverlay = document.querySelector(".mobile-menu-overlay");
  const menuIcon = document.querySelector(".icon");
  const body = document.body;
  
  if (!menuMobile) return;
  
  if (menuMobile.classList.contains("open")) {
    menuMobile.classList.add("closing");
    if (menuOverlay) {
      menuOverlay.classList.remove("active");
    }
    // Libera o scroll do site
    body.classList.remove("no-scroll");

    setTimeout(() => {
      menuMobile.classList.remove("open");
      menuMobile.classList.remove("closing");
      if (menuIcon) {
          menuIcon.src = "imagens/menu_white_36dp.svg";
      }
    }, 350); 
  } else {
    menuMobile.classList.add("open");
    menuMobile.classList.remove("closing");
    if (menuOverlay) {
      menuOverlay.classList.add("active");
    }
    // Bloqueia scroll do site
    body.classList.add("no-scroll");
    if (menuIcon) {
        menuIcon.src = "imagens/close_white_36dp.svg"; 
    }
  }
}

// Suporte para gestos de swipe (deslizar) para fechar o menu
let touchStartX = 0;
let touchEndX = 0;
let touchStartY = 0;
let touchEndY = 0;

document.addEventListener('DOMContentLoaded', function() {
  // Event listener para o formulário de adicionar ao carrinho (index.php)
  const cartForm = document.getElementById('cartForm');
  if (cartForm) {
    cartForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const fd = new FormData(this);
      fetch('add_to_cart.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
          alert(d.msg || d.message);
          location.reload();
        });
    });
  }

  const menuMobile = document.querySelector(".mobile-menu");
  const menuHeader = document.querySelector(".mobile-menu-header");
  
  if (menuMobile && menuHeader) {
    // Swipe no header para fechar (mais intuitivo)
    menuHeader.addEventListener('touchstart', function(e) {
      touchStartX = e.changedTouches[0].screenX;
      touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });
    
    menuHeader.addEventListener('touchend', function(e) {
      touchEndX = e.changedTouches[0].screenX;
      touchEndY = e.changedTouches[0].screenY;
      handleSwipe();
    }, { passive: true });
    
    // Swipe na borda esquerda do menu também funciona
    let swipeStartTime = 0;
    menuMobile.addEventListener('touchstart', function(e) {
      // Só detecta swipe se começar na borda esquerda (primeiros 30px)
      if (e.changedTouches[0].clientX < 30) {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
        swipeStartTime = Date.now();
      }
    }, { passive: true });
    
    menuMobile.addEventListener('touchend', function(e) {
      if (swipeStartTime > 0 && (Date.now() - swipeStartTime) < 500) {
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;
        handleSwipe();
        swipeStartTime = 0;
      }
    }, { passive: true });
  }
});

function handleSwipe() {
  const swipeThreshold = 80; // Mínimo de pixels para considerar swipe
  const swipeDistanceX = touchEndX - touchStartX;
  const swipeDistanceY = Math.abs(touchEndY - touchStartY);
  
  // Só fecha se o swipe horizontal for maior que o vertical (evita conflito com scroll)
  if (swipeDistanceX > swipeThreshold && swipeDistanceX > swipeDistanceY) {
    const menuMobile = document.querySelector(".mobile-menu");
    if (menuMobile && menuMobile.classList.contains("open")) {
      menuShow();
    }
  }
}

// Scroll header effect
document.addEventListener("scroll", () => {
  const header = document.querySelector("header");
  if (window.scrollY > 50) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

// Fecha o menu ao clicar num link
document.querySelectorAll(".mobile-menu .nav-link").forEach((item) => {
  item.addEventListener("click", function() {
    // Pequeno delay para permitir que o clique seja processado
    setTimeout(menuShow, 100);
  });
});

// Fecha o menu ao pressionar ESC
document.addEventListener("keydown", function(e) {
  if (e.key === "Escape") {
    const menuMobile = document.querySelector(".mobile-menu");
    if (menuMobile && menuMobile.classList.contains("open")) {
      menuShow();
    }
  }
});

// Registo Form
document.addEventListener('DOMContentLoaded', function () {
  const registerForm = document.getElementById('registerForm');
  const overlay = document.getElementById('confirmOverlay');

  if (registerForm && overlay) {
    registerForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const pwd = registerForm.querySelector('#password')?.value || '';
      const pwd2 = registerForm.querySelector('#confirm_password')?.value || '';

      if (pwd !== pwd2) {
        alert('Passwords do not match.');
        return;
      }

      const submitBtn = registerForm.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;

      overlay.classList.add('show');
      overlay.setAttribute('aria-hidden', 'false');

      setTimeout(function () {
        window.location.href = "index.html"; 
      }, 3000); 
    });
  }
});