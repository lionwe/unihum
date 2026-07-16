document.querySelectorAll(".integration-icon a").forEach((el) => {
  el.addEventListener("click", clickHandler);
});

window.addEventListener("click", (e) => {
  if (e.target.closest(".integration-page [type='submit']")) {
    saveHandler(e);
  }
});

function saveHandler(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append(
    "options",
    JSON.stringify(Object.fromEntries(new FormData(e.target.closest("form"))))
  );
  const searchParams = new URLSearchParams(window.location.search);
  formData.append("slug", searchParams.get("integration_name"));
  formData.append("action", "reintegration_save_settings");
  formData.append("nonce", reintegration_ajax.nonce);
  fetch(reintegration_ajax.ajax_url, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
    });
}

function clickHandler(e) {
  e.preventDefault();
  const link = new URL(e.target.closest("a").href);
	const pageName = link.searchParams.get("integration_name");
	if (!pageName) {
		window.open(link.href, "_black", "width=600 ,height=700");
    return;
  }
  const data = new FormData();
  data.append("action", "reintegration_get_settings_page");
  data.append("integration_name", pageName);
  data.append("nonce", reintegration_ajax.nonce);
  fetch(reintegration_ajax.ajax_url, {
    method: "POST",
    body: data,
  })
    .then((response) => response.json())
    .then((data) => {
      document.querySelector(".integration-page").innerHTML = data.data.content;
      // Update the URL without reloading the page
      const newUrl = new URL(window.location);
      newUrl.searchParams.set("integration_name", pageName);
      window.history.pushState({}, "", newUrl);
    });
}
