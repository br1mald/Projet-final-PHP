<?php
/** @var PDO $pdo */

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";
require_once __DIR__ . "/../config.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET" && isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "all":
            $stmt = $pdo->query("SELECT c.*, COUNT(a.id) as articles_count FROM categories c LEFT JOIN articles a ON c.id = a.categorie_id GROUP BY c.id ORDER BY c.nom");

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
} elseif ($method === "POST") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    // if there is an error with the js script
    if (empty($body)) {
        $body = $_POST;
    }

    $nom = htmlspecialchars(trim($body["nom"] ?? ""), ENT_QUOTES, "UTF-8");

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
} elseif ($method === "PUT" || $method === "PATCH") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    // if there is an error with the js script
    if (empty($body)) {
        $body = $_POST;
    }

    $category_id = $body["id"] ?? null;
    $nom = htmlspecialchars(trim($body["nom"] ?? ""), ENT_QUOTES, "UTF-8");

    $errors = [];
    if (!is_numeric($category_id)) {
        $errors["id"] = "ID de catégorie invalide.";
    }
    if ($nom === "" || strlen($nom) < 3) {
        $errors["nom"] = "Nom invalide (min 3 caractères).";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    // Vérifier que la catégorie existe
    $stmt_check = $pdo->prepare("SELECT id FROM categories WHERE id = :id");
    $stmt_check->execute([":id" => $category_id]);
    if (!$stmt_check->fetch()) {
        header("Content-Type: application/json", true, 404);
        echo json_encode(["error" => "Catégorie non trouvée."]);
        exit();
    }

    $stmt = $pdo->prepare("UPDATE categories SET nom = :nom WHERE id = :id");
    $success = $stmt->execute([
        ":nom" => $nom,
        ":id" => $category_id,
    ]);

    if ($success) {
        header("Content-Type: application/json", true, 200);
        echo json_encode(["ok" => true, "id" => $category_id]);
        exit();
    } else {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["error" => "Impossible de modifier la catégorie."]);
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
}
?>
