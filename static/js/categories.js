import { showFormErrors } from "./validation.js";
import { apiGet, apiPost, apiDelete } from "./api.js";

const categoriesContainer = document.querySelector(".categories-container");
const postForm = document.querySelector("form[action='../api/categories.php']");
const deleteFormContainer = document.querySelector(".delete-form-container");

console.log("Hello"); // debugging

async function getCategory(id) {
  const data = await apiGet(`categories.php?action=search&id=${id}`);
  return data;
}

async function getAllCategories() {
  const data = await apiGet(`categories.php?action=all`);
  data.forEach((category) => {
    const li = document.createElement("li");

    li.className = "category-name";
    li.textContent = `${category.nom}`;

    categoriesContainer.appendChild(li);
    console.log(`added category: ${category.nom}`);
  });
}

function validatePayload(payload, method) {
  const errors = {};
  if (method === "post") {
    if (!payload.nom || payload.nom.trim().length < 3) {
      errors.titre = "Le nom doit contenir au moins 3 caractères";
    }
  } else if (method === "delete") {
    if (!payload.categoryId || isNaN(Number(payload.categoryId))) {
      errors.categoryId = "Veuillez choisir une catégorie valide.";
    }
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
        () => (window.location.href = "/final_project/categories/liste.php"),
        1000,
      );
    } catch (err) {
      console.error(err);
      showFormErrors(postForm, { server: err.message || "Erreur serveur" });
    }
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

if (window.location.pathname.includes("/categories/liste.php"))
  getAllCategories();

if (window.location.pathname.includes("/categories/ajouter.php"))
  submitPostForm();
if (window.location.pathname.includes("/categories/supprimer.php"))
  populateDeleteForm();
