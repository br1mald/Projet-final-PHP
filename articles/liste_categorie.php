<?php
$pageTitle = "Liste des articles par catégorie";
require_once __DIR__ . "/../entete.php";
?>

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

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
