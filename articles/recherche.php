<?php
session_start();

// Récupérer le terme de recherche depuis l'URL
$termeRecherche = isset($_GET['q']) ? trim($_GET['q']) : '';
$pageTitle = $termeRecherche !== '' ? 'Recherche : ' . $termeRecherche : 'Recherche';
require_once __DIR__ . '/../entete.php';
?>

<!-- Image par défaut -->
<script>
const IMG_DEFAULT = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80';
const TERME_RECHERCHE = <?= json_encode($termeRecherche) ?>;
</script>

<main class="container">
  <div class="page-header">
    <h1>Recherche</h1>
    <div class="search-form">
      <form id="search-form" method="GET">
        <input 
          type="text" 
          name="q" 
          id="search-input" 
          placeholder="Rechercher un article..." 
          value="<?= htmlspecialchars($termeRecherche) ?>"
          class="search-input"
        >
        <button type="submit" class="search-btn">
          🔍 Rechercher
        </button>
      </form>
    </div>
  </div>

  <!-- Résultats de recherche -->
  <div id="search-results">
    <div class="loading-message">Entrez un mot clé pour rechercher un article.</div>
  </div>
</main>

<style>
.search-form {
  margin: 2rem 0;
}

#search-form {
  display: flex;
  gap: 0.5rem;
  max-width: 600px;
  margin: 0 auto;
}

.search-input {
  flex: 1;
  padding: 0.875rem;
  border: 2px solid var(--border-light);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(187, 249, 252, 0.2);
}

.search-btn {
  padding: 0.875rem 1.5rem;
  background: var(--accent);
  color: var(--primary);
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.search-btn:hover {
  background: var(--accent-dark);
  transform: translateY(-1px);
}

.loading-message {
  text-align: center;
  color: var(--muted);
  padding: 3rem;
  font-style: italic;
  background: #fff;
  border-radius: 12px;
  margin: 2rem 0;
}

.search-results-header {
  margin: 2rem 0;
  padding: 1rem;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 12px;
  border: 1px solid var(--border-light);
}

.search-results-header h2 {
  color: var(--text);
  margin-bottom: 0.5rem;
}

.search-results-header p {
  color: var(--muted);
  margin: 0;
}

.article-featured {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  margin-bottom: 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.article-featured img {
  width: 100%;
  height: 300px;
  object-fit: cover;
}

.article-featured .content {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.article-featured .featured-category {
  color: var(--accent);
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.article-featured .featured-title {
  margin: 0 0 1rem 0;
  font-size: 1.5rem;
  line-height: 1.3;
}

.article-featured .featured-title a {
  color: var(--text);
  text-decoration: none;
}

.article-featured .featured-title a:hover {
  color: var(--accent);
}

.article-featured .featured-excerpt {
  color: var(--muted);
  line-height: 1.6;
  margin-bottom: 1rem;
}

.article-featured .featured-meta {
  color: var(--muted);
  font-size: 0.9rem;
}

.articles-grid-3 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.article-card-sm {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  transition: transform 0.3s ease;
}

.article-card-sm:hover {
  transform: translateY(-4px);
}

.article-card-sm img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.article-card-sm .content {
  padding: 1.5rem;
}

.article-card-sm .card-category {
  color: var(--accent);
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.article-card-sm .card-title {
  margin: 0.5rem 0;
  font-size: 1.1rem;
  line-height: 1.3;
}

.article-card-sm .card-title a {
  color: var(--text);
  text-decoration: none;
}

.article-card-sm .card-title a:hover {
  color: var(--accent);
}

.article-card-sm .card-meta {
  color: var(--muted);
  font-size: 0.85rem;
}

.section-title {
  color: var(--text);
  font-size: 1.3rem;
  margin: 2rem 0 1rem 0;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--accent);
}

@media (max-width: 768px) {
  #search-form {
    flex-direction: column;
  }
  
  .article-featured {
    grid-template-columns: 1fr;
  }
  
  .article-featured img {
    height: 200px;
  }
  
  .articles-grid-3 {
    grid-template-columns: 1fr;
  }
}
</style>

<script>
// Fonction API simplifiée
async function apiGet(endpoint) {
  try {
    const response = await fetch(`/Projet-final-PHP/api/${endpoint}`);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Erreur API:', error);
    throw error;
  }
}

// Récupérer tous les articles
async function getAllArticles() {
  try {
    const data = await apiGet('articles.php?action=all');
    return Array.isArray(data) ? data : [];
  } catch (error) {
    console.error('Erreur lors de la récupération des articles:', error);
    return [];
  }
}

// Rechercher dans les articles
function searchArticles(articles, searchTerm) {
  if (!searchTerm) return [];
  
  const term = searchTerm.toLowerCase();
  
  return articles.filter(article => {
    return (
      (article.titre && article.titre.toLowerCase().includes(term)) ||
      (article.description && article.description.toLowerCase().includes(term)) ||
      (article.contenu && article.contenu.toLowerCase().includes(term)) ||
      (article.categorie && article.categorie.toLowerCase().includes(term))
    );
  });
}

// Formater la date en français
function formatDateFr(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
  return d.getDate() + ' ' + mois[d.getMonth()] + ' ' + d.getFullYear();
}

// Obtenir l'image d'un article
function getArticleImage(article) {
  return article.image_url || IMG_DEFAULT;
}

// Échapper le HTML
function escapeHTML(str) {
  if (!str) return '';
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

// Créer l'article vedette
function createFeaturedArticle(article) {
  const img = getArticleImage(article);
  
  return `
    <div class="article-featured">
      <img src="${img}" alt="${escapeHTML(article.titre)}">
      <div class="content">
        <div class="featured-category">${escapeHTML(article.categorie || 'Non classé')}</div>
        <h2 class="featured-title">
          <a href="detail.php?id=${article.id}">${escapeHTML(article.titre)}</a>
        </h2>
        <p class="featured-excerpt">${escapeHTML(article.description || '')}</p>
        <div class="featured-meta">
          ${escapeHTML(article.auteur || 'Anonyme')} | ${formatDateFr(article.date_publication)}
        </div>
      </div>
    </div>
  `;
}

// Créer une carte d'article
function createArticleCard(article) {
  const img = getArticleImage(article);
  
  return `
    <article class="article-card-sm">
      <img src="${img}" alt="${escapeHTML(article.titre)}">
      <div class="content">
        <div class="card-category">${escapeHTML(article.categorie || 'Non classé')}</div>
        <h3 class="card-title">
          <a href="detail.php?id=${article.id}">${escapeHTML(article.titre)}</a>
        </h3>
        <div class="card-meta">${escapeHTML(article.auteur || 'Anonyme')} — ${formatDateFr(article.date_publication)}</div>
      </div>
    </article>
  `;
}

// Effectuer la recherche
async function performSearch() {
  try {
    const searchTerm = TERME_RECHERCHE;
    console.log('Recherche pour:', searchTerm);
    
    if (!searchTerm) {
      document.getElementById('search-results').innerHTML = '<div class="loading-message">Entrez un mot clé pour rechercher un article.</div>';
      return;
    }
    
    // Récupérer tous les articles
    const allArticles = await getAllArticles();
    
    // Filtrer les résultats
    const resultats = searchArticles(allArticles, searchTerm);
    
    console.log('Résultats trouvés:', resultats.length);
    
    // Afficher les résultats
    const container = document.getElementById('search-results');
    
    if (resultats.length === 0) {
      container.innerHTML = `
        <div class="search-results-header">
          <h2>Aucun résultat pour "${escapeHTML(searchTerm)}"</h2>
          <p>Essayez avec d'autres mots-clés ou vérifiez l'orthographe.</p>
        </div>
      `;
      return;
    }
    
    // Afficher l'en-tête des résultats
    let html = `
      <div class="search-results-header">
        <h2>${resultats.length} résultat(s) pour "${escapeHTML(searchTerm)}"</h2>
        <p>Articles trouvés dans le titre, la description, le contenu ou la catégorie.</p>
      </div>
    `;
    
    // Article vedette (premier résultat)
    const vedette = resultats[0];
    html += createFeaturedArticle(vedette);
    
    // Articles secondaires (résultats 1-3)
    const secondaires = resultats.slice(1, 4);
    if (secondaires.length > 0) {
      html += '<div class="articles-grid-3">';
      secondaires.forEach(article => {
        html += createArticleCard(article);
      });
      html += '</div>';
    }
    
    // Articles restants
    const reste = resultats.slice(4);
    if (reste.length > 0) {
      html += '<div class="section-title">Plus de résultats</div>';
      reste.forEach(article => {
        html += createArticleCard(article);
      });
    }
    
    container.innerHTML = html;
    
  } catch (error) {
    console.error('Erreur lors de la recherche:', error);
    document.getElementById('search-results').innerHTML = '<div class="loading-message">Erreur de recherche. Veuillez réessayer.</div>';
  }
}

// Charger la recherche au chargement de la page
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', performSearch);
} else {
  performSearch();
}
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>
