
<?php
/**@var PDO $pdo */
session_start();

require_once "includes/db.php";

$login = isset($_POST["login"]) ? trim($_POST["login"]) : "";
$motdepasse = isset($_POST["motdepasse"]) ? $_POST["motdepasse"] : "";

if ($login === "" || $motdepasse === "") {
    $_SESSION["erreur_connexion"] = "Login et mot de passe requis.";
    header("Location: connexion.php");
    exit();
}

$stmt = $pdo->prepare(
    "SELECT id, nom, prenom, login, mot_de_passe, role FROM utilisateurs WHERE login = ?",
);
$stmt->execute([$login]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($motdepasse, $user["mot_de_passe"])) {
    $_SESSION["erreur_connexion"] = "Login ou mot de passe incorrect.";
    header("Location: connexion.php");
    exit();
}

$_SESSION["id"] = $user["id"];
$_SESSION["nom"] = $user["nom"];
$_SESSION["prenom"] = $user["prenom"];
$_SESSION["login"] = $user["login"];
$_SESSION["role"] = $user["role"];

header("Location: accueil.php");
exit();

