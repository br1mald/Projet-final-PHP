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
      count = await apiGet(`categories.php?action=number_of_articles&id=${category.id}`);
    } catch (e) { /* leave count as 0 if the call fails */ }

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
        <a href="${window.APP_BASE || ''}/categories/modifier.php?id=${category.id}" class="btn btn-secondary btn-sm">Modifier</a>
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
        () => (window.location.href = (window.APP_BASE || "") + "/categories/liste.php"),
        1000,
      );
    } catch (err) {
      showFormErrors(postForm, { server: err.message || "Erreur serveur" });
    }
  });
}

// supprimer.php — build a styled card per category with a delete button
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

if (window.location.pathname.includes("/categories/liste.php")) {
  getAllCategories();
}
if (window.location.pathname.includes("/categories/ajouter.php"))
  submitPostForm();
if (window.location.pathname.includes("/categories/supprimer.php"))
  loadCategoriesForDeletion();

