<?php
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'visiteur';
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$base = isset($base) ? $base : '';

// Récupérer le nombre d'articles par catégorie
$category_counts = [];
if (file_exists(__DIR__ . '/includes/db.php')) {
    require_once __DIR__ . '/includes/db.php';
    try {
        $stmt = $pdo->query("SELECT c.id, c.nom, COUNT(a.id) as article_count FROM categories c LEFT JOIN articles a ON c.id = a.categorie_id GROUP BY c.id, c.nom ORDER BY c.nom");
        $category_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Créer un tableau associatif pour un accès facile
        $counts_by_name = [];
        $total_articles = 0;
        foreach ($category_counts as $count) {
            $counts_by_name[strtolower($count['nom'])] = $count['article_count'];
            $total_articles += $count['article_count'];
        }
        $category_counts = $counts_by_name;
    } catch (Exception $e) {
        $category_counts = [];
        $total_articles = 0;
    }
}
?>

<nav class="site-nav">
  <ul>
    <li>
      <a href="<?= $base ?>accueil.php" class="<?= $currentPage === 'accueil.php' ? 'active' : '' ?>">Accueil</a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=toutes" class="<?= ($currentPage === 'liste_categorie.php' && ($_GET['categorie'] ?? '') === 'toutes') ? 'active' : '' ?>">
        Toute
        <?php if (isset($total_articles)): ?>
          <span class="category-count"><?= $total_articles ?></span>
        <?php endif; ?>
      </a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=technologie" class="<?= ($_GET['categorie'] ?? '') === 'technologie' ? 'active' : '' ?>">
        Technologie
        <?php if (isset($category_counts['technologie'])): ?>
          <span class="category-count"><?= $category_counts['technologie'] ?></span>
        <?php endif; ?>
      </a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=sport" class="<?= ($_GET['categorie'] ?? '') === 'sport' ? 'active' : '' ?>">
        Sport
        <?php if (isset($category_counts['sport'])): ?>
          <span class="category-count"><?= $category_counts['sport'] ?></span>
        <?php endif; ?>
      </a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=politique" class="<?= ($_GET['categorie'] ?? '') === 'politique' ? 'active' : '' ?>">
        Politique
        <?php if (isset($category_counts['politique'])): ?>
          <span class="category-count"><?= $category_counts['politique'] ?></span>
        <?php endif; ?>
      </a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=education" class="<?= ($_GET['categorie'] ?? '') === 'education' ? 'active' : '' ?>">
        Éducation
        <?php if (isset($category_counts['education'])): ?>
          <span class="category-count"><?= $category_counts['education'] ?></span>
        <?php endif; ?>
      </a>
    </li>
    <li>
      <a href="<?= $base ?>articles/liste_categorie.php?categorie=culture" class="<?= ($_GET['categorie'] ?? '') === 'culture' ? 'active' : '' ?>">
        Culture
        <?php if (isset($category_counts['culture'])): ?>
          <span class="category-count"><?= $category_counts['culture'] ?></span>
        <?php endif; ?>
      </a>
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
