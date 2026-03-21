<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
?>

<main>

    <h1>Créer un article</h1>

    <form action="../api/articles.php" method="post">
        <input name="title" type="text" placeholder="Titre"></input>
        <input name="description" type="text" placeholder="Description courte"></input>
        <input name="content" type="text" placeholder="Contenu"></input>
        <select class="form-select-field" name="category">
            <option value="">--Veuillez choisir une catégorie--</option>
        </select>
        <input class="current-date" name="date" type="hidden" value=""></input> <!-- maybe do this in js directly -->
        <button type="submit">Soumettre</button>
    </form>

</main>

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php";
?>
