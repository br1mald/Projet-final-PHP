import { showFormErrors, escapeHTML } from "./validation.js";
import { apiGet, apiPost, apiDelete } from "./api.js";

const appBase = "/final_project"; // changer selon la structure du serveur

const articlesContainer = document.querySelector(".articles-container");
const formSelectField = document.querySelector(".form-select-field");
const postForm = document.querySelector("form[action='../api/articles.php']");
const deleteFormContainer = document.querySelector(".delete-form-container");

console.log("Hello"); // debugging, file wasn't loading

async function getArticle(id) {
  const data = await apiGet(`articles.php?action=search&id=${id}`);
  return data;
}

async function renderArticleDetails(id) {
  const data = await getArticle(id);
  const articleContainer = document.querySelector(".article-details");
  articleContainer.innerHTML = `<h1>${escapeHTML(data.titre)}</h1> <br>
    Contenu: ${escapeHTML(data.contenu)} <br>
    Catégorie: ${escapeHTML(data.cat_nom)} <br>
    Auteur: ${escapeHTML(data.util_nom)} <br>
    Date de publication: ${escapeHTML(data.date_publication)}`;
  console.log("rendered"); // testing
}

async function getLatestArticles() {
  const data = await apiGet(`articles.php?action=latest`);
  articlesContainer.innerHTML = "";
  console.log("Emptied container"); // debugging
  data.forEach((article) => {
    const li = document.createElement("li");

    const a = document.createElement("a");
    a.className = "article-link";
    a.href = `./articles/detail.php?id=${article.id}`;
    a.textContent = article.titre;

    const p = document.createElement("p");
    p.textContent = article.description;

    li.appendChild(a);
    li.appendChild(p);

    articlesContainer.appendChild(li);
  });
}

async function getAllArticles() {
  const data = await apiGet("articles.php?action=all");
  articlesContainer.innerHTML = "";
  console.log("Emptied container"); // debugging
  data.forEach((article) => {
    const li = document.createElement("li");

    const a = document.createElement("a");
    a.className = "article-link";
    a.href = `./articles/detail.php?id=${article.id}`;
    a.textContent = article.titre;

    const p = document.createElement("p");
    p.textContent = article.description;

    li.appendChild(a);
    li.appendChild(p);

    articlesContainer.appendChild(li);
  });
  console.log("All articles rendered"); // testing
}

async function populateSelectForm() {
  const data = await apiGet("categories.php?action=all");
  data.forEach((category) => {
    const option = document.createElement("option");
    option.textContent = category.nom;
    option.value = category.id;
    formSelectField.appendChild(option);
  });
}

function validatePayload(payload, method) {
  const errors = {};
  if (method === "post") {
    if (!payload.titre || payload.titre.trim().length < 3) {
      errors.titre = "Le titre doit contenir au moins 3 caractères";
    }
    if (!payload.description || payload.description.trim().length < 10) {
      errors.description =
        "La description courte doit contenir au moins 10 caractères.";
    }
    if (!payload.contenu || payload.contenu.trim().length < 20) {
      errors.contenu = "Le contenu doit contenir au moins 20 caractères.";
    }
    if (!payload.categorie_id || isNaN(Number(payload.categorie_id))) {
      errors.categorie_id = "Veuillez choisir une catégorie valide.";
    }
  } else if (method === "delete") {
    if (!payload.articleId || isNaN(Number(payload.articleId))) {
      errors.articleId = "Veuillez choisir un article valide.";
    }
  }

  return errors;
}

function submitPostForm() {
  postForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const payload = {
      titre: postForm.title.value,
      description: postForm.description.value,
      contenu: postForm.content.value,
      categorie_id: postForm.category.value,
      date_publication: postForm.date.value,
    };

    const errors = validatePayload(payload, "post");
    showFormErrors(postForm, errors);

    if (Object.keys(errors).length > 0) return;

    try {
      const res = await apiPost("articles.php", payload);
      console.log("created", res);
      showFormErrors(postForm, { success: "Article ajouté avec succès" });
      setTimeout(
        () => (window.location.href = "/final_project/accueil.php"),
        1000,
      );
    } catch (err) {
      console.error(err);
      showFormErrors(postForm, { server: err.message || "Erreur serveur" });
    }
  });
}

async function populateDeleteForm() {
  const data = await apiGet("articles.php?action=all");
  data.forEach((article) => {
    const form = document.createElement("form");
    form.className = "delete-form";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "article";
    input.value = article.id;
    const submitButton = document.createElement("button");
    submitButton.type = "submit";
    submitButton.textContent = article.titre;

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
        articleId: deleteForm.article.value,
      };

      const errors = validatePayload(payload, "delete");
      showFormErrors(deleteForm, errors);

      if (Object.keys(errors).length > 0) return;

      try {
        const res = await apiDelete(`articles.php`, payload);
        console.log("deleted", res);
        showFormErrors(deleteForm, { success: "Article supprimé avec succès" });

        setTimeout(() => deleteForm.remove(), 1500);
      } catch (err) {
        console.error(err);
        showFormErrors(deleteForm, { server: err.message || "Erreur serveur" });
      }
    });
  });
}

console.log(window.location.pathname);
if (window.location.pathname.includes("accueil.php")) getLatestArticles();
// if (window.location.pathname.includes("accueil.php")) getAllArticles(); testing

if (window.location.pathname.includes("detail.php")) {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  if (id) renderArticleDetails(id);
}

if (window.location.pathname.includes("/articles/ajouter.php")) {
  const currentTime = new Date();
  const dateField = document.querySelector(".current-date");
  dateField.value = currentTime.toISOString().slice(0, 19).replace("T", " ");
  populateSelectForm();
  submitPostForm();
}

if (window.location.pathname.includes("/articles/supprimer.php")) {
  populateDeleteForm();
}
