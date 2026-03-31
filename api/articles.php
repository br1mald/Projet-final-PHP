<?php

/** @var PDO $pdo */

require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET" && isset($_GET["action"])) {
    // READ
    switch ($_GET["action"]) {
        case "search_bar": // requête provenant de la barre de recherche
            $stmt = $pdo->prepare(
                // requête sql
                "SELECT articles.titre, articles.description, articles.contenu FROM articles JOIN categories ON articles.categorie_id = categories.id WHERE articles.titre LIKE :input OR articles.contenu LIKE :input or categories.nom LIKE :input",
            );
            $stmt->execute([":input" => "%" . $_GET["input"] . "%"]); // les % avant et après signifient "si x contient input", x étant le titre, le contenu ou le nom de catégorie

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // on récupère toutes les valeurs correspondantes

            if ($results) {
                // si on a des résultats on les envoie
                json_response($results);
            } else {
                // sinon on envoie une chaîne vide
                json_response("");
            }

            break;
        case "search_by_id": // rechercher un article par son id
            $stmt = $pdo->prepare(
                "SELECT articles.*, categories.nom AS categorie, CONCAT(utilisateurs.prenom, ' ', utilisateurs.nom) AS auteur FROM articles JOIN utilisateurs ON articles.auteur_id = utilisateurs.id JOIN categories ON articles.categorie_id = categories.id WHERE articles.id = :id",
            );
            $stmt->execute([":id" => $_GET["id"]]); // on prend la valeur envoyée en paramètre comme id
            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            // envoi de la réponse en format json
            if ($article) {
                json_response($article);
            } else {
                json_error("Article non trouvé", 400);
            }
            break;
        case "latest": // affichage des derniers articles (pour accueil.php)
            $stmt = $pdo->query(
                "SELECT a.*, c.nom as categorie, CONCAT(u.prenom, ' ', u.nom) as auteur, u.nom as util_nom, c.nom as cat_nom FROM articles a LEFT JOIN categories c ON a.categorie_id = c.id LEFT JOIN utilisateurs u ON a.auteur_id = u.id ORDER BY a.date_publication DESC LIMIT 20",
            );
            $latest_articles = $stmt->fetchAll(PDO::FETCH_ASSOC); // on envoie les 20 articles les plus récents

            // envoi de la réponse en format json
            if ($latest_articles) {
                json_response($latest_articles);
            } else {
                json_error("Article non trouvé", 400);
            }
            break;
        case "category_filter": // on filtre les articles par catégorie
            $stmt = $pdo->prepare(
                "SELECT * FROM articles WHERE categorie_id = :id;",
            );
            $stmt->execute(["id" => $_GET["categorie_id"]]); //
            $liste_articles = $stmt->fetch(PDO::FETCH_ASSOC);

            // envoi de la réponse
            if ($liste_articles) {
                json_response($liste_articles);
            } else {
                json_error("Aucun article trouvé", 400);
            }
            break;
        case "all": // récupérer tous les articles (pour modifier.php et supprimer.php)
            $stmt = $pdo->query(
                "SELECT a.*, c.nom as categorie, CONCAT(u.prenom, ' ', u.nom) as auteur, u.nom as util_nom, c.nom as cat_nom FROM articles a LEFT JOIN categories c ON a.categorie_id = c.id LEFT JOIN utilisateurs u ON a.auteur_id = u.id ORDER BY a.date_publication DESC",
            );
            $all_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // envoi de la réponse json
            if ($all_articles) {
                json_response($all_articles);
            } else {
                json_error("Aucun article trouvé", 400);
            }
    }
} elseif ($method === "POST" && isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "create":
            //CREATE

            // stockage des données dans des variables php
            $titre = trim($_POST["title"] ?? "");
            $description = trim(
                $_POST["description"] ?? "",
                ENT_QUOTES,
                "UTF-8",
            );
            $contenu = trim($_POST["content"] ?? "");
            $categorie_id = $_POST["category"] ?? null;
            $image_path = null;

            if (
                isset($_FILES["image"]) &&
                $_FILES["image"]["error"] === UPLOAD_ERR_OK
            ) {
                $file = $_FILES["image"];

                $allowed = ["image/jpeg", "image/png", "image/webp"];
                if (!in_array($file["type"], $allowed)) {
                    json_error(
                        "Format non autorisé (JPEG, PNG, WEBP uniquement",
                        400,
                    );
                }

                if ($file["size"] > 2 * 1024 * 1024) {
                    json_error("Image trop volumineuse (max 2Mo", 400);
                }

                $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
                $filename = uniqid("art_") . "." . $ext;
                $dest = __DIR__ . "/../uploads/" . $filename;

                if (!move_uploaded_file($file["tmp_name"], $dest)) {
                    json_error("Erreur lors du téléchargement", 400);
                }

                $image_path = "uploads/" . $filename;
            }
            // validation côté serveur
            $errors = [];

            if ($titre === "" || strlen($titre) < 3) {
                $errors["titre"] = "Titre invalide (min 3 caractères).";
            }
            if ($description === "" || strlen($description) < 10) {
                $errors["description"] =
                    "Description trop courte (min 10 caractères).";
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

            // requête sql pour l'ajout de l'article
            $stmt = $pdo->prepare(
                "INSERT into articles (titre, description, contenu, categorie_id, auteur_id, date_publication, image) VALUES (:titre, :description, :content, :category_id, :author, NOW(), :image);",
            );
            $success = $stmt->execute([
                ":titre" => $titre,
                ":description" => $description,
                ":content" => $contenu,
                ":category_id" => $categorie_id,
                ":author" => 1, // TODO: change when auth is implemented
                ":image" => $image_path, //filepath
            ]);

            // envoi de la réponse json
            if ($success) {
                $newId = $pdo->lastInsertId();
                header("Content-Type: application/json", true, 201);
                echo json_encode(["ok" => true, "id" => $newId]);
                exit();
            } else {
                header("Content-Type: application/json", true, 400);
                echo json_encode([
                    "error" => 'Impossible d\'ajouter l\'article.',
                ]);
                exit();
            }
            break;
        case "update":
            // UPDATE
            // stockage des données dans des variables php

            $article_id = $_POST["id"] ?? null;
            $titre = trim($_POST["titre"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $contenu = trim($_POST["contenu"] ?? "");
            $categorie_id = $_POST["categorie_id"] ?? null;
            $image_sql = null;

            // validation côté serveur
            $errors = [];

            if (!is_numeric($article_id)) {
                $errors["id"] = "Article invalide";
            }
            if ($titre === "" || strlen($titre) < 3) {
                $errors["titre"] = "Titre invalide (min 3 caractères).";
            }
            if ($description === "" || strlen($description) < 10) {
                $errors["description"] =
                    "Description trop courte (min 10 caractères).";
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
            $params = [
                ":id" => $article_id,
                ":titre" => $titre,
                ":description" => $description,
                ":content" => $contenu,
                ":category_id" => $categorie_id,
            ];
            // gestion d'image si il y en a
            if (
                isset($_FILES["image"]) &&
                $_FILES["image"]["error"] === UPLOAD_ERR_OK
            ) {
                $file = $_FILES["image"];

                $allowed = ["image/jpeg", "image/png", "image/webp"];
                if (!in_array($file["type"], $allowed)) {
                    json_error(
                        "Format non autorisé (JPEG, PNG, WEBP uniquement",
                        400,
                    );
                }

                if ($file["size"] > 2 * 1024 * 1024) {
                    json_error("Image trop volumineuse (max 2Mo)", 400);
                }

                $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
                $filename = uniqid("art_") . "." . $ext;
                $dest = __DIR__ . "/../uploads/" . $filename;

                if (!move_uploaded_file($file["tmp_name"], $dest)) {
                    json_error("Erreur lors du téléchargement", 400);
                }

                $image_sql = ", image = :image";
                $params[":image"] = "uploads/" . $filename;
            }

            // requête sql pour l'ajout de l'article
            $stmt = $pdo->prepare(
                "UPDATE articles SET titre = :titre, description = :description, contenu = :content, categorie_id = :category_id $image_sql WHERE id = :id;",
            );
            $success = $stmt->execute($params);

            // envoi de la réponse json
            if ($success) {
                header("Content-Type: application/json", true, 201);
                echo json_encode(["ok" => true, "id" => $article_id]);
                exit();
            } else {
                header("Content-Type: application/json", true, 400);
                echo json_encode([
                    "error" => 'Impossible de modifier l\'article.',
                ]);
                exit();
            }

            break;
    }
} elseif ($method === "DELETE") {
    // DELETE
    // récupération des données
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    // stockage dans des variables
    $article_id = $body["articleId"] ?? null;

    // validation côté serveur
    $errors = [];
    if (!is_numeric($article_id)) {
        $errors["article_id"] = "Article invalide";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    // requête sql
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id;");
    $success = $stmt->execute([":id" => $article_id]);

    // envoi de la réponse
    if ($success) {
        header("Content-type: application/json", true, 200);
        echo json_encode(["ok" => true, "id" => $article_id]);
        exit();
    } else {
        header("Content-type: application/json", true, 400);
        echo json_encode(["error" => "Impossible de supprimer l' article"]);
        exit();
    }
}
?>
