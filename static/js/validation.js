export function showFormErrors(form, errors) {
  let errorsContainer = form.querySelector(".form-errors");
  if (!errorsContainer) {
    errorsContainer = document.createElement("div");
    errorsContainer.className = "form-errors";
    form.prepend(errorsContainer);
  }
  errorsContainer.innerHTML = "";
  if (!errors || Object.keys(errors).length === 0) return;
  for (const [field, msg] of Object.entries(errors)) {
    const errorDiv = document.createElement("div");
    errorDiv.className = "error";
    errorDiv.textContent = `${field}: ${msg}`;
    errorsContainer.appendChild(errorDiv);
  }
}

export function escapeHTML(str) {
  if (typeof str !== "string") return str;
  return str.replace(
    /[&<>'"]/g,
    (tag) =>
      ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        "'": "&#39;",
        '"': "&quot;",
      })[tag] || tag,
  );
}
