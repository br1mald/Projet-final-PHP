<?php
session_start();
require_once __DIR__ . '/data_articles.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$pageTitle = $q !== '' ? 'Recherche : ' . $q : 'Recherche';
require_once __DIR__ . '/../entete.php';

define('IMG_DEFAULT', 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80');

$resultats = [];
if ($q !== '') {
  $qLower = strtolower($q);
  foreach (getArticles('toutes') as $article) {
    if (
      str_contains(strtolower($article['titre']), $qLower) ||
      str_contains(strtolower($article['description'] ?? ''), $qLower) ||
      str_contains(strtolower($article['contenu'] ?? ''), $qLower) ||
      str_contains(strtolower($article['categorie'] ?? ''), $qLower)
    ) {
      $resultats[] = $article;
    }
  }
}

function imgSearch($art) {
  return !empty($art['image']) ? htmlspecialchars($art['image']) : IMG_DEFAULT;
}
?>

<main class="container" style="background:#fff; padding:1.5rem;">

  <?php if ($q === '') : ?>
    <div class="alert alert-info">Entrez un mot clé pour rechercher un article.</div>

  <?php elseif (empty($resultats)) : ?>
    <div class="rubrique-header">
      <div class="trait"></div>
      <h2>Recherche : "<?= htmlspecialchars($q) ?>"</h2>
    </div>
    <div class="alert alert-error">
      Aucun article trouvé pour "<strong><?= htmlspecialchars($q) ?></strong>".
    </div>

  <?php else : ?>
    <div class="rubrique-header">
      <div class="trait"></div>
      <h2><?= count($resultats) ?> résultat(s) pour "<?= htmlspecialchars($q) ?>"</h2>
    </div>

    <?php
    $vedette     = $resultats[0];
    $secondaires = array_slice($resultats, 1, 3);
    $reste       = array_slice($resultats, 4);
    ?>

    <div class="article-featured" style="margin-bottom:1.5rem;">
      <img class="featured-img" src="<?= imgSearch($vedette) ?>" alt="<?= htmlspecialchars($vedette['titre']) ?>">
      <div>
        <div class="featured-category"><?= ucfirst(htmlspecialchars($vedette['categorie'])) ?></div>
        <h2 class="featured-title">
          <a href="detail.php?id=<?= (int)$vedette['id'] ?>"><?= htmlspecialchars($vedette['titre']) ?></a>
        </h2>
        <p class="featured-excerpt"><?= htmlspecialchars($vedette['description'] ?? '') ?></p>
        <div class="featured-meta">
          <?= htmlspecialchars($vedette['auteur']) ?> &nbsp;|&nbsp;
          <?= htmlspecialchars($vedette['date_publication']) ?>
        </div>
      </div>
    </div>

    <?php if (!empty($secondaires)) : ?>
      <div class="articles-grid-3" style="margin-bottom:1.5rem;">
        <?php foreach ($secondaires as $art) : ?>
          <article class="article-card-sm">
            <img class="card-img" src="<?= imgSearch($art) ?>" alt="<?= htmlspecialchars($art['titre']) ?>">
            <div class="card-category"><?= ucfirst(htmlspecialchars($art['categorie'])) ?></div>
            <h3 class="card-title">
              <a href="detail.php?id=<?= (int)$art['id'] ?>"><?= htmlspecialchars($art['titre']) ?></a>
            </h3>
            <div class="card-meta"><?= htmlspecialchars($art['auteur']) ?> — <?= htmlspecialchars($art['date_publication']) ?></div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($reste)) : ?>
      <div class="section-title">Plus de résultats</div>
      <?php foreach ($reste as $art) : ?>
        <div class="article-list-item">
          <img class="list-img" src="<?= imgSearch($art) ?>" alt="<?= htmlspecialchars($art['titre']) ?>">
          <div>
            <div class="list-category"><?= ucfirst(htmlspecialchars($art['categorie'])) ?></div>
            <h3 class="list-title">
              <a href="detail.php?id=<?= (int)$art['id'] ?>"><?= htmlspecialchars($art['titre']) ?></a>
            </h3>
            <div class="list-meta"><?= htmlspecialchars($art['auteur']) ?> — <?= htmlspecialchars($art['date_publication']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../footer.php'; ?>
