import { defineConfig } from "vite";

export default defineConfig({
  build: {
    lib: {
      entry: "src/js/index.js", // твій головний файл
      formats: ["iife"], // формат для браузера
      name: "ReintegrationForm",
      fileName: () => "script.js",
    },
    outDir: "dist", // вихідна папка
    emptyOutDir: true,
  },
});
