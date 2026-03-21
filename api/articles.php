<?php

/** @var PDO $pdo */

require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "GET" && isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "search":
            $stmt = $pdo->prepare(
                "SELECT articles.*, categories.nom AS cat_nom, utilisateurs.nom AS util_nom FROM articles JOIN utilisateurs ON articles.auteur_id = utilisateurs.id JOIN categories ON articles.categorie_id = categories.id WHERE articles.id = :id;",
            );
            $stmt->execute([":id" => $_GET["id"]]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($article) {
                json_response($article);
            } else {
                json_error("Article non trouvé", 400);
            }
            break;
        case "latest":
            $stmt = $pdo->query(
                "SELECT * FROM articles ORDER BY date_publication DESC LIMIT 3;",
            );
            $latest_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($latest_articles) {
                json_response($latest_articles);
            } else {
                json_error("Article non trouvé", 400);
            }
            break;
        case "category_filter":
            $stmt = $pdo->prepare(
                "SELECT * FROM articles WHERE categorie_id = :id;",
            );
            $stmt->execute(["id" => $_GET["categorie_id"]]);
            $liste_articles = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($liste_articles) {
                json_response($liste_articles);
            } else {
                json_error("Aucun article trouvé", 400);
            }
            break;
        case "all":
            $stmt = $pdo->query("SELECT * FROM articles;");
            $all_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($all_articles) {
                json_response($all_articles);
            } else {
                json_error("Aucun article trouvé", 400);
            }
    }
}
?>
