<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
?>

<main>

    <h1>Créer une catégorie</h1>

    <form action="../api/categories.php" method="post">
        <input name="nom" type="text" placeholder="Nom"></input>
        <button type="submit">Soumettre</button>
    </form>

</main>

<script type="module" src="../static/js/categories.js"></script>

<?php require_once __DIR__ . "/../footer.php";
?>
