<?php
/** @var PDO $pdo */

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";
require_once __DIR__ . "/../config.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method = "GET" && isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "all":
            $stmt = $pdo->query("SELECT * FROM utilisateurs");

            $liste_utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($liste_utilisateurs) {
                json_response($liste_utilisateurs);
            } else {
                json_error("Aucun utilisateur trouvé", 400);
            }
            break;
        case "search":
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
            $stmt->execute([":id" => $_GET["id"]]);

            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($utilisateur) {
                json_response($utilisateur);
            } else {
                json_error("Utilisateur non trouvé", 400);
            }
            break;
        case "role-filter":
            $stmt = $pdo->prepare(
                "SELECT * FROM utilisateurs WHERE role = :role",
            );
            $stmt->execute(["role" => $_GET["role"]]);
            $liste_utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($liste_utilisateurs) {
                json_response($liste_utilisateurs);
            } else {
                json_error("Aucun utilisateur trouvé", 400);
            }
            break;
    }
}
?>
