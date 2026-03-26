<?php

require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);
?>

<main>

    <h1>Créer un article</h1>

    <form class="post-form" enctype="multipart/form-data">
        <input name="title" type="text" placeholder="Titre" required></input>
        <input name="description" type="text" placeholder="Description courte" required></input>
        <input name="content" type="text" placeholder="Contenu" required></input>
        <select class="form-select-field" name="category" required>
            <option value="">--Veuillez choisir une catégorie--</option>
        </select>
        <input type="file" name="image" accept="image/*"></input>
        <button type="submit">Soumettre</button>
    </form>

</main>

<script type="module" src="../static/js/articles.js"></script>

<?php require_once __DIR__ . "/../footer.php";
?>
