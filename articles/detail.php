<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ../accueil.php');
    exit;
}

$pageTitle = "Article";
require_once __DIR__ . '/../entete.php';
$userRole = $_SESSION['role'] ?? 'visiteur';
$canEdit = in_array($userRole, ['editeur', 'administrateur']);
?>

<main class="container">
  <div id="article-detail-wrapper" style="display:grid; grid-template-columns:1fr 280px; gap:2rem; background:#fff; padding:1.5rem;">
    <div id="article-main-col">
      <div id="article-loading">Chargement...</div>
      <div id="article-error" class="alert alert-error" style="display:none;"></div>
      <div id="article-details" class="article-details" style="display:none;"></div>
      <footer class="article-footer">
      </footer>
    </div>
    <aside class="sidebar" id="article-sidebar" style="padding-left:1.5rem; border-left:1px solid var(--border-light);">
      <div class="sidebar-title">En continu</div>
      <div id="article-en-continu">Chargement...</div>
      <div class="sidebar-title" style="margin-top:1.5rem;">À lire aussi</div>
      <div id="article-similaires"></div>
    </aside>
  </div>
</main>

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

// Fonction pour récupérer un article par ID
async function getArticle(id) {
  const data = await apiGet(`articles.php?action=search&id=${id}`);
  return data;
}

// Fonction pour formater la date en français
function formatDateFr(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
  return d.getDate() + ' ' + mois[d.getMonth()] + ' ' + d.getFullYear();
}

// Fonction pour formater l'heure
function formatTime(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
}

// Fonction pour échapper le HTML
function escapeHTML(str) {
  if (!str) return '';
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

// Fonction pour échapper les attributs
function escapeAttr(str) {
  if (!str) return '';
  return str.replace(/[&<>"']/g, function(match) {
    const escape = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    };
    return escape[match];
  });
}

window.ARTICLE_ID = <?= $id ?>;
window.USER_CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;
</script>
<script>
// Fonction principale pour afficher les détails de l'article
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

    const imgUrl = data.image_url || 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80';
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
        <div class="cat-badge" style="margin-bottom:0.8rem;">${escapeHTML(data.categorie || '')}</div>
        <h1 class="article-title">${escapeHTML(data.titre)}</h1>
        <div class="article-meta">
          <span>${escapeHTML(data.auteur || '')}</span>
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
      const similaires = allData.filter(a => a.categorie === data.categorie && a.id !== data.id).slice(0, 3);
      if (similaires.length > 0) {
        const simHtml = similaires.map(s => {
          const sImg = s.image_url || imgUrl;
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

// Charger l'article au chargement de la page
if (window.ARTICLE_ID) {
  renderArticleDetails(window.ARTICLE_ID);
}
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>
