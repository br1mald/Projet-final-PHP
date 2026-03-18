const articlesContainer = document.querySelector("#articles-container");

console.log("Hello"); // debugging, file wasn't loading

async function getArticle(id) {
  const data = await apiGet(`articles.php?action=search&id=${id}`);
  const articleContainer = document.querySelector("#article-details");
  articleContainer.innerHTML = `${data.titre} <br> ${data.contenu}`;
  // return data;
}

if (window.location.href.includes("detail.php?id=1")) getArticle(1);

async function getLatestArticles() {
  data = await apiGet(`articles.php?action=latest`);
  data.forEach((article) => {
    const newArticle = document.createElement("li");
    newArticle.innerHTML = `
      <a id="article-link" href="./articles/detail.php">${article.titre}</a>
      <p>${article.description}</a>
      `;
    articlesContainer.appendChild(newArticle);
  });
}
console.log(window.location.pathname);
if (window.location.pathname.includes("accueil.php")) getLatestArticles();

async function loadArticle(id) {
  data = await getArticle(id);
  window.location.href = "/articles/detail.php";
  const articleContainer = document.querySelector("#article-detail");
  articleContainer.innerHTML = `${data.titre} <br> ${data.contenu}`;
}
