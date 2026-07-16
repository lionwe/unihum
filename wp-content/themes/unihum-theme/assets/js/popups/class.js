function getProp(element, property, default_value = "") {
  return getComputedStyle(element).getPropertyValue(property) || default_value;
}

export class Popup {
  constructor(selector = "", settings = {}) {
    if (!selector) return;
    this.selector = selector;
    this.element = document.querySelector(selector);
    if (this.element) {
      const closeButtonSelector = settings.closeButton || ".close-button";
      this.closeButton = this.element.querySelector(closeButtonSelector);
    } else {
      return console.error("Popup not found:", selector);
    }

    this.canAvtion = true;
    this.openButtonsSelector = settings.openButtons || "";
    this.openButtons = [];
    this.closeOnResize = settings.closeOnResize || false;
    this.on = settings.on || {};
    this.resizeTimer = null;
    this.animations = {};

    this.init();
  }

  init() {
    if (this.closeButton) {
      this.closeButton.addEventListener("click", this.close.bind(this));
    }
    this.element.addEventListener("click", (e) => {
      if (e.target == this.element) {
        this.close();
      }
    });
    if (this.openButtonsSelector) {
      this.openButtons = Array.from(
        document.querySelectorAll(this.openButtonsSelector)
      );
      this.openButtons.forEach((button) => {
        button.addEventListener("click", this.open.bind(this));
      });
    }
    if (this.closeOnResize) {
      window.addEventListener("resize", () => {
        if (!this.element.hasAttribute("open")) return;
        clearTimeout(this.resizeTimer);
        this.resizeTimer = setTimeout(() => {
          this.close();
        }, 200);
      });
    }

    let openDuration = getProp(this.element, "--_open", 600);
    if (typeof openDuration === "string") {
      if (openDuration.endsWith("ms")) {
        openDuration = parseFloat(openDuration.slice(0, -2));
      } else if (openDuration.endsWith("s")) {
        openDuration = parseFloat(openDuration.slice(0, -1)) * 1000;
      } else {
        openDuration = 500;
      }
    }
    this.animations.open = openDuration;
    let closeDuration = getProp(this.element, "--_close", 600);
    if (typeof closeDuration === "string") {
      if (closeDuration.endsWith("ms")) {
        closeDuration = parseFloat(closeDuration.slice(0, -2));
      } else if (closeDuration.endsWith("s")) {
        closeDuration = parseFloat(closeDuration.slice(0, -1)) * 1000;
      }
    }
    this.animations.close = closeDuration;
    console.info("Popup initialized:", this.selector);
    if (this.on?.init) {
      const initCallback =
        typeof this.on.init === "function" ? this.on.init : () => {};
      initCallback.call(this); // Виконуємо функцію з контекстом поточного об'єк
    }
  }
  open() {
    if (!this.canAvtion) return;
    console.info("Popup opened:", this.selector);
    this.canAvtion = false;
    this.element.removeAttribute("close");
    this.element.setAttribute("open", "");
    setTimeout(() => {
      this.canAvtion = true;
    }, this.animations.open);
    if (this.on?.open) {
      const openCallback =
        typeof this.on.open === "function" ? this.on.open : () => {};
      openCallback.call(this); // Виконуємо функцію з контекстом поточного об'єкта
    }
  }
  close() {
    if (!this.canAvtion) return;
    console.info("Popup closed:", this.selector);
    this.canAvtion = false;
    this.element.setAttribute("close", "");
    this.element.removeAttribute("open");
    setTimeout(() => {
      this.canAvtion = true;
    }, this.animations.open);
    if (this.on?.close) {
      const closeCallback =
        typeof this.on.close === "function" ? this.on.close : () => {};
      closeCallback.call(this); // Виконуємо функцію з контекстом поточного об'єкта
    }
  }
  toggle() {
    if (!this.canAvtion) return;
    console.info("Popup toggled:", this.selector);
    this.canAvtion = false;
    this.element.hasAttribute("open") ? this.close() : this.open();
    setTimeout(() => {
      this.canAvtion = true;
    }, this.animations.open);
    if (this.on?.toggle) {
      const toggleCallback =
        typeof this.on.toggle === "function" ? this.on.toggle : () => {};
      toggleCallback.call(this); // Виконуємо функцію з контекстом поточного об'єкта
    }
  }
}
