import { showFormErrors, escapeHTML } from "./validation.js";
import { apiGet, apiPost, apiPatch, apiDelete } from "./api.js";

const categoriesContainer = document.querySelector(".categories-container");
const postForm = document.querySelector("form[action='../api/categories.php']");

async function getCategory(id) {
  const data = await apiGet(`categories.php?action=search&id=${id}`);
  return data;
}

async function getAllCategories() {
  const data = await apiGet(`categories.php?action=all`);

  const grid = document.createElement("div");
  grid.className = "categories-grid";

  for (const category of data) {
    let count = 0;
    try {
      count = await apiGet(
        `categories.php?action=number_of_articles&id=${category.id}`,
      );
    } catch (e) {
      /* si l'appel échoue count reste à 0 */
    }

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
      <div class="card-actions">
        <a href="${window.APP_BASE || ""}/categories/modifier.php?id=${category.id}" class="btn btn-secondary btn-sm">Modifier</a>
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
  }
  return errors;
}

function submitPostForm() {
  postForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const payload = { nom: postForm.nom.value };

    const errors = validatePayload(payload, "post");
    showFormErrors(postForm, errors);
    if (Object.keys(errors).length > 0) return;

    try {
      const res = await apiPost("categories.php", payload);
      showFormErrors(postForm, { success: "Catégorie ajoutée avec succès" });
      setTimeout(
        () =>
          (window.location.href =
            (window.APP_BASE || "") + "/categories/liste.php"),
        1000,
      );
    } catch (err) {
      showFormErrors(postForm, { server: err.message || "Erreur serveur" });
    }
  });
}

async function loadCategoriesForDeletion() {
  const list = document.getElementById("deleteCategoriesList");
  const loading = document.getElementById("loading");
  const errorMessage = document.getElementById("errorMessage");

  try {
    const data = await apiGet("categories.php?action=all");
    loading.style.display = "none";

    data.forEach((category) => {
      const card = document.createElement("div");
      card.className = "delete-category-card";

      const info = document.createElement("div");
      info.className = "category-info";
      const name = document.createElement("h4");
      name.className = "category-name";
      name.textContent = category.nom;
      info.appendChild(name);

      const btn = document.createElement("button");
      btn.className = "btn btn-danger btn-sm";
      btn.textContent = "Supprimer";
      btn.addEventListener("click", async () => {
        if (!confirm(`Supprimer la catégorie "${category.nom}" ?`)) return;
        try {
          await apiDelete("categories.php", { categoryId: category.id });
          card.remove();
        } catch (err) {
          alert("Erreur : " + err.message);
        }
      });

      card.appendChild(info);
      card.appendChild(btn);
      list.appendChild(card);
    });
  } catch (err) {
    loading.style.display = "none";
    errorMessage.style.display = "block";
  }
}

function handleEditCategoryForm() {
  const form = document.getElementById("editCategoryForm");
  const nomInput = document.getElementById("nom");
  const alertMsg = document.getElementById("alertMsg");
  const categoryIdEl = document.getElementById("categoryId");
  if (!form || !nomInput || !alertMsg || !categoryIdEl) return;

  const categoryId = categoryIdEl.value;

  function showAlert(message, isError) {
    alertMsg.textContent = message;
    alertMsg.className = isError ? "error-message" : "help-text";
    alertMsg.style.display = "block";
  }

  async function loadCategory() {
    try {
      const data = await apiGet(
        `categories.php?action=search&id=${categoryId}`,
      );
      nomInput.value = data.nom || "";
    } catch (err) {
      showAlert("Impossible de charger la catégorie : " + err.message, true);
    }
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const nom = nomInput.value.trim();
    if (nom.length < 3) {
      showAlert("Le nom doit contenir au moins 3 caractères.", true);
      return;
    }
    try {
      await apiPatch("categories.php", { id: categoryId, value: nom });
      showAlert("Catégorie modifiée avec succès !", false);
      setTimeout(
        () =>
          (window.location.href =
            (window.APP_BASE || "") + "/categories/liste.php"),
        1200,
      );
    } catch (err) {
      showAlert("Erreur : " + err.message, true);
    }
  });

  loadCategory();
}

if (window.location.pathname.includes("/categories/liste.php")) {
  getAllCategories();
}
if (window.location.pathname.includes("/categories/ajouter.php"))
  submitPostForm();
if (window.location.pathname.includes("/categories/supprimer.php"))
  loadCategoriesForDeletion();
if (window.location.pathname.includes("/categories/modifier.php"))
  handleEditCategoryForm();
