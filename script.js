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
      list.innerHTML += `
        <div class="ing-row">
          <label>${nameArr[i]}</label>
          <input type="checkbox" name="ing[]" value="${iid.trim()}" checked>
        </div>`;
    });
  }
}

// Fechar modal ao clicar fora ou no botão cancelar
window.onclick = function(event) {
  const modal = document.getElementById('ingModal');
  if (event.target == modal) modal.style.display = "none";
}

// --- Funções de Navegação e Menu ---
function menuShow() {
  let menuMobile = document.querySelector('.mobile-menu');
  let icon = document.querySelector('.icon');
  if (menuMobile.classList.contains('open')) {
    menuMobile.classList.remove('open');
    icon.src = "imagens/menu_white_36dp.svg";
  } else {
    menuMobile.classList.add('open');
    icon.src = "imagens/close_white_36dp.svg";
  }
}

// Efeito de scroll no Header
window.addEventListener("scroll", function() {
  const header = document.querySelector("header");
  if (window.scrollY > 50) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

// AJAX para Adicionar ao Carrinho
document.getElementById('cartForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('add_to_cart.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.success) {
      alert(data.message);
      document.getElementById('ingModal').style.display = 'none';
      // Atualiza o contador visualmente sem recarregar a página
      const countEl = document.querySelector('.cart-count');
      if(countEl) countEl.innerText = data.cart_count;
    }
  });
});