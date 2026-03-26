<?php

require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["administrateur"]);
?>

<main>

    <h1>Créer un utilisateur</h1>

    <form action="../api/utilisateurs.php" method="post">
        <input name="nom" type="text" placeholder="Nom"></input>
        <input name="prenom" type="text" placeholder="Prénom"></input>
        <input name="login" type="text" placeholder="Login"></input>
        <input name="password" type="password"></input>
        <select class="role-select" name="userRole">
            <option value="editeur">Éditeur</option>
            <option value="administrateur">Administrateur</option>
        </select>
        <button type="submit">Soumettre</button>
    </form>

</main>

<script type="module" src="../static/js/utilisateurs.js"></script>

<?php require_once __DIR__ . "/../footer.php";
?>
