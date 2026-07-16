document.querySelectorAll(".wrap table button").forEach((button) => {
  button.addEventListener("click", handleDelete);
});
function handleDelete(event) {
  event.preventDefault();
  const button = event.target;
  const data = new FormData();
  data.append("action", "reintegration_delete_lead");
  data.append("id", button.id);
  data.append("nonce", reintegration_ajax.nonce);

  fetch(reintegration_ajax.ajax_url, {
    method: "POST",
    body: data,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        button.closest("tr").remove();
      } else {
        console.error("Error deleting lead:", data);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}
