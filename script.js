// Alternar menu lateral
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("open");
}

// Simula adicionar pedido
function addOrder(itemName) {
  if (lang === "pt") {
    alert(`${itemName} adicionado ao pedido!`);
  } else {
    alert(`${itemName} added to your order!`);
  }
}

// Menu
function menuShow() {
  let menuMobile = document.querySelector(".mobile-menu");
  if (menuMobile.classList.contains("open")) {
    // Adiciona a classe 'closing' para ativar a transição
    menuMobile.classList.add("closing");

    // Espera pelo fim da transição para remover a classe 'open' e 'closing'
    setTimeout(() => {
      menuMobile.classList.remove("open");
      menuMobile.classList.remove("closing");
      document.querySelector(".icon").src = "imagens/menu_white_36dp.svg";
    }, 300); // 300ms é a duração da transição. Ajuste conforme necessário
  } else {
    menuMobile.classList.add("open");
    menuMobile.classList.remove("closing");
    document.querySelector(".icon").src = "imagens/close_white_36dp.svg";
  }
}

// Transição da nav-bar
window.addEventListener("scroll", function () {
  const header = document.querySelector("header");
  if (window.scrollY > 50) {
    // diminui 50px após scroll
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

// Fecha o menu mobile ao clicar em um link
document.querySelectorAll(".mobile-menu").forEach((item) => {
  item.addEventListener("click", menuShow);
});

