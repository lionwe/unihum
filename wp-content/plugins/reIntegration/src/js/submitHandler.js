import { Form } from "./validation.js";

const SUCCESS_MESSAGE_DURATION = 4000;
const forms = [...document.querySelectorAll(".reintegration-form form")].map(
  (form) => ({
    form,
    validation: new Form(form),
  })
);

forms.forEach(({ form, validation }) => {
  form.noValidate = true;
  form.addEventListener("submit", (event) =>
    handleFormSubmit(event, validation)
  );
});

async function handleFormSubmit(event, validation) {
  event.preventDefault();

  const form = event.currentTarget;
  if (form.dataset.submitting === "true" || !validation.validate()) {
    return;
  }

  const submitButton = form.querySelector("[type='submit']");
  const submitButtonText = submitButton?.querySelector(".btn__text") || submitButton;
  const initialButtonText = submitButtonText?.textContent || "";

  setSubmittingState(form, submitButton, submitButtonText, true);
  clearFormError(form);

  try {
    const response = await fetch(window.reintegration_ajax.ajax_url, {
      method: "POST",
      body: createRequestData(form),
      credentials: "same-origin",
    });
    const result = await response.json();

    if (!response.ok || !result.success) {
      validation.applyServerErrors(result.data?.errors);
      showFormError(
        form,
        result.data?.message ||
          window.reintegration_ajax.messages?.request_error ||
          "Не вдалося відправити форму. Спробуйте ще раз."
      );
      return;
    }

    validation.clearErrors();
    form.reset();
    showSuccessMessage(form);

    document.dispatchEvent(
      new CustomEvent("reintegrationFormSubmitted", {
        detail: {
          form,
          data: result,
        },
      })
    );
  } catch (error) {
    console.error("Error submitting form:", error);
    showFormError(
      form,
      window.reintegration_ajax.messages?.request_error ||
        "Не вдалося відправити форму. Спробуйте ще раз."
    );
  } finally {
    setSubmittingState(
      form,
      submitButton,
      submitButtonText,
      false,
      initialButtonText
    );
  }
}

function createRequestData(form) {
  const formObject = Object.fromEntries(new FormData(form).entries());
  const urlParameters = new URLSearchParams(window.location.search);
  const utmParameters = [
    "utm_source",
    "utm_medium",
    "utm_campaign",
    "utm_term",
    "utm_content",
  ];

  utmParameters.forEach((parameter) => {
    const currentValue = urlParameters.get(parameter);
    if (currentValue) {
      formObject[parameter] = currentValue;
      localStorage.setItem(parameter, currentValue);
      return;
    }

    const storedValue = localStorage.getItem(parameter);
    if (storedValue) {
      formObject[parameter] = storedValue;
    }
  });

  const requestData = new FormData();
  requestData.append("form_data", JSON.stringify(formObject));
  requestData.append("action", "reintegration_send_form");
  requestData.append("nonce", window.reintegration_ajax.nonce);
  return requestData;
}

function setSubmittingState(
  form,
  button,
  buttonText,
  isSubmitting,
  initialText = ""
) {
  form.dataset.submitting = String(isSubmitting);

  if (!button) {
    return;
  }

  button.disabled = isSubmitting;
  button.setAttribute("aria-busy", String(isSubmitting));

  if (buttonText) {
    buttonText.textContent = isSubmitting
      ? window.reintegration_ajax.messages?.sending || "Відправка..."
      : initialText;
  }
}

function showFormError(form, message) {
  let error = form.querySelector(".reintegration-form__form-error");
  if (!error) {
    error = document.createElement("div");
    error.className = "reintegration-form__form-error";
    error.setAttribute("role", "alert");
    form.append(error);
  }
  error.textContent = message;
}

function clearFormError(form) {
  form.querySelector(".reintegration-form__form-error")?.remove();
}

function showSuccessMessage(form) {
  const wrapper = form.closest(".reintegration-form");
  if (!wrapper) {
    return;
  }

  const success = document.createElement("div");
  success.className = "reintegration-form__success";
  success.setAttribute("role", "status");
  success.setAttribute("aria-live", "polite");
  success.innerHTML =
    window.reintegration_ajax.success_message ||
    "<p>Дякуємо! Форму успішно відправлено.</p>";

  form.hidden = true;
  form.style.display = "none";
  wrapper.append(success);

  window.setTimeout(() => {
    success.remove();
    form.hidden = false;
    form.style.display = "";
  }, window.reintegration_ajax.success_duration || SUCCESS_MESSAGE_DURATION);
}
