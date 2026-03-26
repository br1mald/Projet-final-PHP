<?php
$pageTitle = "Liste des articles par catégorie";
require_once __DIR__ . '/../entete.php';
?>

<script>
const IMAGES_PAR_CATEGORIE = {
  'technologie': ['https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=600&h=300&fit=crop&q=80'],
  'sport': ['https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=300&h=200&fit=crop&q=80'],
  'education': ['https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=800&h=400&fit=crop&q=80', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&h=300&fit=crop&q=80'],
  'culture': ['https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=600&h=300&fit=crop&q=80'],
  'politique': ['https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1555848962-6e79363ec58f?w=600&h=300&fit=crop&q=80'],
  'toutes': ['https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80', 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=300&h=200&fit=crop&q=80']
};
</script>

<main class="container">
  <div class="page-header">
    <h1 id="page-title">Toutes les catégories</h1>
    <div class="breadcrumb">
      <a href="../accueil.php">Accueil</a> &gt; <span id="breadcrumb-category">Toutes les catégories</span>
    </div>
  </div>

  <div id="articles-container" class="articles-grid">
    <div class="loading-message">Chargement des articles...</div>
  </div>

  <div id="pagination-container" class="pagination"></div>
</main>

<style>
.articles-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:1.5rem; margin-bottom:2rem; }
.article-card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.08); transition:transform .3s ease; }
.article-card:hover { transform:translateY(-4px); }
.article-card img { width:100%; height:200px; object-fit:cover; }
.article-card-content { padding:1.5rem; }
.article-category { color:var(--accent); font-size:.8rem; font-weight:600; margin-bottom:.5rem; }
.article-title { margin:.5rem 0; font-size:1.1rem; line-height:1.3; }
.article-title a { color:var(--text); text-decoration:none; }
.article-title a:hover { color:var(--accent); }
.article-excerpt { color:var(--muted); margin:1rem 0; line-height:1.5; }
.article-meta { color:var(--muted); font-size:.85rem; display:flex; justify-content:space-between; align-items:center; }
.pagination { display:flex; justify-content:center; gap:.5rem; margin-top:2rem; }
.pagination a,.pagination span { padding:.5rem 1rem; background:#fff; border:1px solid var(--border-light); border-radius:6px; text-decoration:none; color:var(--text); transition:all .3s ease; }
.pagination a:hover { background:var(--accent); color:var(--primary); }
.pagination .active { background:var(--accent); color:var(--primary); border-color:var(--accent); }
.loading-message { text-align:center; color:var(--muted); padding:3rem; font-style:italic; grid-column:1/-1; }
.page-header { margin-bottom:2rem; }
.page-header h1 { color:var(--text); margin-bottom:.5rem; }
.breadcrumb { color:var(--muted); font-size:.9rem; }
.breadcrumb a { color:var(--accent); text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }
@media(max-width:768px){.articles-grid{grid-template-columns:1fr;}}
</style>

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

function getCategorieActive() {
  const params = new URLSearchParams(window.location.search);
  return params.get('categorie') || 'toutes';
}

async function getAllArticles() {
  try {
    const data = await apiGet('articles.php?action=all');
    return Array.isArray(data) ? data : [];
  } catch (error) {
    console.error('Erreur lors de la récupération des articles:', error);
    return [];
  }
}

async function getAllCategories() {
  try {
    const data = await apiGet('categories.php?action=all');
    return Array.isArray(data) ? data : [];
  } catch (error) {
    return [];
  }
}

async function filterArticlesByCategory(articles, categorie) {
  if (categorie === 'toutes') return articles;
  const categories = await getAllCategories();
  const targetCategory = categories.find(cat =>
    cat.nom.toLowerCase() === categorie.toLowerCase() ||
    cat.nom.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '') === categorie.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
  );
  if (!targetCategory) {
    return articles.filter(article => {
      const articleCategorie = article.categorie ? article.categorie.toLowerCase() : '';
      const targetCategorie = categorie.toLowerCase();
      return articleCategorie.includes(targetCategorie) || targetCategorie.includes(articleCategorie);
    });
  }
  return articles.filter(article => {
    const articleCategorie = article.categorie ? article.categorie.toLowerCase() : '';
    return articleCategorie === targetCategory.nom.toLowerCase();
  });
}

function formatDateFr(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
  return d.getDate() + ' ' + mois[d.getMonth()] + ' ' + d.getFullYear();
}

function getRandomImage(categorie) {
  const images = IMAGES_PAR_CATEGORIE[categorie] || IMAGES_PAR_CATEGORIE['toutes'];
  return images[Math.floor(Math.random() * images.length)];
}

function escapeHTML(str) {
  if (!str) return '';
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

function createArticleCard(article) {
  const img = article.image_url || getRandomImage(article.categorie ? article.categorie.toLowerCase() : 'toutes');
  return `<article class="article-card">
    <img src="${img}" alt="${escapeHTML(article.titre)}">
    <div class="article-card-content">
      <div class="article-category">${escapeHTML(article.categorie || 'Non classé')}</div>
      <h3 class="article-title"><a href="detail.php?id=${article.id}">${escapeHTML(article.titre)}</a></h3>
      <p class="article-excerpt">${escapeHTML(article.description || '')}</p>
      <div class="article-meta">
        <span>${escapeHTML(article.auteur || 'Anonyme')}</span>
        <span>${formatDateFr(article.date_publication)}</span>
      </div>
    </div>
  </article>`;
}

function createPagination(currentPage, totalPages, categorie) {
  if (totalPages <= 1) return '';
  let pagination = '';
  if (currentPage > 1) pagination += `<a href="?categorie=${categorie}&page=${currentPage - 1}">«</a>`;
  for (let i = 1; i <= totalPages; i++) {
    pagination += i === currentPage ? `<span class="active">${i}</span>` : `<a href="?categorie=${categorie}&page=${i}">${i}</a>`;
  }
  if (currentPage < totalPages) pagination += `<a href="?categorie=${categorie}&page=${currentPage + 1}">»</a>`;
  return pagination;
}

function updateUI(categorie) {
  const title = categorie === 'toutes' ? 'Toutes les catégories' : categorie.charAt(0).toUpperCase() + categorie.slice(1);
  document.getElementById('page-title').textContent = title;
  document.getElementById('breadcrumb-category').textContent = title;
}

async function loadArticles() {
  try {
    const categorie = getCategorieActive();
    const params = new URLSearchParams(window.location.search);
    const currentPage = parseInt(params.get('page')) || 1;
    const parPage = 3;
    updateUI(categorie);
    const allArticles = await getAllArticles();
    const filteredArticles = await filterArticlesByCategory(allArticles, categorie);
    const startIndex = (currentPage - 1) * parPage;
    const paginatedArticles = filteredArticles.slice(startIndex, startIndex + parPage);
    const totalPages = Math.ceil(filteredArticles.length / parPage);
    const container = document.getElementById('articles-container');
    if (paginatedArticles.length === 0) {
      container.innerHTML = '<div class="loading-message">Aucun article trouvé dans cette catégorie.</div>';
    } else {
      container.innerHTML = paginatedArticles.map(article => createArticleCard(article)).join('');
    }
    document.getElementById('pagination-container').innerHTML = createPagination(currentPage, totalPages, categorie);
  } catch (error) {
    console.error('Erreur lors du chargement des articles:', error);
    document.getElementById('articles-container').innerHTML = '<div class="loading-message">Erreur de chargement. Veuillez réessayer.</div>';
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadArticles);
} else {
  loadArticles();
}
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>
