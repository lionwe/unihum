import IMask from "imask";

const inputsWithMask = document.querySelectorAll("input[data-mask]");

inputsWithMask.forEach((input) => {
  IMask(input, {
    mask: input.dataset.mask,
    prepare: (str, unmasked) => {
      if (str.replace(/(\D|\s)/, "").length >= 12) {
        return str.replace(/(\D|\s)/, "");
      }
      if (str && !unmasked._value) {
        return "380" + str;
      }
      return str;
    },
  });
});
