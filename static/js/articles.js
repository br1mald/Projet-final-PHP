import { showFormErrors, escapeHTML } from "./validation.js";
import { apiGet, apiPost, apiPatch, apiDelete } from "./api.js";

const appBase = "/final_project"; // changer selon la structure du serveur

const articlesContainer = document.querySelector(".articles-container");
const formSelectField = document.querySelector(".form-select-field");
const postForm = document.querySelector("form[action='../api/articles.php']");
const patchFormContainer = document.querySelector(".patch-form-container");
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
      date_publication: postForm.date.value,
    };

    const errors = validatePayload(payload, "post"); // on valide le contenu
    showFormErrors(postForm, errors); // on affiche les erreurs si il y en a

    if (Object.keys(errors).length > 0) return; // si il y a des erreurs on arrête l'exécution de la fonction

    try {
      const res = await apiPost("articles.php", payload); // envoi de la requête
      console.log("created", res);
      showFormErrors(postForm, { success: "Article ajouté avec succès" }); // affichage d'un message de succès
      setTimeout(
        // redirection de l'utilisateur vers accueil.php après un court délai
        () => (window.location.href = "/final_project/accueil.php"),
        1000,
      );
    } catch (err) {
      // en cas d'erreur
      console.error(err);
      showFormErrors(postForm, { server: err.message || "Erreur serveur" }); // on montre un message d'erreur à l'utilisateur
    }
  });
}

async function populatePatchForm() {
  // permet de génerer le formulaire de modification dans modifier.php
  const data = await apiGet("articles.php?action=all"); // récupération de tous les articles
  data.forEach((article) => {
    // pour chaque article
    console.log("found article");
    const article_attributes = Object.entries(article); // on récupère le nom de l'attribut et la valeur sous forme de tableau

    const allowedAttributes = [
      // les attributs dont la modification est autorisée
      "titre",
      "description",
      "contenu",
      "categorie_id",
    ];

    article_attributes.forEach((attribute) => {
      // pour chaque attribut de l'article
      if (!allowedAttributes.includes(attribute[0])) return; // si l'attribut ne doit pas être modifié on arrête l'exécution pour celui-ci

      console.log(`Found attribute: ${attribute}`);

      const form = document.createElement("form"); // on crée un formulaire
      form.className = "patch-form";

      const idInput = document.createElement("input"); // on crée un input de type hidden qui stocke l'id de l'article en question
      idInput.type = "hidden";
      idInput.value = article.id;
      idInput.name = "articleId";

      form.appendChild(idInput); // on l'ajoute au formulaire

      const nameInput = document.createElement("input"); // on crée un input de type hidden qui stocke le nom de l'attribut

      nameInput.type = "hidden";
      nameInput.value = attribute[0];
      nameInput.name = "attributeName";

      form.appendChild(nameInput); // on l'ajoute au formulaire

      const valueInput = document.createElement("input"); // on crée un input de type text qui permet de modifier la valeur de l'attribut

      valueInput.type = "text";
      valueInput.value = attribute[1];
      valueInput.name = "attributeValue";

      form.appendChild(valueInput); // on l'ajoute au formulaire

      const submitButton = document.createElement("button"); // bouton de soumission
      submitButton.type = "submit";

      form.appendChild(submitButton); // on l'ajoute au formulaire

      patchFormContainer.appendChild(form); // on ajoute le formulaire au patchFormContainer dans modifier.php
    });
    const br = document.createElement("br"); // on ajoute une ligne pour séparer chaque formulaire, à enlever plus tard
    patchFormContainer.appendChild(br);
  });
  submitPatchForm(); // active la fonction qui permet de soumettre les formulaires une fois qu'ils sont tous générés
}

function submitPatchForm() {
  // pour soumettre le formulaire de modification
  const patchForms = document.querySelectorAll(".patch-form"); // on récupère tous les formulaires de modification
  patchForms.forEach((patchForm) => {
    // pour chaque formulaire
    patchForm.addEventListener("submit", async (e) => {
      // activation quand l'utilisateur essaie d'envoyer le formulaire
      e.preventDefault();

      const payload = {
        // contenu du body de la requête
        id: patchForm.articleId.value, // id de l'article
        attribute: patchForm.attributeName.value, // nom de l'attribut
        value: patchForm.attributeValue.value, // valeur de l'attribut
      };

      const errors = validatePayload(payload, "patch"); // validation côté client
      showFormErrors(patchForm, errors);

      if (Object.keys(errors).length > 0) return;

      try {
        const res = await apiPatch("articles.php", payload); // envoi de la requête
        console.log("Success", res);
        showFormErrors(patchForm, { success: "Article modifié avec succès." }); // affichage message de succès
      } catch (err) {
        // en cas d'erreur
        console.error(err);
        showFormErrors(patchForm, { server: err.message || "Erreur serveur" }); // affichage message d'erreur
      }
    });
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

console.log(window.location.pathname);
if (window.location.pathname.includes("accueil.php")) getLatestArticles();
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
  populatePatchForm(); // active la fonction qui permet de soumettre le formulaire de modification
