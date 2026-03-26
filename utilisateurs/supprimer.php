<?php
$pageTitle = "Supprimer un utilisateur";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Supprimer un utilisateur</h1>
    <div class="actions">
      <a href="liste.php" class="btn btn-secondary btn-sm">← Retour</a>
    </div>
  </div>

  <div class="delete-container">
    <div class="warning-message">
      <h3>⚠ Attention</h3>
      <p>La suppression d'un utilisateur est irréversible. Toutes les données associées seront perdues.</p>
    </div>
    <div id="deleteUsersList"></div>
    <p id="loadingUsers">Chargement…</p>
    <p id="errorUsers" style="display:none; color:var(--error);">Erreur lors du chargement.</p>
  </div>
</main>

<script type="module" src="../static/js/utilisateurs.js"></script>
<?php require_once __DIR__ . "/../footer.php"; ?>

