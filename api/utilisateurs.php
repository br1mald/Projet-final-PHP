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
    $prenom = htmlspecialchars(trim($body["prenom"] ?? ""), ENT_QUOTES, "UTF-8");
    $login = htmlspecialchars(trim($body["login"] ?? ""), ENT_QUOTES, "UTF-8");
    $mot_de_passe = $body["mot_de_passe"] ?? "";
    $confirm_password = $body["confirm_password"] ?? "";
    $role = htmlspecialchars(trim($body["role"] ?? ""), ENT_QUOTES, "UTF-8");
    $date = $body["date"] ?? null;

    $errors = [];
    
    if ($nom === "" || strlen($nom) < 2) {
        $errors["nom"] = "Nom invalide (min 2 caractères).";
    }
    if ($prenom === "" || strlen($prenom) < 2) {
        $errors["prenom"] = "Prénom invalide (min 2 caractères).";
    }
    if ($login === "" || strlen($login) < 3) {
        $errors["login"] = "Login invalide (min 3 caractères).";
    }
    if ($mot_de_passe === "" || strlen($mot_de_passe) < 6) {
        $errors["mot_de_passe"] = "Mot de passe trop court (min 6 caractères).";
    }
    if ($mot_de_passe !== $confirm_password) {
        $errors["confirm_password"] = "Les mots de passe ne correspondent pas.";
    }
    if (!in_array($role, ['administrateur', 'editeur', 'visiteur'])) {
        $errors["role"] = "Rôle invalide.";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    // Vérifier si le login existe déjà
    $stmt_check = $pdo->prepare("SELECT id FROM utilisateurs WHERE login = :login");
    $stmt_check->execute([":login" => $login]);
    if ($stmt_check->fetch()) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["error" => "Ce login est déjà utilisé."]);
        exit();
    }

    // Hasher le mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        "INSERT into utilisateurs (nom, prenom, login, mot_de_passe, role, date) VALUES (:nom, :prenom, :login, :mot_de_passe, :role, :date);",
    );
    $success = $stmt->execute([
        ":nom" => $nom,
        ":prenom" => $prenom,
        ":login" => $login,
        ":mot_de_passe" => $mot_de_passe_hash,
        ":role" => $role,
        ":date" => $date,
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
} elseif ($method === "PUT" || $method === "PATCH") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    // if there is an error with the js script
    if (!is_array($body) || empty($body)) {
        $body = $_POST;
    }

    $user_id = $body["id"] ?? null;
    $nom = htmlspecialchars(trim($body["nom"] ?? ""), ENT_QUOTES, "UTF-8");
    $prenom = htmlspecialchars(trim($body["prenom"] ?? ""), ENT_QUOTES, "UTF-8");
    $login = htmlspecialchars(trim($body["login"] ?? ""), ENT_QUOTES, "UTF-8");
    $mot_de_passe = $body["mot_de_passe"] ?? "";
    $confirm_password = $body["confirm_password"] ?? "";
    $role = htmlspecialchars(trim($body["role"] ?? ""), ENT_QUOTES, "UTF-8");
    $date = $body["date"] ?? null;

    $errors = [];
    if (!is_numeric($user_id)) {
        $errors["id"] = "ID d'utilisateur invalide.";
    }
    if ($nom === "" || strlen($nom) < 2) {
        $errors["nom"] = "Nom invalide (min 2 caractères).";
    }
    if ($prenom === "" || strlen($prenom) < 2) {
        $errors["prenom"] = "Prénom invalide (min 2 caractères).";
    }
    if ($login === "" || strlen($login) < 3) {
        $errors["login"] = "Login invalide (min 3 caractères).";
    }
    if ($mot_de_passe !== "" && strlen($mot_de_passe) < 6) {
        $errors["mot_de_passe"] = "Mot de passe trop court (min 6 caractères).";
    }
    if ($mot_de_passe !== "" && $mot_de_passe !== $confirm_password) {
        $errors["confirm_password"] = "Les mots de passe ne correspondent pas.";
    }
    if (!in_array($role, ['administrateur', 'editeur', 'visiteur'])) {
        $errors["role"] = "Rôle invalide.";
    }

    if (!empty($errors)) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["errors" => $errors]);
        exit();
    }

    // Vérifier que l'utilisateur existe
    $stmt_check = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = :id");
    $stmt_check->execute([":id" => $user_id]);
    if (!$stmt_check->fetch()) {
        header("Content-Type: application/json", true, 404);
        echo json_encode(["error" => "Utilisateur non trouvé."]);
        exit();
    }

    // Vérifier si le login existe déjà (pour un autre utilisateur)
    $stmt_login = $pdo->prepare("SELECT id FROM utilisateurs WHERE login = :login AND id != :id");
    $stmt_login->execute([":login" => $login, ":id" => $user_id]);
    if ($stmt_login->fetch()) {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["error" => "Ce login est déjà utilisé."]);
        exit();
    }

    // Préparer la requête de mise à jour
    $update_fields = [
        "nom = :nom",
        "prenom = :prenom", 
        "login = :login",
        "role = :role",
        "date = :date"
    ];
    
    $params = [
        ":nom" => $nom,
        ":prenom" => $prenom,
        ":login" => $login,
        ":role" => $role,
        ":date" => $date,
        ":id" => $user_id
    ];

    // Ajouter le mot de passe seulement si fourni
    if ($mot_de_passe !== "") {
        $update_fields[] = "mot_de_passe = :mot_de_passe";
        $params[":mot_de_passe"] = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    }

    $sql = "UPDATE utilisateurs SET " . implode(", ", $update_fields) . " WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute($params);

    if ($success) {
        header("Content-Type: application/json", true, 200);
        echo json_encode(["ok" => true, "id" => $user_id]);
        exit();
    } else {
        header("Content-Type: application/json", true, 400);
        echo json_encode(["error" => "Impossible de modifier l'utilisateur."]);
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
        echo json_encode(["error" => "Impossible de supprimer l'utilisateur"]);
        exit();
    }
}
?>
