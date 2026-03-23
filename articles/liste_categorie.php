<?php
session_start();

require_once __DIR__ . '/data_articles.php';

$categorieActive = isset($_GET['categorie']) ? $_GET['categorie'] : 'toutes';
$pageTitle = $categorieActive !== 'toutes' ? ucfirst($categorieActive) : "Toutes les catégories";
require_once __DIR__ . '/../entete.php';

// Images par sujet (fallback si article sans image)
$imagesParCategorie = [
  'technologie' => ['https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=600&h=300&fit=crop&q=80'],
  'sport' => ['https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=300&h=200&fit=crop&q=80'],
  'education' => ['https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=800&h=400&fit=crop&q=80', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&h=300&fit=crop&q=80'],
  'culture' => ['https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=600&h=300&fit=crop&q=80'],
  'politique' => ['https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1555848962-6e79363ec58f?w=600&h=300&fit=crop&q=80'],
  'international' => ['https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80'],
  'europe' => ['https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80'],
  'afrique' => ['https://images.unsplash.com/photo-1489392191049-fc10c97e64b6?w=600&h=300&fit=crop&q=80'],
  'ameriques' => ['https://images.unsplash.com/photo-1483736762161-1d107f3c78e1?w=600&h=300&fit=crop&q=80'],
  'toutes' => ['https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80', 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&h=300&fit=crop&q=80', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=300&h=200&fit=crop&q=80'],
];

$imgs = $imagesParCategorie[$categorieActive] ?? $imagesParCategorie['toutes'];

$articles = getArticles($categorieActive);

$parPage = 3;
$totalArticles = count($articles);
$totalPages = max(1, (int)ceil($totalArticles / $parPage));
$pageActuelle = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $totalPages)) : 1;
$debut = ($pageActuelle - 1) * $parPage;
$articlesPage = array_slice($articles, $debut, $parPage);

foreach ($articlesPage as $i => &$art) {
  if (empty($art['image'])) {
    $art['image'] = $imgs[($debut + $i) % count($imgs)];
  }
}
unset($art);

$vedette     = $articlesPage[0] ?? null;
$secondaires = array_slice($articlesPage, 1, 3);
$reste       = array_slice($articlesPage, 4);
?>

<main class="container">

  <div class="rubrique-header">
    <div class="trait"></div>
    <h2><?= $categorieActive !== 'toutes' ? ucfirst(htmlspecialchars($categorieActive)) : 'Toutes les actualités' ?></h2>
  </div>

  <?php if (empty($articles)) : ?>
    <div class="alert alert-info">Aucun article trouvé pour cette catégorie.</div>
  <?php else : ?>

    <?php if ($vedette) : ?>
      <div class="article-featured" style="margin-bottom:1.5rem;">
        <img class="featured-img" src="<?= htmlspecialchars($vedette['image']) ?>" alt="<?= htmlspecialchars($vedette['titre']) ?>">
        <div>
          <div class="featured-category"><?= ucfirst(htmlspecialchars($vedette['categorie'])) ?></div>
          <h2 class="featured-title">
            <a href="detail.php?id=<?= (int)$vedette['id'] ?>"><?= htmlspecialchars($vedette['titre']) ?></a>
          </h2>
          <p class="featured-excerpt"><?= htmlspecialchars($vedette['description']) ?></p>
          <div class="featured-meta"><?= htmlspecialchars($vedette['auteur']) ?> &nbsp;|&nbsp; <?= htmlspecialchars($vedette['date_publication']) ?></div>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($secondaires)) : ?>
      <div class="articles-grid-3" style="margin-bottom:1.5rem;">
        <?php foreach ($secondaires as $art) : ?>
          <article class="article-card-sm">
            <img class="card-img" src="<?= htmlspecialchars($art['image']) ?>" alt="<?= htmlspecialchars($art['titre']) ?>">
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
      <div class="section-title" style="margin-top:1.5rem;">Plus d'articles</div>
      <?php foreach ($reste as $art) : ?>
        <div class="article-list-item">
          <img class="list-img" src="<?= htmlspecialchars($art['image']) ?>" alt="<?= htmlspecialchars($art['titre']) ?>">
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

    <?php if ($totalPages > 1) : ?>
      <div class="pagination" style="display:flex; gap:0.75rem; margin-top:1.5rem; justify-content:center; align-items:center;">
        <?php if ($pageActuelle > 1) : ?>
          <a class="btn btn-secondary btn-sm" href="liste_categorie.php?categorie=<?= urlencode($categorieActive) ?>&page=<?= $pageActuelle - 1 ?>">← Précédent</a>
        <?php else : ?>
          <span class="btn btn-secondary btn-sm" style="opacity:0.5;">← Précédent</span>
        <?php endif; ?>
        <span>Page <?= $pageActuelle ?> / <?= $totalPages ?></span>
        <?php if ($pageActuelle < $totalPages) : ?>
          <a class="btn btn-secondary btn-sm" href="liste_categorie.php?categorie=<?= urlencode($categorieActive) ?>&page=<?= $pageActuelle + 1 ?>">Suivant →</a>
        <?php else : ?>
          <span class="btn btn-secondary btn-sm" style="opacity:0.5;">Suivant →</span>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../footer.php'; ?>
