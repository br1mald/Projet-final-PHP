<?php
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'visiteur';
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$base = isset($base) ? $base : '';
?>

<nav class="site-nav">
  <ul>
    <li>
      <a href="<?= $base ?>accueil.php" class="<?= $currentPage === 'accueil.php' ? 'active' : '' ?>">Accueil</a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=toutes" class="<?= ($currentPage === 'liste_categorie.php' && ($_GET['categorie'] ?? '') === 'toutes') ? 'active' : '' ?>">Toute</a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=technologie" class="<?= ($_GET['categorie'] ?? '') === 'technologie' ? 'active' : '' ?>">Technologie</a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=sport" class="<?= ($_GET['categorie'] ?? '') === 'sport' ? 'active' : '' ?>">Sport</a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=politique" class="<?= ($_GET['categorie'] ?? '') === 'politique' ? 'active' : '' ?>">Politique</a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=education" class="<?= ($_GET['categorie'] ?? '') === 'education' ? 'active' : '' ?>">Éducation</a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=culture" class="<?= ($_GET['categorie'] ?? '') === 'culture' ? 'active' : '' ?>">Culture</a>
    </li>

    <?php if ($role === 'editeur' || $role === 'administrateur') : ?>
    <li>
      <a href="<?= $base ?>articles/ajouter.php" class="<?= $currentPage === 'ajouter.php' ? 'active' : '' ?>">+ Article</a>
    </li>
    <li>
      <a href="<?= $base ?>categories/liste.php" class="<?= ($currentPage === 'liste.php' && strpos($_SERVER['PHP_SELF'] ?? '', 'categories') !== false) ? 'active' : '' ?>">Catégories</a>
    </li>
    <?php endif; ?>

    <?php if ($role === 'administrateur') : ?>
    <li>
      <a href="<?= $base ?>utilisateurs/liste.php" class="<?= ($currentPage === 'liste.php' && strpos($_SERVER['PHP_SELF'] ?? '', 'utilisateurs') !== false) ? 'active' : '' ?>">Utilisateurs</a>
    </li>
    <?php endif; ?>

    <?php if ($role === 'visiteur') : ?>
    <li style="margin-left:auto;">
      <a href="<?= $base ?>connexion.php" class="btn-connexion">Connexion</a>
    </li>
    <?php else : ?>
    <li style="margin-left:auto;">
      <span style="padding:0.7rem 0.9rem; font-size:0.78rem; color:#555; font-family:'Barlow',sans-serif; font-weight:700; text-transform:uppercase;">
        <?= htmlspecialchars($_SESSION['prenom'] ?? '') ?>
      </span>
    </li>
    <li>
      <a href="<?= $base ?>deconnexion.php" class="btn-logout">Déconnexion</a>
    </li>
    <?php endif; ?>
  </ul>
</nav>
