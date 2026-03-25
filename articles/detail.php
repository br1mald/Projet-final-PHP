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
window.ARTICLE_ID = <?= $id ?>;
window.USER_CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;
</script>
<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . '/../footer.php'; ?>
