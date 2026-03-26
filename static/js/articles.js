import { showFormErrors, escapeHTML } from "./validation.js";
import { apiGet, apiPost, apiPatch, apiDelete } from "./api.js";

const appBase = window.APP_BASE || "/final_project";

const formSelectField = document.querySelector(".form-select-field");
const postForm = document.querySelector(".post-form");
const editForm = document.querySelector(".edit-article-form");
const deleteFormContainer = document.querySelector(".delete-form-container");

function formatDateFr(dateStr) {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  const mois = [
    "janvier",
    "février",
    "mars",
    "avril",
    "mai",
    "juin",
    "juillet",
    "août",
    "septembre",
    "octobre",
    "novembre",
    "décembre",
  ];
  return d.getDate() + " " + mois[d.getMonth()] + " " + d.getFullYear();
}

async function getArticle(id) {
  const data = await apiGet(`articles.php?action=search_by_id&id=${id}`);
  return data;
}

async function renderArticleDetails(id) {
  const data = await getArticle(id);
  const articleContainer = document.querySelector(".article-details");
  if (!articleContainer) return;

  const img =
    window.IMG_DEFAULT ||
    "https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80";
  if (data.image) img = `${appBase}/${data.image}`;

  const categorie = data.categorie || "";
  const auteur = data.auteur || "";
  const paragraphes = (data.contenu || "")
    .split("\n\n")
    .filter((p) => p.trim());
  const contentHtml =
    paragraphes.length > 0
      ? paragraphes.map((p) => `<p>${escapeHTML(p.trim())}</p>`).join("")
      : `<p>${escapeHTML(data.contenu || "")}</p>`;

  let editBtns = "";
  if (window.USER_CAN_EDIT) {
    editBtns = `<div style="display:flex; gap:0.8rem; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-light);">
      <a href="${appBase}/articles/modifier.php?id=${data.id}" class="btn btn-primary btn-sm">Modifier</a>
      <a href="${appBase}/articles/supprimer.php?id=${data.id}" class="btn btn-danger btn-sm">Supprimer</a>
    </div>`;
  }

  articleContainer.innerHTML = `
    <article class="article-detail">
      <div class="cat-badge" style="margin-bottom:0.8rem;">${escapeHTML(categorie)}</div>
      <h1 class="article-title">${escapeHTML(data.titre)}</h1>
      <div class="article-meta">
        <span>${escapeHTML(auteur)}</span>
        <span>${formatDateFr(data.date_publication)}</span>
      </div>
      <hr style="border:none; border-top:1px solid var(--border-light); margin-bottom:1.5rem;">
      <img src="${img}" alt="${escapeHTML(data.titre)}" style="width:100%; height:380px; object-fit:cover; margin-bottom:1.5rem;">
      <div class="article-content">${contentHtml}</div>
      <div style="margin-top:2rem; padding-top:1rem; border-top:1px solid var(--border-light);">
        <a href="${appBase}/accueil.php" class="btn btn-secondary btn-sm">← Retour à l'accueil</a>
        ${editBtns}
      </div>
    </article>`;

  // Populate "À lire aussi" sidebar with latest articles
  const similairesContainer = document.getElementById("article-similaires");
  if (similairesContainer) {
    try {
      const allArticles = await apiGet("articles.php?action=all");
      const others = Array.isArray(allArticles)
        ? allArticles.filter((a) => a.id != id).slice(0, 5)
        : [];
      if (others.length === 0) {
        similairesContainer.innerHTML =
          '<div style="color:var(--muted);font-size:.85rem;">Aucun autre article.</div>';
      } else {
        const imgDefault =
          window.IMG_DEFAULT ||
          "https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80";
        similairesContainer.innerHTML = others
          .map((a) => {
            const time = a.date_publication
              ? new Date(a.date_publication).toLocaleTimeString("fr-FR", {
                  hour: "2-digit",
                  minute: "2-digit",
                })
              : "";
            return `<div class="sidebar-item">
            <div class="sidebar-time">${time}</div>
            <div class="sidebar-item-title"><a href="${appBase}/articles/detail.php?id=${a.id}">${escapeHTML(a.titre)}</a></div>
            <div class="sidebar-cat">${escapeHTML((a.description || "").substring(0, 100))}${(a.description || "").length > 100 ? "…" : ""}</div>
          </div>`;
          })
          .join("");
      }
    } catch (e) {
      similairesContainer.innerHTML = "";
    }
  }
}

async function getLatestArticles() {
  const data = await apiGet(`articles.php?action=all`);

  if (!Array.isArray(data) || data.length === 0) {
    const noArticle =
      '<div style="text-align:center;color:#888;padding:2rem;">Aucun article disponible.</div>';
    const c1 = document.getElementById("a-la-une-container");
    const c2 = document.getElementById("dernieres-actualites-container");
    if (c1) c1.innerHTML = noArticle;
    if (c2) c2.innerHTML = noArticle;
    return;
  }

  const imgDefault =
    window.IMG_DEFAULT ||
    "https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80";

  // À la une (first article)
  const aLaUneContainer = document.getElementById("a-la-une-container");
  if (aLaUneContainer) {
    const a = data[0];
    const img = a.image || imgDefault;
    aLaUneContainer.innerHTML = `
      <div class="article-featured">
        <img class="featured-img" src="${img}" alt="${escapeHTML(a.titre)}">
        <div>
          <div class="featured-category">${escapeHTML(a.categorie || "")}</div>
          <h2 class="featured-title"><a href="${appBase}/articles/detail.php?id=${a.id}">${escapeHTML(a.titre)}</a></h2>
          <p class="featured-excerpt">${escapeHTML(a.description || "")}</p>
          <div class="featured-meta">${escapeHTML(a.auteur || "")} | ${formatDateFr(a.date_publication)}</div>
        </div>
      </div>`;
  }

  // Dernières actualités (articles 1-3)
  const dernieresContainer = document.getElementById(
    "dernieres-actualites-container",
  );
  if (dernieresContainer) {
    const dernieres = data.slice(1, 4);
    dernieresContainer.innerHTML = dernieres
      .map((a) => {
        const img = a.image || imgDefault;
        return `<article class="article-card-sm">
        <img class="card-img" src="${img}" alt="${escapeHTML(a.titre)}">
        <div class="card-category">${escapeHTML(a.categorie || "")}</div>
        <h3 class="card-title"><a href="${appBase}/articles/detail.php?id=${a.id}">${escapeHTML(a.titre)}</a></h3>
        <div class="card-meta">${escapeHTML(a.auteur || "")} — ${formatDateFr(a.date_publication)}</div>
      </article>`;
      })
      .join("");
  }

  // Plus d'actualités (articles 4-8)
  const plusContainer = document.getElementById("plus-actualites-container");
  if (plusContainer) {
    const plus = data.slice(4, 9);
    plusContainer.innerHTML = plus
      .map((a) => {
        const img = a.image || imgDefault;
        return `<div class="article-list-item">
        <img src="${img}" alt="${escapeHTML(a.titre)}">
        <div class="content">
          <div class="list-category">${escapeHTML(a.categorie || "")}</div>
          <h3 class="list-title"><a href="${appBase}/articles/detail.php?id=${a.id}">${escapeHTML(a.titre)}</a></h3>
          <div class="list-meta">${escapeHTML(a.auteur || "")} — ${formatDateFr(a.date_publication)}</div>
        </div>
      </div>`;
      })
      .join("");
  }

  // En continu sidebar (first 6)
  const enContinuContainer = document.getElementById("en-continu-container");
  if (enContinuContainer) {
    const enContinu = data.slice(0, 6);
    enContinuContainer.innerHTML = enContinu
      .map((a) => {
        const time = a.date_publication
          ? new Date(a.date_publication).toLocaleTimeString("fr-FR", {
              hour: "2-digit",
              minute: "2-digit",
            })
          : "";
        return `<div class="sidebar-item">
        <div class="sidebar-time">${time}</div>
        <div class="sidebar-item-title"><a href="${appBase}/articles/detail.php?id=${a.id}">${escapeHTML(a.titre)}</a></div>
        <div class="sidebar-cat">${escapeHTML((a.description || "").substring(0, 100))}${(a.description || "").length > 100 ? "…" : ""}</div>
      </div>`;
      })
      .join("");
  }
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
  } else if (method === "patch") {
    if (payload.attribute && payload.value && payload.id) {
      if (payload.attribute === "id" && isNaN(Number(payload.value)))
        errors.id = "Article invalide";
      else if (payload.attribute === "titre" && payload.value.trim().length < 3)
        errors.titre = "Le titre doit contenir au moins 3 caractères";
      else if (
        payload.attribute === "description" &&
        payload.value.trim().length < 10
      )
        errors.description =
          "La description courte doit contenir au moins 10 caractères.";
      else if (
        payload.attribute === "contenu" &&
        payload.value.trim().length < 20
      )
        errors.contenu = "Le contenu doit contenir au moins 20 caractères.";
      else if (
        payload.attribute === "category_id" &&
        isNaN(Number(payload.value))
      )
        errors.categorie_id = "Veuillez choisir une catégorie valide.";
    } else errors.attributeValue = "Veuillez choisir un attribut";
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
    };

    const errors = validatePayload(payload, "post");
    showFormErrors(postForm, errors);

    if (Object.keys(errors).length > 0) return;

    const formData = new FormData(e.target);

    const req = await fetch("../api/articles.php?action=create", {
      method: "POST",
      body: formData,
    });

    const res = await req.json();

    if (res.ok) {
      showFormErrors(postForm, { success: "Article ajouté avec succès" });
      setTimeout(() => (window.location.href = appBase + "/accueil.php"), 1000);
    } else {
      showFormErrors(postForm, { server: res.error || "Erreur serveur" });
    }
  });
}

async function populateEditForm() {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");

  if (!id) {
    alert("Aucun article spécifié");
    window.location.href = appBase + "/accueil.php";
    return;
  }

  try {
    const catData = await apiGet("categories.php?action=all");
    const select = document.querySelector(".categorie_id");
    catData.forEach((cat) => {
      const option = document.createElement("option");
      option.value = cat.id;
      option.textContent = cat.nom;
      select.appendChild(option);
    });
  } catch (err) {
    console.error(err);
    alert("Erreur lors du chargement des catégories");
    return;
  }

  try {
    const article = await apiGet("articles.php?action=search_by_id&id=" + id);

    document.querySelector(".article-id").value = article.id;
    document.querySelector(".titre").value = article.titre;
    document.querySelector(".description").value = article.description;
    document.querySelector(".contenu").value = article.contenu;
    document.querySelector(".categorie_id").value = article.categorie_id;

    if (article.image) {
      const img = document.querySelector(".current-image");
      img.src = "/" + article.image;
      img.style.display = "block";
    }
  } catch (err) {
    alert("Article introuvable");
    window.location.href = appBase + "/accueil.php";
    return;
  }

  submitEditForm();
}

function submitEditForm() {
  editForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const titre = editForm.titre.value.trim();
    const description = editForm.description.value.trim();
    const contenu = editForm.contenu.value.trim();
    const categorie = editForm.categorie_id.value;

    if (titre.length < 3) {
      alert("Le titre doit contenir au moins 3 caractères");
      return;
    }
    if (description.length < 10) {
      alert("La description doit contenir au moins 10 caractères");
      return;
    }
    if (contenu.length < 20) {
      alert("Le contenu doit contenir au moins 20 caractères");
      return;
    }
    if (!categorie) {
      alert("Veuillez choisir une catégorie");
      return;
    }

    const formData = new FormData(editForm);

    try {
      const res = await fetch(appBase + "/api/articles.php?action=update", {
        method: "POST",
        body: formData,
      });

      const data = await res.json();

      if (res.ok) {
        window.location.href = appBase + "/articles/detail.php?id=" + data.id;
      } else {
        showFormErrors(editForm, "Erreur lors de la modification");
      }
    } catch (err) {
      console.error(err);
      showFormErrors(editForm, { server: err.message || "Erreur server" });
    }
  });
}

async function populateDeleteForm() {
  const data = await apiGet("articles.php?action=all");
  data.forEach((article) => {
    const form = document.createElement("form");
    form.className = "delete-form admin-delete-item";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "article";
    input.value = article.id;

    const submitButton = document.createElement("button");
    submitButton.type = "submit";
    submitButton.className = "btn btn-danger btn-sm";
    submitButton.textContent = "Supprimer";

    const span = document.createElement("span");
    span.textContent = article.titre;

    form.appendChild(input);
    form.appendChild(span);
    form.appendChild(submitButton);

    deleteFormContainer.appendChild(form);
  });
  submitDeleteForm();
}

function submitDeleteForm() {
  const deleteForms = document.querySelectorAll(".delete-form");
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
        showFormErrors(deleteForm, { success: "Article supprimé avec succès" });
        setTimeout(() => deleteForm.remove(), 1500);
      } catch (err) {
        console.error(err);
        showFormErrors(deleteForm, { server: err.message || "Erreur serveur" });
      }
    });
  });
}

function searchBar() {
  const searchBarEl = document.querySelector("input[name='search-bar']");
  const queryResults = document.querySelector(".query-results");
  if (!searchBarEl || !queryResults) return;

  searchBarEl.addEventListener("input", async (e) => {
    const input = e.target.value;
    if (input.trim() === "") {
      queryResults.innerHTML = "<p>Veuillez saisir un mot clé</p>";
      return;
    }

    try {
      const articles = await apiGet(
        `articles.php?action=search_bar&input=${input}`,
      );

      if (articles.length === 0) {
        queryResults.innerHTML = "Aucun résultat";
      } else {
        queryResults.innerHTML = `${articles.map((article) => `<p class="search-result-item"><a href="${appBase}/articles/detail.php?id=${article.id}">${escapeHTML(article.titre)}</a></p>`).join("")}`;
      }
    } catch (err) {
      console.error(err);
    }
  });
}

if (window.location.pathname.includes("accueil.php")) {
  getLatestArticles();
}

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

if (window.location.pathname.includes("/articles/supprimer.php"))
  populateDeleteForm();

if (window.location.pathname.includes("/articles/modifier.php"))
  populateEditForm();
