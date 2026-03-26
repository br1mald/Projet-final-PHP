<?php
$termeRecherche = isset($_GET['q']) ? trim($_GET['q']) : '';
$pageTitle = $termeRecherche !== '' ? 'Recherche : ' . $termeRecherche : 'Recherche';
require_once __DIR__ . '/../entete.php';
?>

<script>
const IMG_DEFAULT = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80';
const TERME_RECHERCHE = <?= json_encode($termeRecherche) ?>;
</script>

<main class="container">
  <div class="page-header">
    <h1>Recherche</h1>
    <div class="search-form">
      <form id="search-form" method="GET">
        <input type="text" name="q" id="search-input" placeholder="Rechercher un article..."
          value="<?= htmlspecialchars($termeRecherche) ?>" class="search-input">
        <button type="submit" class="search-btn">🔍 Rechercher</button>
      </form>
    </div>
  </div>

  <div id="search-results">
    <div class="loading-message">Entrez un mot clé pour rechercher un article.</div>
  </div>
</main>


<script>
async function apiGet(endpoint) {
  try {
    const base = window.API_BASE || '/final_project/api';
    const response = await fetch(`${base}/${endpoint}`);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    return await response.json();
  } catch (error) {
    console.error('Erreur API:', error);
    throw error;
  }
}

async function getAllArticles() {
  try {
    const data = await apiGet('articles.php?action=all');
    return Array.isArray(data) ? data : [];
  } catch (error) {
    return [];
  }
}

function searchArticles(articles, searchTerm) {
  if (!searchTerm) return [];
  const term = searchTerm.toLowerCase();
  return articles.filter(article =>
    (article.titre && article.titre.toLowerCase().includes(term)) ||
    (article.description && article.description.toLowerCase().includes(term)) ||
    (article.contenu && article.contenu.toLowerCase().includes(term)) ||
    (article.categorie && article.categorie.toLowerCase().includes(term))
  );
}

function formatDateFr(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
  return d.getDate() + ' ' + mois[d.getMonth()] + ' ' + d.getFullYear();
}

function escapeHTML(str) {
  if (!str) return '';
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

function createFeaturedArticle(article) {
  const img = article.image_url || IMG_DEFAULT;
  return `<div class="article-featured">
    <img src="${img}" alt="${escapeHTML(article.titre)}">
    <div class="content">
      <div class="featured-category">${escapeHTML(article.categorie || 'Non classé')}</div>
      <h2 class="featured-title"><a href="detail.php?id=${article.id}">${escapeHTML(article.titre)}</a></h2>
      <p class="featured-excerpt">${escapeHTML(article.description || '')}</p>
      <div class="featured-meta">${escapeHTML(article.auteur || 'Anonyme')} | ${formatDateFr(article.date_publication)}</div>
    </div>
  </div>`;
}

function createArticleCard(article) {
  const img = article.image_url || IMG_DEFAULT;
  return `<article class="article-card-sm">
    <img src="${img}" alt="${escapeHTML(article.titre)}">
    <div class="content">
      <div class="card-category">${escapeHTML(article.categorie || 'Non classé')}</div>
      <h3 class="card-title"><a href="detail.php?id=${article.id}">${escapeHTML(article.titre)}</a></h3>
      <div class="card-meta">${escapeHTML(article.auteur || 'Anonyme')} — ${formatDateFr(article.date_publication)}</div>
    </div>
  </article>`;
}

async function performSearch() {
  try {
    const searchTerm = TERME_RECHERCHE;
    if (!searchTerm) {
      document.getElementById('search-results').innerHTML = '<div class="loading-message">Entrez un mot clé pour rechercher un article.</div>';
      return;
    }
    const allArticles = await getAllArticles();
    const resultats = searchArticles(allArticles, searchTerm);
    const container = document.getElementById('search-results');
    if (resultats.length === 0) {
      container.innerHTML = `<div class="search-results-header"><h2>Aucun résultat pour "${escapeHTML(searchTerm)}"</h2><p>Essayez avec d'autres mots-clés ou vérifiez l'orthographe.</p></div>`;
      return;
    }
    let html = `<div class="search-results-header"><h2>${resultats.length} résultat(s) pour "${escapeHTML(searchTerm)}"</h2><p>Articles trouvés dans le titre, la description, le contenu ou la catégorie.</p></div>`;
    html += createFeaturedArticle(resultats[0]);
    const secondaires = resultats.slice(1, 4);
    if (secondaires.length > 0) {
      html += '<div class="articles-grid-3">';
      secondaires.forEach(article => { html += createArticleCard(article); });
      html += '</div>';
    }
    const reste = resultats.slice(4);
    if (reste.length > 0) {
      html += '<div class="section-title">Plus de résultats</div>';
      reste.forEach(article => { html += createArticleCard(article); });
    }
    container.innerHTML = html;
  } catch (error) {
    console.error('Erreur lors de la recherche:', error);
    document.getElementById('search-results').innerHTML = '<div class="loading-message">Erreur de recherche. Veuillez réessayer.</div>';
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', performSearch);
} else {
  performSearch();
}
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>
