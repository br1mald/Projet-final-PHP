<?php

require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["visiteur", "editeur", "administrateur"]);
?>

<main class="article-details"></main>

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
