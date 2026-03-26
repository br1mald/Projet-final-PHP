<?php
$pageTitle = "Modifier des catégories";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Modifier une catégorie</h1>
    <div class="actions">
      <a href="liste.php" class="btn btn-secondary btn-sm">← Retour</a>
    </div>
  </div>
  <div class="patch-form-container"></div>
</main>

<script type="module" src="../static/js/categories.js"></script>
<?php require_once __DIR__ . "/../footer.php"; ?>
