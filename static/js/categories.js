import { showFormErrors, escapeHTML } from "./validation.js";
import { apiGet, apiPost, apiPatch, apiDelete } from "./api.js";

const categoriesContainer = document.querySelector(".categories-container");
const postForm = document.querySelector("form[action='../api/categories.php']");
const patchFormContainer = document.querySelector(".patch-form-container");
const deleteFormContainer = document.querySelector(".delete-form-container");

console.log("Hello"); // debugging

async function getCategory(id) {
  const data = await apiGet(`categories.php?action=search&id=${id}`);
  return data;
}

async function getAllCategories() {
  const data = await apiGet(`categories.php?action=all`);

  // Create a grid wrapper — the CSS class "categories-grid" turns this into a responsive card grid
  const grid = document.createElement("div");
  grid.className = "categories-grid";

  // Use for...of so we can await the article-count call inside the loop
  for (const category of data) {
    // Fetch how many articles belong to this category
    let count = 0;
    try {
      count = await apiGet(`categories.php?action=number_of_articles&id=${category.id}`);
    } catch (e) { /* leave count as 0 if the call fails */ }

    // Build a card for this category
    const card = document.createElement("div");
    card.className = "category-card";
    card.innerHTML = `
      <h3 class="category-name">${escapeHTML(category.nom)}</h3>
      <div class="category-stats">
        <div class="stat-item">
          <span class="stat-number">${count}</span>
          <span class="stat-label"> articles</span>
        </div>
      </div>
    `;
    grid.appendChild(card);
  }

  categoriesContainer.appendChild(grid);
}

function validatePayload(payload, method) {
  const errors = {};
  if (method === "post") {
    if (!payload.nom || payload.nom.trim().length < 3) {
      errors.nom = "Le nom doit contenir au moins 3 caractères";
    }
  } else if (method === "delete") {
    if (!payload.categoryId || isNaN(Number(payload.categoryId))) {
      errors.categoryId = "Veuillez choisir une catégorie valide.";
    }
  } else if (method === "patch") {
    if (payload.id && payload.value) {
      if (isNaN(Number(payload.id))) errors["id"] = "Id invalide";
      if (payload.value.trim().length < 3)
        errors.nom = "Le nom doit contenir au moins 3 caractères";
    } else errors.payload = "Aucune valeur reçue.";
  }

  return errors;
}

function submitPostForm() {
  postForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const payload = {
      nom: postForm.nom.value,
    };

    const errors = validatePayload(payload, "post");
    showFormErrors(postForm, errors);

    if (Object.keys(errors).length > 0) return;

    try {
      const res = await apiPost("categories.php", payload);
      console.log("created", res);
      showFormErrors(postForm, { success: "Catégorie ajoutée avec succès" });
      setTimeout(
        () => (window.location.href = (window.APP_BASE || "") + "/categories/liste.php"),
        1000,
      );
    } catch (err) {
      console.error(err);
      showFormErrors(postForm, { server: err.message || "Erreur serveur" });
    }
  });
}

async function populatePatchForm() {
  const data = await apiGet("categories.php?action=all");

  data.forEach((category) => {
    console.log("found category");

    const form = document.createElement("form");
    form.className = "patch-form";

    const valueInput = document.createElement("input");
    valueInput.type = "text";
    valueInput.value = category.nom;
    valueInput.name = "nom";

    const idInput = document.createElement("input");
    idInput.type = "hidden";
    idInput.value = category.id;
    idInput.name = "categoryId";

    form.appendChild(valueInput);
    form.appendChild(idInput);

    patchFormContainer.appendChild(form);

    const br = document.createElement("br");
    patchFormContainer.appendChild(br);
  });
  submitPatchForm();
}

function submitPatchForm() {
  const patchForms = document.querySelectorAll(".patch-form");
  patchForms.forEach((patchForm) => {
    patchForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const payload = {
        id: patchForm.categoryId.value,
        value: patchForm.nom.value,
      };

      const errors = validatePayload(payload, "patch");
      showFormErrors(patchForm, errors);

      if (Object.keys(errors).length > 0) return;

      try {
        const res = await apiPatch("categories.php", payload);
        console.log("Success", res);
        showFormErrors(patchForm, {
          success: "Catégorie modifiée avec succès.",
        });
      } catch (err) {
        console.error(err);
        showFormErrors(patchForm, { server: err.message || "Erreur serveur" });
      }
    });
  });
}

async function populateDeleteForm() {
  const data = await apiGet("categories.php?action=all");
  data.forEach((category) => {
    const form = document.createElement("form");
    form.className = "delete-form";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "category";
    input.value = category.id;
    const submitButton = document.createElement("button");
    submitButton.type = "submit";
    submitButton.textContent = category.nom;

    form.appendChild(input);
    form.appendChild(submitButton);

    deleteFormContainer.appendChild(form);
  });
  submitDeleteForm();
}

function submitDeleteForm() {
  const deleteForms = document.querySelectorAll(".delete-form"); // might want to do this for the post form too.
  deleteForms.forEach((deleteForm) => {
    deleteForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const payload = {
        categoryId: deleteForm.category.value,
      };

      const errors = validatePayload(payload, "delete");
      showFormErrors(deleteForm, errors);

      if (Object.keys(errors).length > 0) return;

      try {
        const res = await apiDelete(`categories.php`, payload);
        console.log("deleted", res);
        showFormErrors(deleteForm, {
          success: "Catégorie supprimé avec succès",
        });

        setTimeout(() => deleteForm.remove(), 1500);
      } catch (err) {
        console.error(err);
        showFormErrors(deleteForm, { server: err.message || "Erreur serveur" });
      }
    });
  });
}

if (window.location.pathname.includes("/categories/liste.php")) {
  getAllCategories();
}
if (window.location.pathname.includes("/categories/ajouter.php"))
  submitPostForm();
if (window.location.pathname.includes("/categories/supprimer.php"))
  populateDeleteForm();
if (window.location.pathname.includes("/categories/modifier.php"))
  populatePatchForm();
