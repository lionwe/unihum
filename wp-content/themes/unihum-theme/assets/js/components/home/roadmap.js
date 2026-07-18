document.querySelectorAll("[data-roadmap]").forEach((roadmap) => {
  const tabs = Array.from(roadmap.querySelectorAll("[data-roadmap-tab]"));
  const panels = Array.from(roadmap.querySelectorAll("[data-roadmap-panel]"));
  const current = roadmap.querySelector("[data-roadmap-current]");
  const timeline = roadmap.querySelector(".roadmap__timeline");
  const stage = roadmap.querySelector("[data-roadmap-stage]");
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  let activeIndex = 0;
  let scrollFrame = null;

  if (tabs.length < 2 || tabs.length !== panels.length || !timeline || !stage) {
    return;
  }

  const isDesktop = () => window.matchMedia("(min-width: 993px)").matches;

  const activateTab = (nextIndex, shouldFocus = false, shouldScroll = false) => {
    activeIndex = Math.max(0, Math.min(nextIndex, tabs.length - 1));

    tabs.forEach((tab, index) => {
      const isActive = index === activeIndex;

      tab.classList.toggle("is-active", isActive);
      tab.setAttribute("aria-selected", String(isActive));
      tab.tabIndex = isActive ? 0 : -1;
      panels[index].classList.toggle("is-active", isActive);
      panels[index].classList.toggle("is-past", index < activeIndex);
      panels[index].classList.toggle("is-future", index > activeIndex);
      panels[index].setAttribute("aria-hidden", String(!isActive));
    });

    if (current) {
      current.textContent = String(activeIndex + 1).padStart(2, "0");
    }

    if (shouldFocus) {
      tabs[activeIndex].focus();
    }

    if (shouldScroll && isDesktop()) {
      const timelineTop = timeline.getBoundingClientRect().top + window.scrollY;
      const distance = Math.max(timeline.offsetHeight - window.innerHeight, 1);
      const target = timelineTop + (distance * activeIndex) / (tabs.length - 1);

      window.scrollTo({
        top: target,
        behavior: prefersReducedMotion.matches ? "auto" : "smooth",
      });
    }
  };

  tabs.forEach((tab, index) => {
    tab.addEventListener("click", () => activateTab(index, false, true));
    tab.addEventListener("keydown", (event) => {
      let nextIndex = null;

      if (event.key === "ArrowRight" || event.key === "ArrowDown") {
        nextIndex = (index + 1) % tabs.length;
      } else if (event.key === "ArrowLeft" || event.key === "ArrowUp") {
        nextIndex = (index - 1 + tabs.length) % tabs.length;
      } else if (event.key === "Home") {
        nextIndex = 0;
      } else if (event.key === "End") {
        nextIndex = tabs.length - 1;
      }

      if (nextIndex === null) {
        return;
      }

      event.preventDefault();
      activateTab(nextIndex, true, true);
    });
  });

  const updateFromScroll = () => {
    scrollFrame = null;

    if (!isDesktop()) {
      return;
    }

    const rect = timeline.getBoundingClientRect();
    const distance = Math.max(timeline.offsetHeight - window.innerHeight, 1);
    const progress = Math.min(Math.max(-rect.top / distance, 0), 1);
    const nextIndex = Math.round(progress * (tabs.length - 1));

    if (nextIndex !== activeIndex) {
      activateTab(nextIndex);
    }
  };

  const requestScrollUpdate = () => {
    if (scrollFrame === null) {
      scrollFrame = window.requestAnimationFrame(updateFromScroll);
    }
  };

  window.addEventListener("scroll", requestScrollUpdate, { passive: true });
  window.addEventListener("resize", requestScrollUpdate);
  activateTab(0);
  requestScrollUpdate();
});
