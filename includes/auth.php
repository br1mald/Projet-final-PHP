<?php

function get_role()
{
    $role = "visiteur";

    if (isset($_SESSION["role"])) {
        $role = $_SESSION["role"];
    }

    return $role;
}
function check_role($user_role, $authorized_roles)
{
    if (in_array($user_role, $authorized_roles)) {
        return;
    } else {
        header("Location: /final_project/accueil.php");
        die("Accès non-autorisé.");
    }
}
?>
