<?php
$pageTitle = "Supprimer un article";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Supprimer un article</h1>
    <div class="actions">
      <a href="../accueil.php" class="btn btn-secondary btn-sm">← Retour à l'accueil</a>
    </div>
  </div>

  <div class="delete-form-container"></div>
</main>

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
