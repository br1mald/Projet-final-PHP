<?php
$pageTitle = "Catégories";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Catégories</h1>
    <div class="actions">
      <a href="ajouter.php" class="btn btn-primary btn-sm">+ Ajouter</a>
      <a href="supprimer.php" class="btn btn-danger btn-sm">Supprimer</a>
    </div>
  </div>
  <div class="categories-container"></div>
</main>

<script type="module" src="../static/js/categories.js"></script>
<?php require_once __DIR__ . "/../footer.php"; ?>
