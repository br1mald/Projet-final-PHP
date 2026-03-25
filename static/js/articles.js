import { showFormErrors, escapeHTML } from "./validation.js";
import { apiGet, apiPost, apiPatch, apiDelete } from "./api.js";

const appBase = "/final_project"; // changer selon la structure du serveur

const articlesContainer = document.querySelector(".articles-container");
const formSelectField = document.querySelector(".form-select-field");
const postForm = document.querySelector(".post-form");
const editForm = document.querySelector(".edit-article-form");
const deleteFormContainer = document.querySelector(".delete-form-container");

console.log("Hello"); // debugging, file wasn't loading

async function getArticle(id) {
  const data = await apiGet(`articles.php?action=search_by_id&id=${id}`);
  return data;
}

async function renderArticleDetails(id) {
  const data = await getArticle(id);
  const articleContainer = document.querySelector(".article-details");
  articleContainer.innerHTML = `<h1>${escapeHTML(data.titre)}</h1> <br>
    <img src=/final_project/${data.image}> <br>
    Contenu: ${escapeHTML(data.contenu)} <br>
    Catégorie: ${escapeHTML(data.cat_nom)} <br>
    Auteur: ${escapeHTML(data.util_nom)} <br>
    Date de publication: ${escapeHTML(data.date_publication)}`;
  console.log("rendered"); // testing
}

async function getLatestArticles() {
  // récupère les derniers articles pour les afficher dans accueil.php
  const data = await apiGet(`articles.php?action=latest`);
  articlesContainer.innerHTML = "";
  console.log("Emptied container"); // debugging
  data.forEach((article) => {
    const li = document.createElement("li"); // crée un élément li

    const a = document.createElement("a");
    a.className = "article-link";
    a.href = `./articles/detail.php?id=${article.id}`;
    a.textContent = article.titre; // le titre est mis sous forme de lien

    const p = document.createElement("p"); // crée un paragraphe qui contient la description courte
    p.textContent = article.description;

    li.appendChild(a);
    li.appendChild(p); // on ajoute le titre (lien) et le paragraphe dans le li

    articlesContainer.appendChild(li); // on ajoute le li (et par conséquent le reste) à articlesContainer
  });
}

async function getAllArticles() {
  // processus similaire à getLatestArticles() mais pour récupérer tous les articles
  // not used
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
  // permet de charger l'élément qui permet de sélectionner la catégorie
  const data = await apiGet("categories.php?action=all"); // on récupère toutes les catégories
  data.forEach((category) => {
    // pour chaque catégorie:
    const option = document.createElement("option"); // on crée  une option
    option.textContent = category.nom; // on lui donne pour libellé le nom de la catégorie
    option.value = category.id; // on lui donne pour valeur l'id de la catégorie
    formSelectField.appendChild(option); // on l'ajoute au select
  });
}

function validatePayload(payload, method) {
  // permet de faire la validation côté client
  const errors = {};
  if (method === "post") {
    // pour une requête POST (création)
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
    // requête DELETE (suppression)
    if (!payload.articleId || isNaN(Number(payload.articleId))) {
      errors.articleId = "Veuillez choisir un article valide.";
    }
  } else if (method === "patch") {
    // requête PATCH (modification)
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

  return errors; // on renvoie un dictionnaire qui contient les erreurs si il y en a.
}

function submitPostForm() {
  // permettre de soumettre le formulaire
  postForm.addEventListener("submit", async (e) => {
    // activation lorsque l'utilisateur essaie d'envoyer le formulaire
    e.preventDefault();

    const payload = {
      // le contenu du body de la requête POST
      titre: postForm.title.value,
      description: postForm.description.value,
      contenu: postForm.content.value,
      categorie_id: postForm.category.value,
    };

    const errors = validatePayload(payload, "post"); // on valide le contenu
    showFormErrors(postForm, errors); // on affiche les erreurs si il y en a

    if (Object.keys(errors).length > 0) return; // si il y a des erreurs on arrête l'exécution de la fonction

    const formData = new FormData(e.target);

    const req = await fetch("../api/articles.php?action=create", {
      method: "POST",
      body: formData,
    }); // envoi de la requête

    const res = await req.json();

    if (res.ok) {
      showFormErrors(postForm, { success: "Article ajouté avec succès" }); // affichage d'un message de succès
      setTimeout(
        // redirection de l'utilisateur vers accueil.php après un court délai
        () => (window.location.href = "/final_project/accueil.php"),
        1000,
      );
    } else {
      // en cas d'erreur
      console.error(err);
      showFormErrors(postForm, { server: err.message || "Erreur serveur" }); // on montre un message d'erreur à l'utilisateur
    }
  });
}

async function populateEditForm() {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");

  if (!id) {
    alert("Aucun article spécifié");
    window.location.href = "/accueil.php";
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
    window.location.href = "/final_project/accueil.php";
    return;
  }

  submitEditForm(); // active la fonction qui permet de soumettre les formulaires une fois qu'ils sont tous générés
}

function submitEditForm() {
  // pour soumettre le formulaire de modification
  editForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Client-side validation
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

    // envoi de la requête
    const formData = new FormData(editForm);

    try {
      const res = await fetch("/final_project/api/articles.php?action=update", {
        method: "POST",
        body: formData,
      });

      const data = await res.json();

      if (res.ok) {
        window.location.href =
          "/final_project/articles/detail.php?id=" + data.id;
      } else {
        // si il y a erreur
        showFormErrors(editForm, "Erreur lors de la modification");
      }
    } catch (err) {
      console.error(err);
      showFormErrors(editForm, { server: err.message || "Erreur server" });
    }
  });
}

async function populateDeleteForm() {
  // permet de générer le formulaire de suppression
  const data = await apiGet("articles.php?action=all"); // on récupère tous les articles
  data.forEach((article) => {
    // pour chaque article
    const form = document.createElement("form"); // on crée un formulaire
    form.className = "delete-form";

    const input = document.createElement("input"); // input qui contient l'id de l'article à supprimer
    input.type = "hidden";
    input.name = "article";
    input.value = article.id;

    const submitButton = document.createElement("button"); // bouton de soumission
    submitButton.type = "submit";
    submitButton.textContent = article.titre;

    form.appendChild(input);
    form.appendChild(submitButton);

    deleteFormContainer.appendChild(form); // ajout du formulaire au deleteFormContainer
  });
  submitDeleteForm(); // active la fonction qui permet de soumettre les formulaires une fois qu'ils sont tous générés
}

function submitDeleteForm() {
  // permet de soumettre le formulaire
  const deleteForms = document.querySelectorAll(".delete-form"); // might want to do this for the post form too.
  deleteForms.forEach((deleteForm) => {
    // pour chaque formulaire de suppression
    deleteForm.addEventListener("submit", async (e) => {
      // activation quand l'utilisateur essaie de soumettre le formulaire
      e.preventDefault();
      const payload = {
        // contenu du body de la requête
        articleId: deleteForm.article.value,
      };

      const errors = validatePayload(payload, "delete"); // validation client
      showFormErrors(deleteForm, errors);

      if (Object.keys(errors).length > 0) return;

      try {
        const res = await apiDelete(`articles.php`, payload); // envoi de la requête
        console.log("deleted", res);
        showFormErrors(deleteForm, { success: "Article supprimé avec succès" });

        setTimeout(() => deleteForm.remove(), 1500); // on supprime le formulaire après un court délai en cas de succès
      } catch (err) {
        // en cas d'erreur
        console.error(err);
        showFormErrors(deleteForm, { server: err.message || "Erreur serveur" }); // affichage message d'erreur
      }
    });
  });
}

function searchBar() {
  // barre de recherche
  const searchBar = document.querySelector("input[name='search-bar']"); // l'input qui sert de barre de recherche
  const queryResults = document.querySelector(".query-results"); // div qui va afficher les résultats obtenus

  searchBar.addEventListener("input", async (e) => {
    // activation quand l'utilisateur saisit quelque chose
    const input = e.target.value; // on récupère la valeur saisie
    if (input.trim() === "") {
      // si input est vide (si l'utilisateur efface tout) on affiche ce message
      queryResults.innerHTML = "<p>Veuillez saisir un mot clé</p>";
      return;
    }

    console.log(input);
    try {
      const articles = await apiGet(
        // envoi de la requête
        `articles.php?action=search_bar&input=${input}`,
      );

      if (articles.length === 0) {
        // si on n'obtient aucun résultat
        queryResults.innerHTML = "Aucun résultat";
      } else {
        // on affiche les résultats
        queryResults.innerHTML = `${articles.map((article) => `<p style='color: green'>${escapeHTML(article.titre)}</p>`).join("")}`;
      }
    } catch (err) {
      console.error(err);
    }
  });
}

console.log(window.location.pathname);
if (window.location.pathname.includes("accueil.php")) {
  getLatestArticles();
  searchBar();
}
// if (window.location.pathname.includes("accueil.php")) getAllArticles(); testing

if (window.location.pathname.includes("detail.php")) {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  if (id) renderArticleDetails(id);
}

if (window.location.pathname.includes("/articles/ajouter.php")) {
  const currentTime = new Date(); // objet de type Date
  const dateField = document.querySelector(".current-date");
  dateField.value = currentTime.toISOString().slice(0, 19).replace("T", " "); // permet de formatter la date dans un format accepté par MySQL
  populateSelectForm(); // active la fonction pour charger les options de catégorie
  submitPostForm(); // active la fonction qui permet de soumettre le formulaire de création
}

if (window.location.pathname.includes("/articles/supprimer.php"))
  populateDeleteForm(); // active la fonction qui permet de soumettre le formulaire de suppresssion

if (window.location.pathname.includes("/articles/modifier.php"))
  populateEditForm(); // active la fonction qui permet de soumettre le formulaire de modification
