<?php

require_once __DIR__ . "/entete.php";
require_once __DIR__ . "/menu.php";
require_once __DIR__ . "/includes/auth.php";

$role = get_role();
check_role($role, ["visiteur", "editeur", "administrateur"]);
?>

<main>

    <input type="search" name="search-bar" placeholder="rechercher"></input>
    <div class="query-results"></div>

    <div class="articles-container">
        <p>Chargement des articles...</p>
    </div>

    <div class="pagination"></div>
</main>

<script type="module" src="static/js/articles.js"></script>

<?php require_once "footer.php"; ?>
