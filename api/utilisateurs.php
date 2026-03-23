<?php
/** @var PDO $pdo */

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";
require_once __DIR__ . "/../config.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET" && isset($_GET["action"])) {
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
} elseif ($method === "POST") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    // if there is an error with the js script
    if (!is_array($body) || empty($body)) {
        $body = $_POST;
    }

    $nom = htmlspecialchars(trim($body["nom"] ?? ""), ENT_QUOTES, "UTF-8");
    $prenom = htmlspecialchars(
        trim($body["prenom"] ?? ""),
        ENT_QUOTES,
        "UTF-8",
    );
    $login = htmlspecialchars(trim($body["login"] ?? ""), ENT_QUOTES, "UTF-8");
    $password = htmlspecialchars(
        trim($body["password"] ?? ""),
        ENT_QUOTES,
        "UTF-8",
    );
    $role = htmlspecialchars(trim($body["role"] ?? null), ENT_QUOTES, "UTF-8");

    $errors = [];
    if ($nom === "" || strlen($nom) < 3) {
        $errors["nom"] = "Nom invalide (min 3 caractères).";
    }
    if ($prenom === "" || strlen($prenom) < 3) {
        $errors["prenom"] = "Prénom trop court (min 3 caractères).";
    }
    if ($login === "" || strlen($login) < 5) {
        $errors["login"] = "Login trop court (min 5 caractères).";
    }
    if ($password === "" || strlen($password) < 8) {
        $errors["password"] = "Mot de passe trop court (min 8 caractères).";
    }
    if ($role !== "administrateur" && $role !== "editeur") {
        $errors["role"] = "Rôle invalide";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    $stmt = $pdo->prepare(
        "INSERT into utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES (:nom, :prenom, :login, :password, :role);",
    );
    $success = $stmt->execute([
        ":nom" => $nom,
        ":prenom" => $prenom,
        ":login" => $login,
        ":password" => $password,
        ":role" => $role,
    ]);

    if ($success) {
        $newId = $pdo->lastInsertId();
        header("Content-Type: application/json", true, 201);
        echo json_encode(["ok" => true, "id" => $newId]);
        exit();
    } else {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["error" => 'Impossible d\'ajouter l\'utilisateur.']);
        exit();
    }
} elseif ($method === "DELETE") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    $user_id = $body["userId"] ?? null;
    $errors = [];
    if (!is_numeric($user_id)) {
        $errors["user_id"] = "Utilisateur invalide";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id;");
    $success = $stmt->execute([":id" => $user_id]);

    if ($success) {
        header("Content-type: application/json", true, 200);
        echo json_encode(["ok" => true, "id" => $user_id]);
        exit();
    } else {
        header("Content-type: application/json", true, 400);
        echo json_encode(["error" => "Impossible de supprimer l' utilisateur"]);
        exit();
    }
}

?>
