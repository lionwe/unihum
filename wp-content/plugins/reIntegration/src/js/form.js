let timeout = null;
document.querySelector("form").addEventListener("submit", function (event) {
  event.preventDefault();

  const formData = new FormData(event.target);

  const url = new URL(window.location.href);
  const action = url.searchParams.get("id")
    ? "reintegration_save_form"
    : "reintegration_create_form";

  formData.append("action", action);
  formData.append("nonce", reintegration_ajax.nonce);
  formData.append("id", url.searchParams.get("id") || "");

  const button = event.target.querySelector("[type='submit']");
  let buttonText = button ? button.value : "";
  if (button) {
    button.setAttribute("disabled", "disabled");
    button.value = "Збереження...";
  }

  fetch(reintegration_ajax.ajax_url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        if (button) {
          button.value = "Збережено";
          buttonText = "Зберегти";
        }

        if (action === "reintegration_create_form") {
          url.searchParams.set("id", data.data.id);
          history.pushState(null, "", url.href);
        }
      } else {
        console.error("Error:", data);
        if (button) {
          button.value = "Помилка";
        }
      }
      if (button) {
        button.removeAttribute("disabled");
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          button.value = buttonText;
        }, 2000);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while submitting the form.");
    });
});
