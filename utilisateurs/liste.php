<?php
$pageTitle = "Utilisateurs";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Gestion des utilisateurs</h1>
    <div class="actions">
      <a href="ajouter.php" class="btn btn-primary btn-sm">+ Ajouter</a>
      <a href="supprimer.php" class="btn btn-danger btn-sm">Supprimer</a>
    </div>
  </div>
  <div class="utilisateurs-container"></div>
</main>

<script type="module" src="../static/js/utilisateurs.js"></script>
<?php require_once __DIR__ . "/../footer.php"; ?>
