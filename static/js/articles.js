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

function escapeAttr(str) {
  if (!str) return '';
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

function formatDateFr(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
  return d.getDate() + ' ' + mois[d.getMonth()] + ' ' + d.getFullYear();
}

function formatTime(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
}

async function renderArticleDetails(id) {
  const loading = document.getElementById('article-loading');
  const errorEl = document.getElementById('article-error');
  const detailsEl = document.getElementById('article-details');
  const enContinuEl = document.getElementById('article-en-continu');
  const similairesEl = document.getElementById('article-similaires');

  if (!loading || !detailsEl) return;

  try {
    const data = await getArticle(id);
    loading.style.display = 'none';
    errorEl.style.display = 'none';

    const imgUrl = data.image || 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80';
    const paragraphes = (data.contenu || '').split('\n\n').filter(p => p.trim());
    const contentHtml = paragraphes.map(p => `<p>${escapeHTML(p.trim())}</p>`).join('');

    let editBtns = '';
    if (window.USER_CAN_EDIT) {
      editBtns = `<div style="display:flex; gap:0.8rem; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-light);">
        <a href="modifier.php?id=${data.id}" class="btn btn-primary btn-sm">Modifier</a>
        <a href="supprimer.php?id=${data.id}" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
      </div>`;
    }

    detailsEl.innerHTML = `
      <article class="article-detail">
        <div class="cat-badge" style="margin-bottom:0.8rem;">${escapeHTML(data.cat_nom || '')}</div>
        <h1 class="article-title">${escapeHTML(data.titre)}</h1>
        <div class="article-meta">
          <span>${escapeHTML(data.util_nom || '')}</span>
          <span>${formatDateFr(data.date_publication)}</span>
        </div>
        <hr style="border:none; border-top:1px solid var(--border-light); margin-bottom:1.5rem;">
        <img src="${escapeAttr(imgUrl)}" alt="${escapeAttr(data.titre)}" style="width:100%; height:380px; object-fit:cover; margin-bottom:1.5rem;">
        <div class="article-content">${contentHtml}</div>
        <div style="margin-top:2rem; padding-top:1rem; border-top:1px solid var(--border-light);">
          <a href="../accueil.php" class="btn btn-secondary btn-sm">← Retour à l'accueil</a>
          ${editBtns}
        </div>
      </article>`;
    detailsEl.style.display = 'block';

    // En continu + Articles similaires via API
    const allData = await apiGet('articles.php?action=all').catch(() => []);
    if (Array.isArray(allData)) {
      // En continu : 6 derniers articles (hors article actuel)
      const enContinu = allData.filter(a => a.id !== data.id).slice(0, 6);
      const enContinuHtml = enContinu.map(a => {
        const desc = (a.description || '').substring(0, 100) + ((a.description || '').length > 100 ? '…' : '');
        return `<div class="sidebar-item">
          <div class="sidebar-time">${formatTime(a.date_publication)}</div>
          <div class="sidebar-item-title"><a href="detail.php?id=${a.id}">${escapeHTML(a.titre)}</a></div>
          ${desc ? `<div class="sidebar-cat">${escapeHTML(desc)}</div>` : ''}
        </div>`;
      }).join('');
      enContinuEl.innerHTML = enContinuHtml || '<div class="sidebar-item">Aucun autre article.</div>';

      // À lire aussi : même catégorie
      const similaires = allData.filter(a => a.categorie_id === data.categorie_id && a.id !== data.id).slice(0, 3);
      if (similaires.length > 0) {
        const simHtml = similaires.map(s => {
          const sImg = s.image || imgUrl;
          return `<div style="margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border-light);">
            <img src="${escapeAttr(sImg)}" alt="" style="width:100%; height:100px; object-fit:cover; margin-bottom:0.5rem;">
            <div style="font-family:'Playfair Display',serif; font-size:0.88rem; font-weight:700; line-height:1.3;"><a href="detail.php?id=${s.id}" style="color:inherit;">${escapeHTML(s.titre)}</a></div>
            <div style="font-size:0.65rem; color:#888; margin-top:0.3rem;">${formatDateFr(s.date_publication)}</div>
          </div>`;
        }).join('');
        similairesEl.innerHTML = simHtml;
      } else {
        similairesEl.innerHTML = '<div style="font-size:0.85rem; color:#888;">Aucun article similaire.</div>';
      }
    } else {
      enContinuEl.innerHTML = '<div class="sidebar-item">Chargement échoué.</div>';
      similairesEl.innerHTML = '';
    }
  } catch (err) {
    if (loading) loading.style.display = 'none';
    if (detailsEl) detailsEl.style.display = 'none';
    if (enContinuEl) enContinuEl.innerHTML = '';
    if (similairesEl) similairesEl.innerHTML = '';
    if (errorEl) {
      errorEl.innerHTML = 'Article introuvable. <a href="../accueil.php" class="btn btn-secondary btn-sm">← Retour à l\'accueil</a>';
      errorEl.style.display = 'block';
    }
    console.error('Erreur détail article:', err);
  }
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
        () => (window.location.href = (window.APP_BASE || "") + "/accueil.php"),
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

if (window.location.pathname.includes("detail.php")) {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  if (id) renderArticleDetails(id);
}

if (window.location.pathname.includes("accueil.php") && articlesContainer) {
  getLatestArticles();
}

if (window.location.pathname.includes("/articles/ajouter.php")) {
  const currentTime = new Date();
  const dateField = document.querySelector(".current-date");
  if (dateField) dateField.value = currentTime.toISOString().slice(0, 19).replace("T", " ");
  if (formSelectField) populateSelectForm();
  if (postForm) submitPostForm();
}

if (window.location.pathname.includes("/articles/supprimer.php") && deleteFormContainer) {
  populateDeleteForm();
}
