var swiper = new Swiper(".slide-content", {
  slidesPerView: 3,
  spaceBetween: 25,
  loop: true,
  centerSlide: 'true',
  fade: 'true',
  grabCursor: 'true',
  autoplay: {
    delay: 3000,
    disableOnInteraction: false,
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
    dynamicBullets: true,
  },
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    520: {
      slidesPerView: 2,
    },
    950: {
      slidesPerView: 3,
    },
  },
});

const viewButtons = document.querySelectorAll('.button');
const detailsCards = document.querySelectorAll('.details-card');
let activeIndex = null;

viewButtons.forEach((button, index) => {
  button.addEventListener('click', () => {
    swiper.autoplay.stop();
    activeIndex = index;
    showDetails(activeIndex);
  });
});

function showDetails(index) {
  detailsCards.forEach((card, i) => {
    card.classList.remove('show');
  });
  detailsCards[index].classList.add('show');
  const xButton = document.createElement('button');
  xButton.textContent = 'X';
  xButton.classList.add('close-btn');
  detailsCards[index].appendChild(xButton);
  xButton.addEventListener('click', () => {
    detailsCards[index].classList.remove('show');
    swiper.autoplay.start();
    xButton.remove();
    activeIndex = null;
  });
}

swiper.on('slideChange', function () {
  if (activeIndex !== null) {
    detailsCards[activeIndex].classList.remove('show');
    activeIndex = null;
    swiper.autoplay.start();
  }
});