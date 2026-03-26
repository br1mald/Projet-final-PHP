<?php
$pageTitle = "Ajouter un utilisateur";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["administrateur"]);
?>

<main class="container">
  <div class="page-header">
    <h1>Créer un utilisateur</h1>
    <div class="actions">
      <a href="liste.php" class="btn btn-secondary btn-sm">← Retour</a>
    </div>
  </div>

  <div class="form-container" style="max-width:500px;">
    <form action="../api/utilisateurs.php" method="post">
      <div class="form-group">
        <label>Nom *</label>
        <input class="form-control" name="nom" type="text" placeholder="Nom" required>
      </div>
      <div class="form-group">
        <label>Prénom *</label>
        <input class="form-control" name="prenom" type="text" placeholder="Prénom" required>
      </div>
      <div class="form-group">
        <label>Login *</label>
        <input class="form-control" name="login" type="text" placeholder="Login" required>
      </div>
      <div class="form-group">
        <label>Mot de passe *</label>
        <input class="form-control" name="password" type="password" required>
      </div>
      <div class="form-group">
        <label>Rôle *</label>
        <select class="form-control" name="userRole">
          <option value="editeur">Éditeur</option>
          <option value="administrateur">Administrateur</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
    </form>
  </div>
</main>

<script type="module" src="../static/js/utilisateurs.js"></script>
<?php require_once __DIR__ . "/../footer.php"; ?>
