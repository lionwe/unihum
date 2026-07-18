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

  const currentElement = section.querySelector("[data-created-current]");
  const updateCurrentSlide = (swiper) => {
    if (!currentElement) {
      return;
    }

    currentElement.textContent = String(swiper.realIndex + 1).padStart(2, "0");
  };

  new Swiper(slider, {
    modules: [A11y, Keyboard, Navigation, Pagination],
    centeredSlides: true,
    grabCursor: true,
    slideToClickedSlide: true,
    watchSlidesProgress: true,
    keyboard: {
      enabled: true,
    },
    loop: slideCount > 3,
    slidesPerView: 1.08,
    spaceBetween: 16,
    navigation: {
      nextEl: section.querySelector(".created__navigation--next"),
      prevEl: section.querySelector(".created__navigation--prev"),
    },
    pagination: {
      el: section.querySelector(".created__pagination"),
      clickable: true,
    },
    on: {
      init: updateCurrentSlide,
      slideChange: updateCurrentSlide,
    },
    breakpoints: {
      576: {
        slidesPerView: 1.65,
        spaceBetween: 24,
      },
      992: {
        slidesPerView: 2,
        spaceBetween: 28,
      },
      1200: {
        slidesPerView: 3,
        spaceBetween: 28,
      },
    },
  });
});
