import "./utils";

import("./components/layout/smooth-scroll").catch((error) => {
  console.error("Failed to load SmoothScroll module:", error);
});

if (document.querySelector("[data-header]")) {
  import("./components/layout/header").catch((error) => {
    console.error("Failed to load Header module:", error);
  });
}

if (document.querySelector("[data-scroll-top]")) {
  import("./components/layout/scroll-to-top").catch((error) => {
    console.error("Failed to load ScrollToTop module:", error);
  });
}

if (document.querySelector(".swiper")) {
  import("./swipers/main").catch((error) => {
    console.error("Failed to load Swiper module:", error);
  });
}

if (document.querySelector("[data-created-slider]")) {
  import("./components/home/created").catch((error) => {
    console.error("Failed to load Created slider module:", error);
  });
}
if (document.querySelector(".backdrop")) {
  import("./popups/main").catch((error) => {
    console.error("Failed to load Popups module:", error);
  });
}

import "../css/main.scss";
