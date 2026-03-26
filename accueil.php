<?php
$pageTitle = "Accueil";
require_once "entete.php";
?>

<!-- Image par défaut si l'article n'en a pas -->
<script>
const IMG_DEFAULT = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80';
</script>

<main class="container">
  <?php if (isset($_SESSION['message'])) : ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['type_message'] ?? 'info') ?>">
      <?= htmlspecialchars($_SESSION['message']) ?>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['type_message']); ?>
  <?php endif; ?>

  <div class="main-layout">
    <div class="main-content">
      <!-- À la une -->
      <div class="section-title">À la une</div>
      <div id="a-la-une-container">
        <div class="loading-message">Chargement de l'article à la une...</div>
      </div>

      <!-- Dernières actualités -->
      <div class="section-title">Dernières actualités</div>
      <div id="dernieres-actualites-container" class="articles-grid-3">
        <div class="loading-message">Chargement des dernières actualités...</div>
      </div>

      <!-- Plus d'actualités -->
      <div class="section-title">Plus d'actualités</div>
      <div id="plus-actualites-container" class="article-list">
        <div class="loading-message">Chargement des actualités...</div>
      </div>

      <!-- RUBRIQUE : ACTUALITÉS (International) -->
      <div class="rubrique-header">
        <div class="trait"></div>
        <h2>International</h2>
      </div>
      <div id="international-container" class="articles-grid-3">
        <div class="loading-message">Chargement des articles internationaux...</div>
      </div>

      <!-- RUBRIQUE : EUROPE -->
      <div class="rubrique-header">
        <div class="trait"></div>
        <h2>Europe</h2>
      </div>
      <div id="europe-container" class="articles-grid-3">
        <div class="loading-message">Chargement des articles européens...</div>
      </div>

      <!-- RUBRIQUES : AFRIQUE + AMÉRIQUES -->
      <div class="rubriques-double">
        <div class="rubrique-col">
          <div class="rubrique-header">
            <div class="trait"></div>
            <h2>Afrique</h2>
          </div>
          <div id="afrique-container" class="article-list">
            <div class="loading-message">Chargement des articles africains...</div>
          </div>
        </div>

        <div class="rubrique-col">
          <div class="rubrique-header">
            <div class="trait"></div>
            <h2>Amériques</h2>
          </div>
          <div id="americas-container" class="article-list">
            <div class="loading-message">Chargement des articles américains...</div>
          </div>
        </div>
      </div>
    </div>

    <!-- SIDEBAR : En continu (derniers articles de la BDD) -->
    <aside class="sidebar">
      <div class="sidebar-title">En continu</div>
      <div id="en-continu-container">
        <div class="loading-message">Chargement...</div>
      </div>
    </aside>
  </div>
</main>

<style>
.loading-message {
  text-align: center;
  color: var(--muted);
  padding: 2rem;
  font-style: italic;
}

.article-list {
  margin-bottom: 2rem;
}

.article-list-item {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--border-light);
}

.article-list-item img {
  width: 120px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
}

.article-list-item .content {
  flex: 1;
}

.article-list-item .list-category {
  color: var(--accent);
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.article-list-item .list-title a {
  color: var(--text);
  text-decoration: none;
  font-weight: 600;
  line-height: 1.3;
}

.article-list-item .list-title a:hover {
  color: var(--accent);
}

.article-list-item .list-meta {
  color: var(--muted);
  font-size: 0.85rem;
  margin-top: 0.5rem;
}

.rubriques-double {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

@media (max-width: 768px) {
  .rubriques-double {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .article-list-item {
    flex-direction: column;
  }
  
  .article-list-item img {
    width: 100%;
    height: 200px;
  }
}
</style>

<script>
// Fonction API simplifiée pour éviter les imports
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

// Fonction pour récupérer tous les articles via l'API JavaScript
async function getAllArticles() {
  try {
    const data = await apiGet('articles.php?action=all');
    console.log('Articles reçus:', data); // Debug
    return Array.isArray(data) ? data : [];
  } catch (error) {
    console.error('Erreur lors de la récupération des articles:', error);
    return [];
  }
}

// Fonction pour récupérer les articles par catégorie via l'API JavaScript
async function getArticlesByCategory(categoryName) {
  try {
    const allArticles = await getAllArticles();
    return allArticles.filter(article => 
      article.categorie && article.categorie.toLowerCase() === categoryName.toLowerCase()
    );
  } catch (error) {
    console.error('Erreur lors de la récupération des articles par catégorie:', error);
    return [];
  }
}

// Fonction pour formater la date en français
function formatDateFr(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
  return d.getDate() + ' ' + mois[d.getMonth()] + ' ' + d.getFullYear();
}

// Fonction pour obtenir l'image d'un article
function getArticleImage(article) {
  return article.image_url || IMG_DEFAULT;
}

// Fonction pour créer une carte d'article
function createArticleCard(article) {
  const img = getArticleImage(article);
  return `
    <article class="article-card-sm">
      <img class="card-img" src="${img}" alt="${article.titre}">
      <div class="card-category">${article.categorie || 'Non classé'}</div>
      <h3 class="card-title">
        <a href="articles/detail.php?id=${article.id}">${article.titre}</a>
      </h3>
      <div class="card-meta">${article.auteur || 'Anonyme'} — ${formatDateFr(article.date_publication)}</div>
    </article>
  `;
}

// Fonction pour créer un élément de liste d'articles
function createArticleListItem(article) {
  const img = getArticleImage(article);
  return `
    <div class="article-list-item">
      <img class="list-img" src="${img}" alt="${article.titre}">
      <div>
        <div class="list-category">${article.categorie || 'Non classé'}</div>
        <h3 class="list-title">
          <a href="articles/detail.php?id=${article.id}">${article.titre}</a>
        </h3>
        <div class="list-meta">${article.auteur || 'Anonyme'} — ${formatDateFr(article.date_publication)}</div>
      </div>
    </div>
  `;
}

// Fonction pour créer l'article à la une
function createFeaturedArticle(article) {
  const img = getArticleImage(article);
  return `
    <div class="article-featured">
      <img class="featured-img" src="${img}" alt="${article.titre}">
      <div>
        <div class="featured-category">${article.categorie || 'Non classé'}</div>
        <h2 class="featured-title">
          <a href="articles/detail.php?id=${article.id}">${article.titre}</a>
        </h2>
        <p class="featured-excerpt">${article.description}</p>
        <div class="featured-meta">${article.auteur || 'Anonyme'} | ${formatDateFr(article.date_publication)}</div>
      </div>
    </div>
  `;
}

// Fonction pour créer un élément "En continu"
function createEnContinuItem(article) {
  return `
    <div class="sidebar-item">
      <div class="sidebar-time">${new Date(article.date_publication).toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})}</div>
      <div class="sidebar-item-title">
        <a href="articles/detail.php?id=${article.id}">${article.titre}</a>
      </div>
      <div class="sidebar-cat">${article.description ? article.description.substring(0, 100) + '...' : ''}</div>
    </div>
  `;
}

// Fonction principale pour charger l'accueil
async function loadAccueil() {
  console.log('Début du chargement de l\'accueil...'); // Debug
  
  try {
    const allArticles = await getAllArticles();
    console.log('Nombre d\'articles:', allArticles.length); // Debug
    
    if (allArticles.length === 0) {
      document.getElementById('a-la-une-container').innerHTML = '<div class="alert alert-info">Aucun article publié pour le moment.</div>';
      document.getElementById('dernieres-actualites-container').innerHTML = '<div class="alert alert-info">Aucun article disponible.</div>';
      document.getElementById('plus-actualites-container').innerHTML = '<div class="alert alert-info">Aucun article disponible.</div>';
      document.getElementById('international-container').innerHTML = '<div class="alert alert-info">Aucun article international disponible.</div>';
      document.getElementById('europe-container').innerHTML = '<div class="alert alert-info">Aucun article européen disponible.</div>';
      document.getElementById('afrique-container').innerHTML = '<div class="alert alert-info">Aucun article africain disponible.</div>';
      document.getElementById('americas-container').innerHTML = '<div class="alert alert-info">Aucun article américain disponible.</div>';
      document.getElementById('en-continu-container').innerHTML = '<div class="sidebar-item">Aucun article récent.</div>';
      return;
    }

    console.log('Article à la une:', allArticles[0]); // Debug

    // À la une (premier article)
    const aLaUne = allArticles[0];
    document.getElementById('a-la-une-container').innerHTML = createFeaturedArticle(aLaUne);

    // Dernières actualités (articles 1-3)
    const dernieres = allArticles.slice(1, 4);
    document.getElementById('dernieres-actualites-container').innerHTML = 
      dernieres.map(article => createArticleCard(article)).join('');

    // Plus d'actualités (articles 4-8)
    const plusDActualites = allArticles.slice(4, 9);
    document.getElementById('plus-actualites-container').innerHTML = 
      plusDActualites.map(article => createArticleListItem(article)).join('');

    // Articles par catégorie
    const international = await getArticlesByCategory('International');
    const europe = await getArticlesByCategory('Europe');
    const afrique = await getArticlesByCategory('Afrique');
    const americas = await getArticlesByCategory('Amériques');

    console.log('Articles International:', international.length); // Debug
    console.log('Articles Europe:', europe.length); // Debug

    document.getElementById('international-container').innerHTML = 
      international.slice(0, 3).map(article => createArticleCard(article)).join('') || 
      '<div class="alert alert-info">Aucun article international disponible.</div>';

    document.getElementById('europe-container').innerHTML = 
      europe.slice(0, 3).map(article => createArticleCard(article)).join('') || 
      '<div class="alert alert-info">Aucun article européen disponible.</div>';

    document.getElementById('afrique-container').innerHTML = 
      afrique.slice(0, 3).map(article => createArticleListItem(article)).join('') || 
      '<div class="alert alert-info">Aucun article africain disponible.</div>';

    document.getElementById('americas-container').innerHTML = 
      americas.slice(0, 3).map(article => createArticleListItem(article)).join('') || 
      '<div class="alert alert-info">Aucun article américain disponible.</div>';

    // En continu (6 derniers articles)
    const enContinu = allArticles.slice(0, 6);
    document.getElementById('en-continu-container').innerHTML = 
      enContinu.map(article => createEnContinuItem(article)).join('');

  } catch (error) {
    console.error('Erreur lors du chargement de l\'accueil:', error);
    document.querySelectorAll('.loading-message').forEach(el => {
      el.textContent = 'Erreur de chargement. Veuillez réessayer.';
    });
  }
}

// Charger l'accueil quand la page est prête
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadAccueil);
} else {
  loadAccueil();
}
</script>

<?php require_once "footer.php"; ?>
