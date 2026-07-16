const EMOJI_PATTERN = /[\p{Extended_Pictographic}\p{Regional_Indicator}\u{FE0F}\u{20E3}]/u;
const DEFAULT_MAX_LENGTH = 255;
const TEXTAREA_MAX_LENGTH = 2000;
const PHONE_MAX_LENGTH = 32;
const DEFAULT_PHONE_MIN_DIGITS = 12;

let errorId = 0;

export class Form {
  constructor(form) {
    this.form = form;
    this.fields = [...form.querySelectorAll("input, textarea, select")];
    this.messages = window.reintegration_ajax?.messages || {};
    this.init();
  }

  init() {
    this.fields.forEach((field) => {
      field.addEventListener("input", () => {
        if (field.getAttribute("aria-invalid") === "true") {
          this.validateField(field);
        }
      });

      field.addEventListener("blur", () => {
        this.validateField(field);
      });
    });
  }

  validate() {
    let isValid = true;
    let firstInvalidField = null;

    this.fields.forEach((field) => {
      if (!this.validateField(field)) {
        isValid = false;
        firstInvalidField ||= field;
      }
    });

    firstInvalidField?.focus();
    return isValid;
  }

  validateField(field) {
    const error = this.getFieldError(field);

    if (error) {
      this.showFieldError(field, error);
      return false;
    }

    this.clearFieldError(field);
    return true;
  }

  getFieldError(field) {
    if (field.disabled || field.type === "hidden") {
      return "";
    }

    const value = String(field.value || "").trim();

    if (field.required && !this.hasRequiredValue(field, value)) {
      return this.messages.required || "Заповніть це поле.";
    }

    if (value === "") {
      return "";
    }

    if (EMOJI_PATTERN.test(value)) {
      return this.messages.emoji || "Смайлики використовувати не можна.";
    }

    const maxLength = this.getMaxLength(field);
    if (value.length > maxLength) {
      const message =
        this.messages.too_long || "Максимальна довжина — %d символів.";
      return message.replace("%d", String(maxLength));
    }

    if (field.type === "tel") {
      const minDigits = Number.parseInt(
        field.dataset.minLength || String(DEFAULT_PHONE_MIN_DIGITS),
        10
      );
      const digits = value.replace(/\D/g, "");

      if (digits.length < minDigits) {
        const message =
          this.messages.phone ||
          "Введіть коректний номер телефону — мінімум %d цифр.";
        return message.replace("%d", String(minDigits));
      }
    }

    if (field.validity?.typeMismatch || field.validity?.patternMismatch) {
      return this.messages.invalid || "Перевірте правильність значення.";
    }

    return "";
  }

  hasRequiredValue(field, value) {
    if (field.type === "checkbox" || field.type === "radio") {
      return field.checked;
    }

    return value !== "";
  }

  getMaxLength(field) {
    const configuredLength = Number.parseInt(
      field.dataset.maxLength || field.getAttribute("maxlength") || "",
      10
    );

    if (Number.isFinite(configuredLength) && configuredLength > 0) {
      return configuredLength;
    }

    if (field.type === "tel") {
      return PHONE_MAX_LENGTH;
    }

    if (field.tagName === "TEXTAREA") {
      return TEXTAREA_MAX_LENGTH;
    }

    return DEFAULT_MAX_LENGTH;
  }

  showFieldError(field, message) {
    let error = this.getFieldErrorElement(field);

    if (!error) {
      error = document.createElement("span");
      error.className = "reintegration-form__field-error";
      error.id = `reintegration-field-error-${++errorId}`;
      error.setAttribute("role", "alert");
      field.insertAdjacentElement("afterend", error);
    }

    error.textContent = message;
    field.setAttribute("aria-invalid", "true");
    field.setAttribute("aria-describedby", error.id);
  }

  clearFieldError(field) {
    const error = this.getFieldErrorElement(field);
    const describedBy = field.getAttribute("aria-describedby");

    if (error) {
      error.remove();
    }

    field.removeAttribute("aria-invalid");

    if (describedBy?.startsWith("reintegration-field-error-")) {
      field.removeAttribute("aria-describedby");
    }
  }

  getFieldErrorElement(field) {
    const nextElement = field.nextElementSibling;
    return nextElement?.classList.contains("reintegration-form__field-error")
      ? nextElement
      : null;
  }

  applyServerErrors(errors = {}) {
    Object.entries(errors).forEach(([name, message]) => {
      const field = this.fields.find((item) => item.name === name);
      if (field) {
        this.showFieldError(field, String(message));
      }
    });
  }

  clearErrors() {
    this.fields.forEach((field) => this.clearFieldError(field));
  }
}
