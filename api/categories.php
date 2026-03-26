<?php
/** @var PDO $pdo */

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";
require_once __DIR__ . "/../config.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET" && isset($_GET["action"])) {
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
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id ;");
            $stmt->execute([":id" => $_GET["id"]]);

            $categorie = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($categorie) {
                json_response($categorie);
            } else {
                json_error("Catégorie non trouvée", 400);
            }
            break;
        case "number_of_articles":
            $stmt = $pdo->prepare(
                "SELECT * FROM categories JOIN articles ON categories.id = articles.categorie_id WHERE categories.id = :id;",
            );
            $stmt->execute([":id" => $_GET["id"]]);
            $counter = 0;

            $found_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($found_articles) {
                foreach ($found_articles as $article) {
                    $counter++;
                }
                json_response($counter);
            } else {
                json_error("Aucun article trouvé", 400);
            }
            break;
    }
} elseif ($method === "POST") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    // if there is an error with the js script
    if (empty($body)) {
        $body = $_POST;
    }

    $nom = trim($body["nom"] ?? "");

    $errors = [];
    if ($nom === "" || strlen($nom) < 3) {
        $errors["nom"] = "Nom invalide (min 3 caractères).";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    $stmt = $pdo->prepare("INSERT into categories (nom) VALUES (:nom);");
    $success = $stmt->execute([
        ":nom" => $nom,
    ]);

    if ($success) {
        $newId = $pdo->lastInsertId();
        header("Content-Type: application/json", true, 201);
        echo json_encode(["ok" => true, "id" => $newId]);
        exit();
    } else {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["error" => 'Impossible d\'ajouter la catégorie.']);
        exit();
    }
} elseif ($method === "DELETE") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    $category_id = $body["categoryId"] ?? null;
    $errors = [];
    if (!is_numeric($category_id)) {
        $errors["category_id"] = "Catégorie invalide";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id;");
    $success = $stmt->execute([":id" => $category_id]);

    if ($success) {
        header("Content-type: application/json", true, 200);
        echo json_encode(["ok" => true, "id" => $category_id]);
        exit();
    } else {
        header("Content-type: application/json", true, 400);
        echo json_encode(["error" => "Impossible de supprimer la catégorie"]);
        exit();
    }
} elseif ($method === "PATCH") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    $category_id = $body["id"] ?? null;
    $category_value = trim($body["value"] ?? "");

    $errors = [];

    if (!is_numeric($category_id)) {
        $errors["category_id"] = "Catégorie invalide";
    }
    if ($category_value === "" || strlen($category_value) < 3) {
        $errors["nom"] = "Nom invalide (min 3 caractères)";
    }

    if (!empty($errors)) {
        header("Content-type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    $stmt = $pdo->prepare("UPDATE categories SET nom = :nom WHERE id = :id;");
    $success = $stmt->execute([
        ":nom" => $category_value,
        ":id" => $category_id,
    ]);

    if ($success) {
        header("Content-type: application/json", true, 200);
        echo json_encode(["ok" => true, "id" => $category_id]);
        exit();
    } else {
        header("Content-type: application/json", true, 400);
        echo json_encode(["error" => "Impossible de modifier la catégorie"]);
        exit();
    }
}
?>
