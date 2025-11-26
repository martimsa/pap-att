function menuShow() {
  let menuMobile = document.querySelector(".mobile-menu");
  if (menuMobile.classList.contains("open")) {
    menuMobile.classList.add("closing");
    setTimeout(() => {
      menuMobile.classList.remove("open");
      menuMobile.classList.remove("closing");
    }, 300);
  } else {
    menuMobile.classList.add("open");
    menuMobile.classList.remove("closing");
  }
}

// Scroll Animation
window.addEventListener("scroll", function () {
  const header = document.querySelector("header");
  if (window.scrollY > 50) header.classList.add("scrolled");
  else header.classList.remove("scrolled");
});

document.querySelectorAll(".mobile-menu a").forEach((item) => {
  item.addEventListener("click", menuShow);
});