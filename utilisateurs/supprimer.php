<?php

require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["administrateur"]);
?>

<main>

    <h1>Supprimer un utilisateur</h1>

    <div class="delete-form-container">
    </div>

</main>

<script type="module" src="../static/js/utilisateurs.js"></script>

<?php require_once __DIR__ . "/../footer.php";
?>
