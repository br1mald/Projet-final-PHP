<?php
$pageTitle = "Ajouter une catégorie";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Créer une catégorie</h1>
    <div class="actions">
      <a href="liste.php" class="btn btn-secondary btn-sm">← Retour</a>
    </div>
  </div>

  <div class="form-container" style="max-width:500px;">
    <form action="../api/categories.php" method="post">
      <div class="form-group">
        <label>Nom *</label>
        <input class="form-control" name="nom" type="text" placeholder="Nom de la catégorie" required>
      </div>
      <button type="submit" class="btn btn-primary">Créer la catégorie</button>
    </form>
  </div>
</main>

<script type="module" src="../static/js/categories.js"></script>
<?php require_once __DIR__ . "/../footer.php"; ?>
