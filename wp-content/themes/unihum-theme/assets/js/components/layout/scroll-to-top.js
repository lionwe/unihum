class ScrollToTop {
  constructor(element) {
    this.element = element;
    this.handleScroll = this.handleScroll.bind(this);
    this.handleClick = this.handleClick.bind(this);

    window.addEventListener("scroll", this.handleScroll, { passive: true });
    this.element.addEventListener("click", this.handleClick);

    // Initial check
    this.handleScroll();
  }

  handleScroll() {
    if (window.scrollY > 300) {
      this.element.classList.add("is-visible");
    } else {
      this.element.classList.remove("is-visible");
    }
  }

  handleClick() {
    if (window.lenisInstance) {
      window.lenisInstance.scrollTo(0);
    } else {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    }
  }
}

const btn = document.querySelector("[data-scroll-top]");
if (btn) {
  new ScrollToTop(btn);
}

export default ScrollToTop;
