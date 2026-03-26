<?php
require_once __DIR__ . "/entete.php";

if (isset($_SESSION["role"])) {
    header("Location: accueil.php");
    exit();
}

$pageTitle = "Connexion";
?>

<main class="container">
  <div class="form-container">
    <h2>Connexion</h2>

    <form id="formConnexion" method="POST" novalidate>

      <div class="form-group">
        <label for="login">Login</label>
        <input
          type="text"
          id="login"
          name="login"
          placeholder="Votre identifiant"
          autocomplete="username"
        >
        <span class="error-msg" id="err-login">Le login est obligatoire.</span>
      </div>

      <div class="form-group">
        <label for="motdepasse">Mot de passe</label>
        <input
          type="password"
          id="motdepasse"
          name="motdepasse"
          placeholder="Votre mot de passe"
          autocomplete="current-password"
        >
        <span class="error-msg" id="err-motdepasse">Le mot de passe est obligatoire.</span>
      </div>

      <button type="submit" class="btn btn-primary btn-full">Se connecter</button>

    </form>
  </div>
</main>

<?php require_once __DIR__ . "/footer.php"; ?>

<script type="module" src="/final_project/static/js/auth.js"></script>
</body>
</html>
