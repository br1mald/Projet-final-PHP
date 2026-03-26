<?php

require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);
?>

<main>

    <h1>Modifier une catégorie</h1>
    <div class="patch-form-container"></div>

</main>

<script type="module" src="../static/js/categories.js"></script>

<?php require_once __DIR__ . "/../footer.php";
?>
