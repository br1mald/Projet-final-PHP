<?php
$termeRecherche = isset($_GET["q"]) ? trim($_GET["q"]) : "";
$pageTitle =
    $termeRecherche !== "" ? "Recherche : " . $termeRecherche : "Recherche";
require_once __DIR__ . "/../entete.php";
?>

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

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
