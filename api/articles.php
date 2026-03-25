<?php
/** @var PDO $pdo */

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";
require_once __DIR__ . "/../config.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET" && isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "all":
            $stmt = $pdo->query("SELECT * FROM articles ORDER BY date_publication DESC");

            $liste_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($liste_articles) {
                json_response($liste_articles);
            } else {
                json_error("Aucun article trouvé", 400);
            }
            break;
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
            $stmt = $pdo->query("SELECT * FROM articles ORDER BY date_publication DESC LIMIT 5");

            $latest_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($latest_articles) {
                json_response($latest_articles);
            } else {
                json_error("Aucun article trouvé", 400);
            }
            break;
        case "category":
            if (!isset($_GET["id"])) {
                json_error("ID de catégorie requis", 400);
                break;
            }

            $stmt = $pdo->prepare("SELECT * FROM articles WHERE categorie_id = :id ORDER BY date_publication DESC");
            $stmt->execute([":id" => $_GET["id"]]);

            $articles_by_category = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($articles_by_category) {
                json_response($articles_by_category);
            } else {
                json_error("Aucun article trouvé dans cette catégorie", 400);
            }
            break;
    }
} elseif ($method === "POST") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    // if there is an error with the js script
    if (!is_array($body) || empty($body)) {
        $body = $_POST;
    }

    $titre = htmlspecialchars(trim($body["titre"] ?? ""), ENT_QUOTES, "UTF-8");
    $description = htmlspecialchars(
        trim($body["description"] ?? ""),
        ENT_QUOTES,
        "UTF-8",
    );
    $contenu = htmlspecialchars(
        trim($body["contenu"] ?? ""),
        ENT_QUOTES,
        "UTF-8",
    );
    $categorie_id = $body["categorie_id"] ?? null;
    $image_url = htmlspecialchars(trim($body["image_url"] ?? ""), ENT_QUOTES, "UTF-8");

    $errors = [];
    
    if ($titre === "" || strlen($titre) < 3) {
        $errors["titre"] = "Titre invalide (min 3 caractères).";
    }
    if ($description === "" || strlen($description) < 10) {
        $errors["description"] = "Description invalide (min 10 caractères).";
    }
    if ($contenu === "" || strlen($contenu) < 20) {
        $errors["contenu"] = "Contenu invalide (min 20 caractères).";
    }
    if ($image_url !== "" && !filter_var($image_url, FILTER_VALIDATE_URL)) {
        $errors["image_url"] = "URL d'image invalide.";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    $stmt = $pdo->prepare(
        "INSERT into articles (titre, description, contenu, categorie_id, image_url, date_publication) VALUES (:titre, :description, :contenu, :category_id, :image_url, NOW());",
    );
    $success = $stmt->execute([
        ":titre" => $titre,
        ":description" => $description,
        ":content" => $contenu,
        ":category_id" => $categorie_id,
        ":image_url" => $image_url,
    ]);

    if ($success) {
        $newId = $pdo->lastInsertId();
        header("Content-Type: application/json", true, 201);
        echo json_encode(["ok" => true, "id" => $newId]);
        exit();
    } else {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["error" => 'Impossible d\'ajouter l\'article.']);
        exit();
    }
} elseif ($method === "PUT" || $method === "PATCH") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    // if there is an error with the js script
    if (!is_array($body) || empty($body)) {
        $body = $_POST;
    }

    $article_id = $body["id"] ?? null;
    $titre = htmlspecialchars(trim($body["titre"] ?? ""), ENT_QUOTES, "UTF-8");
    $description = htmlspecialchars(
        trim($body["description"] ?? ""),
        ENT_QUOTES,
        "UTF-8",
    );
    $contenu = htmlspecialchars(
        trim($body["contenu"] ?? ""),
        ENT_QUOTES,
        "UTF-8",
    );
    $categorie_id = $body["categorie_id"] ?? null;
    $image_url = htmlspecialchars(trim($body["image_url"] ?? ""), ENT_QUOTES, "UTF-8");

    $errors = [];
    if (!is_numeric($article_id)) {
        $errors["id"] = "ID d'article invalide.";
    }
    if ($titre === "" || strlen($titre) < 3) {
        $errors["titre"] = "Titre invalide (min 3 caractères).";
    }
    if ($description === "" || strlen($description) < 10) {
        $errors["description"] = "Description invalide (min 10 caractères).";
    }
    if ($contenu === "" || strlen($contenu) < 20) {
        $errors["contenu"] = "Contenu invalide (min 20 caractères).";
    }
    if ($image_url !== "" && !filter_var($image_url, FILTER_VALIDATE_URL)) {
        $errors["image_url"] = "URL d'image invalide.";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    // Vérifier que l'article existe
    $stmt_check = $pdo->prepare("SELECT id FROM articles WHERE id = :id");
    $stmt_check->execute([":id" => $article_id]);
    if (!$stmt_check->fetch()) {
        header("Content-Type: application/json", true, 404);
        echo json_encode(["error" => "Article non trouvé."]);
        exit();
    }

    $stmt = $pdo->prepare(
        "UPDATE articles SET titre = :titre, description = :description, contenu = :contenu, categorie_id = :categorie_id, image_url = :image_url WHERE id = :id",
    );
    $success = $stmt->execute([
        ":titre" => $titre,
        ":description" => $description,
        ":contenu" => $contenu,
        ":categorie_id" => $categorie_id,
        ":image_url" => $image_url,
        ":id" => $article_id,
    ]);

    if ($success) {
        header("Content-Type: application/json", true, 200);
        echo json_encode(["ok" => true, "id" => $article_id]);
        exit();
    } else {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["error" => "Impossible de modifier l'article."]);
        exit();
    }
} elseif ($method === "DELETE") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    $article_id = $body["articleId"] ?? null;
    $errors = [];
    if (!is_numeric($article_id)) {
        $errors["article_id"] = "Article invalide";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id;");
    $success = $stmt->execute([":id" => $article_id]);

    if ($success) {
        header("Content-type: application/json", true, 200);
        echo json_encode(["ok" => true, "id" => $article_id]);
        exit();
    } else {
        header("Content-type: application/json", true, 400);
        echo json_encode(["error" => "Impossible de supprimer l'article"]);
        exit();
    }
}
?>
