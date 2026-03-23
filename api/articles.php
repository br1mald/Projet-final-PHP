<?php

/** @var PDO $pdo */

require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET" && isset($_GET["action"])) {
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
    $date_publication = $body["date_publication"] ?? date("Y-m-d H:i:s");

    $errors = [];
    if ($titre === "" || strlen($titre) < 3) {
        $errors["titre"] = "Titre invalide (min 3 caractères).";
    }
    if ($description === "" || strlen($description) < 10) {
        $errors["description"] = "Description trop courte (min 10 caractères).";
    }
    if ($contenu === "" || strlen($contenu) < 20) {
        $errors["contenu"] = "Contenu trop court (min 20 caractères).";
    }
    if (!is_numeric($categorie_id)) {
        $errors["categorie_id"] = "Catégorie invalide.";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    $stmt = $pdo->prepare(
        "INSERT into articles (titre, description, contenu, categorie_id, auteur_id, date_publication) VALUES (:titre, :description, :content, :category_id, :author, :date);",
    );
    $success = $stmt->execute([
        ":titre" => $titre,
        ":description" => $description,
        ":content" => $contenu,
        ":category_id" => $categorie_id,
        ":author" => 1, // TODO: change when auth is implemented
        ":date" => $date_publication,
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
        echo json_encode(["error" => "Impossible de supprimer l' article"]);
        exit();
    }
} elseif ($method === "PATCH") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    $article_id = $body["id"];
    $attribute_name = htmlspecialchars(
        trim($body["attribute"] ?? ""),
        ENT_QUOTES,
        "UTF-8",
    );
    $attribute_value = htmlspecialchars(
        trim($body["value"] ?? ""),
        ENT_QUOTES,
        "UTF-8",
    );

    $errors = [];

    if (!is_numeric($article_id)) {
        $errors["id"] = "Article invalide";
    }
    if (
        $attribute_name === "titre" &&
        ($attribute_value === "" || strlen($attribute_value) < 3)
    ) {
        $errors["titre"] = "Titre invalide (min 3 caractères).";
    }
    if (
        $attribute_name === "description" &&
        ($attribute_value === "" || strlen($attribute_value) < 10)
    ) {
        $errors["description"] = "Description trop courte (min 10 caractères).";
    }
    if (
        $attribute_name === "contenu" &&
        ($attribute_value === "" || strlen($attribute_value) < 20)
    ) {
        $errors["contenu"] = "Contenu trop court (min 20 caractères).";
    }
    if ($attribute_name === "categorie_id" && !is_numeric($attribute_value)) {
        $errors["categorie_id"] = "Catégorie invalide.";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    $allowed_attributes = ["titre", "description", "contenu", "categorie_id"];
    if (!in_array($attribute_name, $allowed_attributes)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode([
            "errors" => ["attribute" => "Attribut non autorisé."],
        ]);
        exit();
    }

    $stmt = $pdo->prepare(
        "UPDATE articles SET {$attribute_name} = :value WHERE id = :id;",
    );
    $success = $stmt->execute([
        ":value" => $attribute_value,
        ":id" => $article_id,
    ]);

    if ($success) {
        header("Content-type: application/json", true, 200);
        echo json_encode(["ok" => true, "id" => $article_id]);
        exit();
    } else {
        header("Content-type: application/json", true, 400);
        echo json_encode(["error" => "Impossible de modifier l'article"]);
        exit();
    }
}
?>
