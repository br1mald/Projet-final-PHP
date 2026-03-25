<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
?>

<main>

    <h1>Modifier un article</h1>
    <form class="edit-article-form" enctype="multipart/form-data">
        <input type="hidden" name="id" class="article-id">
        <input type="text" name="titre" class="titre" placeholder="Titre" required>
        <input type="text" name="description" class="description" placeholder="Description courte" required>
        <textarea name="contenu" class="contenu" placeholder="Contenu" required></textarea>
        <select name="categorie_id" class="categorie_id" required>
            <option value="">--Choisir une catégorie--</option>
        </select>
        <img class="current-image" src="" alt="" style="display:none; max-width:200px;">
        <input type="file" name="image" accept="image/*">
        <button type="submit">Modifier</button>
    </form>

</main>

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php";
?>
