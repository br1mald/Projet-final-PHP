<?php
session_start();

if (isset($_SESSION['role'])) {
    header('Location: accueil.php');
    exit;
}

$pageTitle = "Connexion";
require_once __DIR__ . '/entete.php';
?>

<main class="container">
  <div class="form-container">
    <h2>Connexion</h2>

    <?php if (isset($_SESSION['erreur_connexion'])) : ?>
      <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['erreur_connexion']) ?>
      </div>
      <?php unset($_SESSION['erreur_connexion']); ?>
    <?php endif; ?>

    <form id="formConnexion" action="connexion_traitement.php" method="POST" novalidate>

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

<?php require_once __DIR__ . '/footer.php'; ?>

<script>
document.getElementById('formConnexion').addEventListener('submit', function(e) {
  let valide = true;

  const login = document.getElementById('login');
  const errLogin = document.getElementById('err-login');
  if (login.value.trim() === '') {
    login.classList.add('input-error');
    errLogin.classList.add('visible');
    valide = false;
  } else {
    login.classList.remove('input-error');
    errLogin.classList.remove('visible');
  }

  const mdp = document.getElementById('motdepasse');
  const errMdp = document.getElementById('err-motdepasse');
  if (mdp.value.trim() === '') {
    mdp.classList.add('input-error');
    errMdp.classList.add('visible');
    valide = false;
  } else {
    mdp.classList.remove('input-error');
    errMdp.classList.remove('visible');
  }

  if (!valide) e.preventDefault();
});

document.getElementById('login').addEventListener('input', function() {
  this.classList.remove('input-error');
  document.getElementById('err-login').classList.remove('visible');
});
document.getElementById('motdepasse').addEventListener('input', function() {
  this.classList.remove('input-error');
  document.getElementById('err-motdepasse').classList.remove('visible');
});
</script>

</body>
</html>
