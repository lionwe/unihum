class Header {
  constructor(element) {
    this.element = element;
    this.toggle = element.querySelector("[data-header-toggle]");
    this.panel = element.querySelector("[data-header-panel]");

    if (!this.toggle || !this.panel) {
      return;
    }

    this.handleToggle = this.handleToggle.bind(this);
    this.handleKeydown = this.handleKeydown.bind(this);
    this.handlePanelClick = this.handlePanelClick.bind(this);
    this.handleScroll = this.handleScroll.bind(this);
    this.lastScrollY = Math.max(0, window.scrollY);

    this.toggle.addEventListener("click", this.handleToggle);
    this.panel.addEventListener("click", this.handlePanelClick);
    document.addEventListener("keydown", this.handleKeydown);
    window.addEventListener("scroll", this.handleScroll, { passive: true });
    this.handleScroll();
  }

  handleToggle() {
    this.setOpen(this.element.dataset.open !== "true");
  }

  handleKeydown(event) {
    if (event.key === "Escape") {
      this.setOpen(false);
    }
  }

  handlePanelClick(event) {
    if (event.target.closest("a")) {
      this.setOpen(false);
    }
  }

  setOpen(isOpen) {
    const label = isOpen
      ? this.toggle.dataset.headerLabelClose
      : this.toggle.dataset.headerLabelOpen;

    this.element.dataset.open = String(isOpen);
    this.toggle.setAttribute("aria-expanded", String(isOpen));

    if (label) {
      this.toggle.setAttribute("aria-label", label);
    }

    if (isOpen) {
      document.body.classList.add("no-scroll");
      if (window.lenisInstance) {
        window.lenisInstance.stop();
      }
    } else {
      document.body.classList.remove("no-scroll");
      if (window.lenisInstance) {
        window.lenisInstance.start();
      }
    }
  }

  handleScroll() {
    if (this.element.dataset.open === "true") {
      return;
    }

    const currentScrollY = Math.max(0, window.scrollY);

    if (currentScrollY > 50) {
      this.element.classList.add("header--scrolled");
    } else {
      this.element.classList.remove("header--scrolled");
    }

    if (currentScrollY > this.lastScrollY && currentScrollY > 100) {
      this.element.classList.add("header--hidden");
    } else {
      this.element.classList.remove("header--hidden");
    }

    this.lastScrollY = currentScrollY;
  }
}

const header = document.querySelector("[data-header]");

if (header) {
  new Header(header);
}

export default Header;
