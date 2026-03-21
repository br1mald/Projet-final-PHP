<?php
/** @var PDO $pdo */

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";
require_once __DIR__ . "/../config.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method = "GET" && isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "all":
            $stmt = $pdo->query("SELECT * FROM categories");

            $liste_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($liste_categories) {
                json_response($liste_categories);
            } else {
                json_error("Aucune catégorie trouvée", 400);
            }
            break;
        case "search":
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
            $stmt->execute([":id" => $_GET["id"]]);

            $categorie = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($categorie) {
                json_response($categorie);
            } else {
                json_error("Catégorie non trouvée", 400);
            }
            break;
    }
}
?>
