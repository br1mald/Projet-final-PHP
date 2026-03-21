const appBase = "/final_project"; // changer selon la structure du serveur

const articlesContainer = document.querySelector("#articles-container");

console.log("Hello"); // debugging, file wasn't loading

async function getArticle(id) {
  const data = await apiGet(`articles.php?action=search&id=${id}`);
  return data;
}

async function renderArticleDetails(id) {
  data = await getArticle(id);
  const articleContainer = document.querySelector("#article-details");
  articleContainer.innerHTML = `<h1>${data.titre}</h1> <br> Contenu: ${data.contenu} <br> Catégorie: ${data.cat_nom} <br> Auteur: ${data.util_nom} <br> Date de publication: ${data.date_publication}`;
  console.log("rendered"); // testing
}

async function getLatestArticles() {
  data = await apiGet(`articles.php?action=latest`);
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
  data = await apiGet("articles.php?action=all");
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

console.log(window.location.pathname);
// if (window.location.pathname.includes("accueil.php")) getLatestArticles();
if (window.location.pathname.includes("accueil.php")) getAllArticles();

if (window.location.pathname.includes("detail.php")) {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  if (id) renderArticleDetails(id);
}
