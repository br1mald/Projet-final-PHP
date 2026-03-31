<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = isset($pageTitle) ? htmlspecialchars($pageTitle) : "ActuMonde";
$base =
    strpos($_SERVER["PHP_SELF"] ?? "", "/articles/") !== false ||
    strpos($_SERVER["PHP_SELF"] ?? "", "/categories/") !== false ||
    strpos($_SERVER["PHP_SELF"] ?? "", "/utilisateurs/") !== false
        ? "../"
        : "";
$scriptPath = $_SERVER["SCRIPT_NAME"] ?? "";
$pathParts = array_filter(explode("/", trim($scriptPath, "/")));
$appBase = count($pathParts) > 1 ? "/" . $pathParts[0] : "";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?> — ActuMonde</title>
  <?php $cssVersion = file_exists(__DIR__ . "/static/style.css")
      ? filemtime(__DIR__ . "/static/style.css")
      : time(); ?>
  <link rel="stylesheet" href="<?= $base ?>static/style.css?v=<?= $cssVersion ?>">
</head>
<body>
  <header class="site-header" style="display:flex; align-items:center; justify-content:space-between; text-align:left; padding:1rem 2rem;">
    <div style="flex:1;"></div>
    <div style="text-align:center;">
      <a href="<?= $base ?>accueil.php" class="logo">Actu<span>Monde</span></a>
      <p class="tagline">Toute l'actualité en temps réel</p>
    </div>
    <div style="flex:1; display:flex; justify-content:flex-end;">
      <form action="<?= $base ?>articles/recherche.php" method="GET" style="display:flex; align-items:center; gap:0.4rem;" id="formRechercheHeader" novalidate>
        <input type="text" name="q" id="champRechercheHeader" placeholder="Rechercher..." style="padding:0.45rem 0.8rem; border:1.5px solid #BBF9FC; font-family:'Barlow',sans-serif; font-size:0.82rem; width:180px; color:#000; outline:none;">
        <button type="submit" style="background:#BBF9FC; border:1.5px solid #BBF9FC; padding:0.45rem 0.8rem; cursor:pointer; font-size:0.82rem; font-weight:700; color:#000;">Q</button>
      </form>
    </div>
  </header>

  <?php require_once __DIR__ . "/menu.php"; ?>

<script>
window.API_BASE = '<?= $appBase ?>/api';
window.APP_BASE = '<?= $appBase ?>';
window.IMG_DEFAULT = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=400&fit=crop&q=80';

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('formRechercheHeader')?.addEventListener('submit', function(e) {
    const champ = document.getElementById('champRechercheHeader');
    if (champ && champ.value.trim() === '') {
      champ.style.borderColor = '#b10000';
      e.preventDefault();
    } else if (champ) {
      champ.style.borderColor = '#BBF9FC';
    }
  });
  document.getElementById('champRechercheHeader')?.addEventListener('input', function() {
    this.style.borderColor = '#BBF9FC';
  });
});
</script>
