app.querySelector("form").addEventListener("submit", function(event) {
	event.preventDefault(); 


	fetch(event.target.getAttribute("action"), {
    method: "POST",
    body: new FormData(event.target),
  })
    .then(function (response) {
      if (response.ok) {
        return response.text();
      } else {
        throw new Error("Network response was not ok.");
      }
    })
    .then(function (data) {
			console.log("Дані успішно збережено");
			app.querySelector("form").reset();
			alert("Дані успішно збережено");
    })
    .catch(function (error) {
      console.error("There was a problem with the fetch operation:", error);
      alert("An error occurred while processing your request.");
    });
})