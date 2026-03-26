<?php
$pageTitle = "Supprimer une catégorie";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Supprimer une catégorie</h1>
    <div class="actions">
      <a href="liste.php" class="btn btn-secondary btn-sm">← Retour</a>
    </div>
  </div>

  <div class="delete-container">
    <div class="warning-message">
      <h3>⚠ Attention</h3>
      <p>La suppression d'une catégorie est irréversible.</p>
    </div>
    <div id="deleteCategoriesList"></div>
    <p id="loading">Chargement…</p>
    <p id="errorMessage" style="display:none; color:var(--error);">Erreur lors du chargement.</p>
  </div>
</main>

<script type="module" src="../static/js/categories.js"></script>
<?php require_once __DIR__ . "/../footer.php"; ?>

