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
  const menuIcon = document.querySelector(".icon");
  const body = document.body;
  
  if (menuMobile.classList.contains("open")) {
    menuMobile.classList.add("closing");
    // Libera o scroll do site
    body.classList.remove("no-scroll");

    setTimeout(() => {
      menuMobile.classList.remove("open");
      menuMobile.classList.remove("closing");
      if (menuIcon) {
          menuIcon.src = "imagens/menu_white_36dp.svg";
      }
    }, 300); 
  } else {
    menuMobile.classList.add("open");
    menuMobile.classList.remove("closing");
    // Bloqueia scroll do site
    body.classList.add("no-scroll");
    if (menuIcon) {
        menuIcon.src = "imagens/close_white_36dp.svg"; 
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
  item.addEventListener("click", menuShow);
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