<?php

/** @var PDO $pdo */

require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method == "GET" && isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "search":
            $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
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
                "SELECT * FROM articles ORDER BY date_publication DESC LIMIT 3 ",
            );
            $latest_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($latest_articles) {
                json_response($latest_articles);
            } else {
                json_error("Article non trouvé", 400);
            }
            break;
    }
}
?>
