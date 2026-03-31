<?php
$pageTitle = "Accueil";
require_once __DIR__ . "/entete.php";
require_once __DIR__ . "/includes/auth.php";

$role = get_role();
check_role($role, ["visiteur", "editeur", "administrateur"]);
?>

<main class="container">
  <?php if (isset($_SESSION["message"])): ?>
    <div class="alert alert-<?= htmlspecialchars(
        $_SESSION["type_message"] ?? "info",
    ) ?>">
      <?= htmlspecialchars($_SESSION["message"]) ?>
    </div>
    <?php unset($_SESSION["message"], $_SESSION["type_message"]); ?>
  <?php endif; ?>

  <div class="main-layout">
    <div class="main-content">
      <div class="section-title">À la une</div>
      <div id="a-la-une-container">
        <div class="loading-message">Chargement de l'article à la une...</div>
      </div>

      <div class="section-title">Dernières actualités</div>
      <div id="dernieres-actualites-container" class="articles-grid-3">
        <div class="loading-message">Chargement des dernières actualités...</div>
      </div>

      <div class="section-title">Plus d'actualités</div>
      <div id="plus-actualites-container" class="article-list">
        <div class="loading-message">Chargement des actualités...</div>
      </div>
    </div>

    <aside class="sidebar">
      <div class="sidebar-title">En continu</div>
      <div id="en-continu-container">
        <div class="loading-message">Chargement...</div>
      </div>
    </aside>
  </div>
</main>

<script type="module" src="static/js/articles.js"></script>

<?php require_once "footer.php"; ?>
