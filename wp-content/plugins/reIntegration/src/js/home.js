document.querySelectorAll("a.button-link-delete")?.forEach((button) => {
  button.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent the default link behavior
    const url = new URL(event.target.href); // Get the URL from the link
    if (!confirm("Are you sure you want to delete this form?")) return; // Confirm deletion

    fetch(reintegration_ajax.ajax_url, {
      method: "POST", // Use POST method
      body: new URLSearchParams({
        action: "reintegration_delete_form", // Action for deletion
        id: url.searchParams.get("id"), // Extract ID from the URL
      }), // Convert the data to URL-encoded format
      headers: {
        "Content-Type": "application/x-www-form-urlencoded", // Set the content type
        "X-Requested-With": "XMLHttpRequest", // Indicate that this is an AJAX request
      },
    })
      .then((response) => response.json()) // Parse the JSON response
      .then((data) => {
        button.closest("tr")?.remove(); // Remove the row from the table
        if (
          document.querySelector(".wrap table tbody").innerHTML.trim() === ""
        ) {
          document.querySelector(".wrap table")?.remove(); // Remove the table if no rows left
          document.querySelector(".wrap").innerHTML +=
            "<p>Форм ще не створено.</p>"; // Show a message if no forms are found
        }
      })
      .catch((error) => {
        console.error("Error:", error); // Log any errors
        alert("An error occurred while deleting the form.");
      });
  });
});
