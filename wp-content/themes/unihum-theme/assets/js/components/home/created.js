import Swiper from "swiper";
import { A11y, Keyboard, Navigation, Pagination } from "swiper/modules";

document.querySelectorAll("[data-created-slider]").forEach((slider) => {
  const slideCount = slider.querySelectorAll(".swiper-slide").length;
  const section = slider.closest(".created");

  if (!section) {
    return;
  }

  if (slideCount < 2) {
    return;
  }

  new Swiper(slider, {
    modules: [A11y, Keyboard, Navigation, Pagination],
    centeredSlides: true,
    grabCursor: true,
    keyboard: {
      enabled: true,
    },
    loop: slideCount > 2,
    slidesPerView: 1.14,
    spaceBetween: 16,
    navigation: {
      nextEl: section.querySelector(".created__navigation--next"),
      prevEl: section.querySelector(".created__navigation--prev"),
    },
    pagination: {
      el: section.querySelector(".created__pagination"),
      clickable: true,
    },
    breakpoints: {
      576: {
        slidesPerView: 1.45,
        spaceBetween: 24,
      },
      992: {
        slidesPerView: 1.8,
        spaceBetween: 28,
      },
    },
  });
});
