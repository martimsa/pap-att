// Função para alternar um menu lateral (sidebar).
// OBS: Não parece estar sendo usada nos arquivos HTML fornecidos, pois não há elemento com id="sidebar".
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("open");
}

// Simula a adição de um item ao pedido, exibindo um alerta para o usuário.
function addOrder(itemName) {
  alert(`${itemName} added to your order!`);
}

// Função para exibir ou ocultar o menu de navegação mobile.
function menuShow() {
  let menuMobile = document.querySelector(".mobile-menu");
  // Verifica se o menu já está aberto
  if (menuMobile.classList.contains("open")) {
    // Adiciona a classe 'closing' para ativar a animação de fechamento (definida no CSS)
    menuMobile.classList.add("closing");

    // Espera pelo fim da transição para remover a classe 'open' e 'closing'
    setTimeout(() => {
      menuMobile.classList.remove("open");
      menuMobile.classList.remove("closing");
      document.querySelector(".icon").src = "imagens/menu_white_36dp.svg";
    }, 300); // A duração deve ser a mesma da transição no CSS.
  } else {
    // Se o menu estiver fechado, adiciona a classe 'open' para exibi-lo e troca o ícone para 'fechar'.
    menuMobile.classList.add("open");
    menuMobile.classList.remove("closing");
    document.querySelector(".icon").src = "imagens/close_white_36dp.svg";
  }
}

// Adiciona um listener de evento de rolagem (scroll) na janela.
window.addEventListener("scroll", function () {
  const header = document.querySelector("header");
  // Se o usuário rolar mais de 50 pixels para baixo...
  if (window.scrollY > 50) {
    // ...adiciona a classe 'scrolled' ao cabeçalho. Isso permite estilizá-lo de forma diferente via CSS (ex: diminuir altura).
    header.classList.add("scrolled");
  } else {
    // ...senão, remove a classe.
    header.classList.remove("scrolled");
  }
});

// Adiciona um listener de clique a todos os elementos com a classe '.mobile-menu'.
// Isso faz com que o menu se feche quando o usuário clica em um link de navegação dentro dele.
document.querySelectorAll(".mobile-menu").forEach((item) => {
  item.addEventListener("click", menuShow);
});

document.addEventListener('DOMContentLoaded', function () {
  const registerForm = document.getElementById('registerForm');
  const overlay = document.getElementById('confirmOverlay');

  if (registerForm && overlay) {
    registerForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const pwd = registerForm.querySelector('#password')?.value || '';
      const pwd2 = registerForm.querySelector('#confirm_password')?.value || '';

      if (pwd !== pwd2) {
        alert('The passwords do not match.');
        return;
      }

      // Bloqueia o botão e mostra a animação de confirmação
      const submitBtn = registerForm.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;

      overlay.classList.add('show');
      overlay.setAttribute('aria-hidden', 'false');

      // Após a animação, redireciona para index.html
      setTimeout(function () {
        window.location.href = 'index.html';
      }, 1600); // 1.6s — ajustável conforme preferir
    });
  }})
