<?php
$pageTitle = "Modifier une catégorie";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
if ($id <= 0) {
    header("Location: liste.php");
    exit();
}
?>

<main class="container">
  <div class="page-header">
    <h1>Modifier la catégorie</h1>
    <div class="actions">
      <a href="liste.php" class="btn btn-secondary btn-sm">← Retour</a>
    </div>
  </div>

  <div class="form-container" style="max-width:500px;">
    <div id="alertMsg"></div>
    <form id="editCategoryForm">
      <div class="form-group">
        <label for="nom">Nom *</label>
        <input id="nom" name="nom" type="text" placeholder="Nom de la catégorie" required minlength="3">
        <small class="help-text">Minimum 3 caractères.</small>
      </div>
      <input type="hidden" id="categoryId" value="<?= $id ?>">
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="liste.php" class="btn btn-secondary">Annuler</a>
      </div>
    </form>
  </div>
</main>

<script type="module" src="../static/js/categories.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
