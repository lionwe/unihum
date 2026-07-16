// import Lenis from "lenis";
// import { gsap } from "gsap";
// import { ScrollTrigger } from "gsap/ScrollTrigger";

// gsap.registerPlugin(ScrollTrigger);

// class SmoothScroll {
//   constructor() {
//     this.init();
//   }

//   init() {
//     this.lenis = new Lenis({
//       lerp: 0.1, // Smoothness level (0.1 is responsive yet smooth)
//       wheelMultiplier: 1,
//       smoothWheel: true,
//       syncTouch: false,
//     });

//     // Synchronize ScrollTrigger with Lenis
//     this.lenis.on("scroll", ScrollTrigger.update);

//     gsap.ticker.add((time) => {
//       this.lenis.raf(time * 1000);
//     });

//     gsap.ticker.lagSmoothing(0);

//     // Expose lenis globally for control from other scripts
//     window.lenisInstance = this.lenis;

//     // Anchor link scroll integration
//     document.addEventListener("click", (e) => {
//       const target = e.target.closest('a[href^="#"]');
//       if (!target) return;

//       const href = target.getAttribute("href");
//       if (href === "#") return;

//       try {
//         const targetElement = document.querySelector(href);
//         if (targetElement) {
//           e.preventDefault();
//           this.lenis.scrollTo(targetElement, {
//             offset: -80,
//             duration: 1.2,
//           });
//         }
//       } catch (err) {
//         // Handle invalid selectors in href if any
//       }
//     });
//   }
// }

// new SmoothScroll();
