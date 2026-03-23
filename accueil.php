<?php
$pageTitle = "Accueil";
require_once "entete.php";
require_once "articles/data_articles.php";

// Image par défaut si l'article n'en a pas
define('IMG_DEFAULT', 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80');

// Récupération des articles depuis la BDD
$tousArticles = getArticles('toutes');
$alaUne = $tousArticles[0] ?? null;
$dernieres = array_slice($tousArticles, 1, 3);
$plusDActualites = array_slice($tousArticles, 4, 8);
$articlesInternational = getArticles('International');
$articlesEurope = getArticles('Europe');
$articlesAfrique = getArticles('Afrique');
$articlesAmericques = getArticles('Amériques');

function img($art) {
  return !empty($art['image']) ? htmlspecialchars($art['image']) : IMG_DEFAULT;
}
?>

<main class="container">
  <?php if (isset($_SESSION['message'])) : ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['type_message'] ?? 'info') ?>">
      <?= htmlspecialchars($_SESSION['message']) ?>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['type_message']); ?>
  <?php endif; ?>

  <div class="main-layout">
    <div class="main-content">

      <!-- À la une -->
      <div class="section-title">À la une</div>
      <?php if ($alaUne) : ?>
      <div class="article-featured">
        <img class="featured-img" src="<?= img($alaUne) ?>" alt="<?= htmlspecialchars($alaUne['titre']) ?>">
        <div>
          <div class="featured-category"><?= ucfirst(htmlspecialchars($alaUne['categorie'])) ?></div>
          <h2 class="featured-title">
            <a href="articles/detail.php?id=<?= (int)$alaUne['id'] ?>"><?= htmlspecialchars($alaUne['titre']) ?></a>
          </h2>
          <p class="featured-excerpt"><?= htmlspecialchars($alaUne['description']) ?></p>
          <div class="featured-meta"><?= htmlspecialchars($alaUne['auteur']) ?> &nbsp;|&nbsp; <?= htmlspecialchars($alaUne['date_publication']) ?></div>
        </div>
      </div>
      <?php else : ?>
      <div class="alert alert-info">Aucun article à la une pour le moment.</div>
      <?php endif; ?>

      <!-- Dernières actualités — 3 colonnes -->
      <div class="section-title">Dernières actualités</div>
      <div class="articles-grid-3">
        <?php foreach ($dernieres as $art) : ?>
        <article class="article-card-sm">
          <img class="card-img" src="<?= img($art) ?>" alt="<?= htmlspecialchars($art['titre']) ?>">
          <div class="card-category"><?= ucfirst(htmlspecialchars($art['categorie'])) ?></div>
          <h3 class="card-title"><a href="articles/detail.php?id=<?= (int)$art['id'] ?>"><?= htmlspecialchars($art['titre']) ?></a></h3>
          <div class="card-meta"><?= htmlspecialchars($art['description']) ?></div>
          <div class="card-meta"><?= htmlspecialchars($art['date_publication']) ?></div>
        </article>
        <?php endforeach; ?>
      </div>

      <!-- Plus d'actualités — liste horizontale -->
      <?php if (!empty($plusDActualites)) : ?>
      <div class="section-title">Plus d'actualités</div>
      <?php foreach ($plusDActualites as $art) : ?>
      <div class="article-list-item">
        <img class="list-img" src="<?= img($art) ?>" alt="<?= htmlspecialchars($art['titre']) ?>">
        <div>
          <div class="list-category"><?= ucfirst(htmlspecialchars($art['categorie'])) ?></div>
          <h3 class="list-title"><a href="articles/detail.php?id=<?= (int)$art['id'] ?>"><?= htmlspecialchars($art['titre']) ?></a></h3>
          <div class="list-meta"><?= htmlspecialchars($art['description']) ?></div>
          <div class="list-meta"><?= htmlspecialchars($art['date_publication']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>

    </div>

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="sidebar-title">En continu</div>
      <?php
      $sidebarItems = [
        ['time'=>'21:40', 'titre'=>'Ligue des champions', 'scores'=>['FC Barcelone - Newcastle 7 - 2','Liverpool - Galatasaray 4 - 0','Bayern Munich - Atalanta 4 - 1','Tottenham - Atlético Madrid 3 - 2'], 'cat'=>''],
        ['time'=>'20:58', 'titre'=>'Croissance africaine 2026', 'scores'=>[], 'cat'=>'La croissance économique devrait être à 4,0 % en 2026.'],
        ['time'=>'20:12', 'titre'=>'Mort de Abdoulaye Ba', 'scores'=>[], 'cat'=>'Le décès de Abdoulaye Ba, étudiant en 2e année de médecine (UCAD).'],
        ['time'=>'19:35', 'titre'=>'Bourses des étudiants au Sénégal', 'scores'=>[], 'cat'=>'Le système des bourses traverse une période de fortes tensions en 2026.'],
      ];
      foreach ($sidebarItems as $item) : ?>
        <div class="sidebar-item">
          <div class="sidebar-time"><?= htmlspecialchars($item['time']) ?></div>
          <div class="sidebar-item-title">
            <a href="#"><?= htmlspecialchars($item['titre']) ?></a>
            <?php if (!empty($item['scores'])) : ?>
              <div style="margin-top:0.3rem;">
                <?php foreach ($item['scores'] as $score) : ?>
                  <div style="font-size:0.75rem; color:#555; font-family:'Barlow',sans-serif;"><?= htmlspecialchars($score) ?></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
          <?php if (!empty($item['cat'])) : ?>
            <div class="sidebar-cat"><?= htmlspecialchars($item['cat']) ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </aside>
  </div>

  <!-- RUBRIQUE : ACTUALITÉS (International) -->
  <?php if (!empty($articlesInternational)) : ?>
  <div class="rubrique-section">
    <div class="rubrique-header">
      <div class="trait"></div>
      <h1>Actualités</h1>
      <a href="articles/liste_categorie.php">›</a>
    </div>
    <?php
    $intFeatured = $articlesInternational[0] ?? null;
    $intCards = array_slice($articlesInternational, 1, 3);
    ?>
    <?php if ($intFeatured) : ?>
    <div class="rubrique-featured">
      <img class="rf-img" src="<?= img($intFeatured) ?>" alt="<?= htmlspecialchars($intFeatured['titre']) ?>">
      <div class="rf-body">
        <div class="rf-category">Analyse &nbsp;■&nbsp; International</div>
        <h3 class="rf-title"><a href="articles/detail.php?id=<?= (int)$intFeatured['id'] ?>" style="color:inherit;"><?= htmlspecialchars($intFeatured['titre']) ?></a></h3>
        <p class="rf-excerpt"><?= htmlspecialchars($intFeatured['description']) ?></p>
        <div class="rf-meta"><?= htmlspecialchars($intFeatured['auteur']) ?> &nbsp;|&nbsp; <?= htmlspecialchars($intFeatured['date_publication']) ?></div>
      </div>
    </div>
    <?php endif; ?>
    <div class="rubrique-grid-3">
      <?php foreach ($intCards as $art) : ?>
      <div class="rubrique-card">
        <a href="articles/detail.php?id=<?= (int)$art['id'] ?>">
          <img class="rc-img" src="<?= img($art) ?>" alt="<?= htmlspecialchars($art['titre']) ?>">
          <div class="rc-body">
            <div class="rc-category">International</div>
            <h3 class="rc-title"><?= htmlspecialchars($art['titre']) ?></h3>
            <p style="font-size:0.95rem; color:#555; padding:0 0.7rem 0.7rem;"><?= htmlspecialchars($art['description']) ?></p>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- RUBRIQUE : EUROPE -->
  <?php if (!empty($articlesEurope)) : ?>
  <div class="rubrique-section">
    <div class="rubrique-header">
      <div class="trait"></div>
      <h1>Europe</h1>
    </div>
    <?php
    $euFeatured = $articlesEurope[0] ?? null;
    $euListe = array_slice($articlesEurope, 1, 3);
    ?>
    <div class="rubrique-split">
      <?php if ($euFeatured) : ?>
      <div class="rs-left">
        <a href="articles/detail.php?id=<?= (int)$euFeatured['id'] ?>">
          <img src="<?= img($euFeatured) ?>" alt="<?= htmlspecialchars($euFeatured['titre']) ?>" style="width:100%;height:150px;object-fit:cover;margin-bottom:0.8rem;">
          <h3 class="rs-title"><?= htmlspecialchars($euFeatured['titre']) ?></h3>
        </a>
        <div class="rs-meta">Europe</div>
      </div>
      <?php endif; ?>
      <div class="rs-right">
        <?php foreach ($euListe as $art) : ?>
        <div class="rs-item">
          <a href="articles/detail.php?id=<?= (int)$art['id'] ?>">
            <div class="rs-item-inner">
              <img src="<?= img($art) ?>" alt="<?= htmlspecialchars($art['titre']) ?>" style="width:80px;height:55px;object-fit:cover;">
              <div>
                <div class="rs-item-title"><?= htmlspecialchars($art['titre']) ?></div>
                <div class="rs-item-cat">Europe</div>
              </div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- RUBRIQUES : AFRIQUE + AMÉRIQUES -->
  <?php if (!empty($articlesAfrique) || !empty($articlesAmericques)) : ?>
  <div class="rubrique-duo">
    <?php if (!empty($articlesAfrique)) : ?>
    <div class="rubrique-duo-col">
      <div class="rubrique-duo-header">
        <div class="trait"></div>
        <h1>Afrique</h1>
        <a href="articles/liste_categorie.php?categorie=afrique">›</a>
      </div>
      <div class="rubrique-duo-body">
        <?php foreach (array_slice($articlesAfrique, 0, 4) as $i => $art) : ?>
        <div class="rubrique-duo-item">
          <?php if ($i < 2) : ?><div class="di-note"><?= $i === 0 ? "Journal de l'Afrique" : "On va plus loin" ?></div><?php endif; ?>
          <div class="di-title"><a href="articles/detail.php?id=<?= (int)$art['id'] ?>"><?= htmlspecialchars($art['titre']) ?></a></div>
          <div class="di-meta">Afrique</div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
    <?php if (!empty($articlesAmericques)) : ?>
    <div class="rubrique-duo-col">
      <div class="rubrique-duo-header">
        <div class="trait"></div>
        <h1>Amériques</h1>
        <a href="articles/liste_categorie.php?categorie=ameriques">›</a>
      </div>
      <div class="rubrique-duo-body">
        <?php foreach (array_slice($articlesAmericques, 0, 4) as $art) : ?>
        <div class="rubrique-duo-item">
          <div class="di-title"><a href="articles/detail.php?id=<?= (int)$art['id'] ?>"><?= htmlspecialchars($art['titre']) ?></a></div>
          <div class="di-meta">Amériques</div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</main>

<?php require_once "footer.php"; ?>
