<?php
session_start();
require_once __DIR__ . '/data_articles.php';

$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = getArticleById($id);

if (!$article) {
  http_response_code(404);
  $pageTitle = 'Article non trouvé';
  require_once __DIR__ . '/../entete.php';
  echo '<main class="container"><div class="alert alert-error" style="margin:2rem 0;">Article introuvable.</div><a href="../accueil.php" class="btn btn-secondary btn-sm">← Retour à l\'accueil</a></main>';
  require_once __DIR__ . '/../footer.php';
  exit;
}

$pageTitle = $article['titre'];
require_once __DIR__ . '/../entete.php';

$articlesSimilaires = array_slice(
  array_filter(getArticles($article['categorie']), fn($a) => $a['id'] !== $article['id']),
  0, 3
);

define('IMG_DEFAULT', 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80');
$imgArt = !empty($article['image']) ? $article['image'] : IMG_DEFAULT;
?>

<main class="container">
  <div style="display:grid; grid-template-columns:1fr 280px; gap:2rem; background:#fff; padding:1.5rem;">

    <article class="article-detail">

      <div class="cat-badge" style="margin-bottom:0.8rem;">
        <?= ucfirst(htmlspecialchars($article['categorie'])) ?>
      </div>

      <h1 class="article-title"><?= htmlspecialchars($article['titre']) ?></h1>

      <div class="article-meta">
        <span><?= htmlspecialchars($article['auteur']) ?></span>
        <span><?= htmlspecialchars($article['date_publication']) ?></span>
      </div>

      <hr style="border:none; border-top:1px solid var(--border-light); margin-bottom:1.5rem;">

      <img src="<?= htmlspecialchars($imgArt) ?>"
           alt="<?= htmlspecialchars($article['titre']) ?>"
           style="width:100%; height:380px; object-fit:cover; margin-bottom:1.5rem;">

      <div class="article-content">
        <?php
          $paragraphes = explode("\n\n", $article['contenu']);
          foreach ($paragraphes as $para) {
            if (trim($para) !== '') {
              echo '<p>' . htmlspecialchars(trim($para)) . '</p>';
            }
          }
        ?>
      </div>

      <div style="margin-top:2rem; padding-top:1rem; border-top:1px solid var(--border-light); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap; align-items:center;">
          <span style="font-family:'Barlow',sans-serif; font-size:0.72rem; font-weight:700; text-transform:uppercase; color:#555;">Tags :</span>
          <span style="font-family:'Barlow',sans-serif; font-size:0.72rem; font-weight:700; text-transform:uppercase; background:#f0feff; border:1px solid var(--accent); padding:2px 8px;">
            <?= ucfirst(htmlspecialchars($article['categorie'])) ?>
          </span>
        </div>
        <a href="../accueil.php" class="btn btn-secondary btn-sm">← Retour à l'accueil</a>
      </div>

      <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['editeur', 'administrateur'])) : ?>
        <div style="display:flex; gap:0.8rem; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-light);">
          <a href="modifier.php?id=<?= (int)$article['id'] ?>" class="btn btn-primary btn-sm">Modifier</a>
          <a href="supprimer.php?id=<?= (int)$article['id'] ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
        </div>
      <?php endif; ?>

    </article>

    <aside class="sidebar" style="padding-left:1.5rem; border-left:1px solid var(--border-light);">
      <div class="sidebar-title">À lire aussi</div>
      <?php foreach ($articlesSimilaires as $sim) : ?>
        <div style="margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border-light);">
          <?php $simImg = !empty($sim['image']) ? $sim['image'] : IMG_DEFAULT; ?>
          <img src="<?= htmlspecialchars($simImg) ?>"
               alt="<?= htmlspecialchars($sim['titre']) ?>"
               style="width:100%; height:100px; object-fit:cover; margin-bottom:0.5rem;">
          <div class="cat-badge" style="margin-bottom:0.3rem;"><?= ucfirst(htmlspecialchars($sim['categorie'])) ?></div>
          <div style="font-family:'Playfair Display',serif; font-size:0.88rem; font-weight:700; line-height:1.3; color:#000;">
            <a href="detail.php?id=<?= (int)$sim['id'] ?>" style="color:inherit;">
              <?= htmlspecialchars($sim['titre']) ?>
            </a>
          </div>
          <div style="font-family:'Barlow',sans-serif; font-size:0.65rem; color:#888; margin-top:0.3rem;">
            <?= htmlspecialchars($sim['date_publication']) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </aside>

  </div>
</main>

<?php require_once __DIR__ . '/../footer.php'; ?>
