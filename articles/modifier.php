<?php
$pageTitle = "Modifier un article";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Modifier un article</h1>
    <div class="actions">
      <a href="../accueil.php" class="btn btn-secondary btn-sm">← Retour à l'accueil</a>
    </div>
  </div>

  <div class="form-container" style="max-width:700px;">
    <form class="edit-article-form" enctype="multipart/form-data">
      <input type="hidden" name="id" class="article-id">
      <div class="form-group">
        <label>Titre *</label>
        <input class="form-control titre" type="text" name="titre" placeholder="Titre" required>
      </div>
      <div class="form-group">
        <label>Description *</label>
        <input class="form-control description" type="text" name="description" placeholder="Description courte" required>
      </div>
      <div class="form-group">
        <label>Contenu *</label>
        <textarea class="form-control contenu" name="contenu" placeholder="Contenu" required rows="8"></textarea>
      </div>
      <div class="form-group">
        <label>Catégorie *</label>
        <select class="form-control categorie_id" name="categorie_id" required>
          <option value="">--Choisir une catégorie--</option>
        </select>
      </div>
      <div class="form-group">
        <label>Image actuelle</label>
        <img class="current-image" src="" alt="" style="display:none; max-width:200px; margin-bottom:0.5rem;">
        <input class="form-control" type="file" name="image" accept="image/*">
      </div>
      <button type="submit" class="btn btn-primary">Modifier l'article</button>
    </form>
  </div>
</main>

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
