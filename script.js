// Função para alternar um menu lateral (sidebar).
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("open");
}

// ----------------------------------------------------
// NOVA FUNÇÃO: Exibe o overlay de confirmação de pedido
// ----------------------------------------------------
function showOrderConfirmation(message) {
  const overlay = document.getElementById('orderConfirmOverlay'); // ID do overlay do pedido
  const confirmMessage = document.getElementById('orderConfirmMessage'); // ID da mensagem

  if (overlay && confirmMessage) {
    confirmMessage.textContent = message;
    
    // Mostra o overlay
    overlay.classList.add('show');
    overlay.setAttribute('aria-hidden', 'false');

    // Remove o overlay após um atraso (2.5 segundos)
    setTimeout(function() {
      overlay.classList.remove('show');
      overlay.setAttribute('aria-hidden', 'true');
    }, 2500); // 2.5 segundos
  } else {
    // Fallback caso o overlay não esteja no HTML
    alert(message); 
  }
}

// Simula a adição de um item ao pedido, exibindo a nova confirmação.
function addOrder(itemName) {
  // A lógica de adição ao carrinho real viria aqui.
  showOrderConfirmation("Pedido de " + itemName + " enviado!");
}

// Função para exibir ou ocultar o menu de navegação mobile.
function menuShow() {
  let menuMobile = document.querySelector(".mobile-menu");
  const menuIcon = document.querySelector(".icon");
  
  if (menuMobile.classList.contains("open")) {
    menuMobile.classList.add("closing");

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
    if (menuIcon) {
        menuIcon.src = "imagens/close_white_36dp.svg"; // Assumindo que tem um ícone de fechar
    }
  }
}

// Event listener para mudar o estilo do cabeçalho ao rolar a página
document.addEventListener("scroll", () => {
  const header = document.querySelector("header");
  if (window.scrollY > 50) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

// Adiciona um listener de clique a todos os elementos com a classe '.mobile-menu'.
document.querySelectorAll(".mobile-menu").forEach((item) => {
  item.addEventListener("click", menuShow);
});

// Lógica de confirmação de registro
document.addEventListener('DOMContentLoaded', function () {
  const registerForm = document.getElementById('registerForm');
  const overlay = document.getElementById('confirmOverlay');

  if (registerForm && overlay) {
    registerForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const pwd = registerForm.querySelector('#password')?.value || '';
      const pwd2 = registerForm.querySelector('#confirm_password')?.value || '';

      if (pwd !== pwd2) {
        alert('As palavras-passe não coincidem.');
        return;
      }

      // Bloqueia o botão e mostra a animação de confirmação
      const submitBtn = registerForm.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;

      overlay.classList.add('show');
      overlay.setAttribute('aria-hidden', 'false');

      // Após a animação, redireciona para index.html
      setTimeout(function () {
        window.location.href = "index.html"; 
      }, 3000); // 3 segundos
    });
  }
});