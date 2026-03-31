<?php
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
if ($id <= 0) {
    header("Location: ../accueil.php");
    exit();
}

$pageTitle = "Article";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["visiteur", "editeur", "administrateur"]);
$canEdit = in_array($role, ["editeur", "administrateur"]);
?>

<script>
window.ARTICLE_ID = <?= $id ?>;
window.USER_CAN_EDIT = <?= $canEdit ? "true" : "false" ?>;
</script>

<main class="container">
  <div style="display:grid; grid-template-columns:1fr 280px; gap:2rem; background:#fff; padding:1.5rem;">
    <div>
      <div class="article-details"></div>
    </div>
    <aside class="sidebar" style="padding-left:1.5rem; border-left:1px solid var(--border-light);">
      <div class="sidebar-title">À lire aussi</div>
      <div id="article-similaires">
        <div class="loading-message">Chargement...</div>
      </div>
    </aside>
  </div>
</main>

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
