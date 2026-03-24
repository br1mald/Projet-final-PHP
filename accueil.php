<?php
require_once "entete.php";
require_once "menu.php";
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
