<?php
$pageTitle = "Ajouter un article";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Créer un article</h1>
    <div class="actions">
      <a href="../accueil.php" class="btn btn-secondary btn-sm">← Retour à l'accueil</a>
    </div>
  </div>

  <div class="form-container" style="max-width:700px;">
    <form class="post-form" enctype="multipart/form-data">
      <div class="form-group">
        <label>Titre *</label>
        <input class="form-control" name="title" type="text" placeholder="Titre" required>
      </div>
      <div class="form-group">
        <label>Description *</label>
        <input class="form-control" name="description" type="text" placeholder="Description courte" required>
      </div>
      <div class="form-group">
        <label>Contenu *</label>
        <textarea class="form-control" name="content" placeholder="Contenu" required rows="8"></textarea>
      </div>
      <div class="form-group">
        <label>Catégorie *</label>
        <select class="form-control form-select-field" name="category" required>
          <option value="">--Veuillez choisir une catégorie--</option>
        </select>
      </div>
      <div class="form-group">
        <label>Image</label>
        <input class="form-control" type="file" name="image" accept="image/*">
      </div>
      <div class="form-group">
        <label>Date de publication</label>
        <input class="form-control current-date" type="text" name="date_publication" readonly>
      </div>
      <button type="submit" class="btn btn-primary">Publier l'article</button>
    </form>
  </div>
</main>

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
