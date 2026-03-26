<?php
/**@var PDO $pdo */
session_start();
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/response.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "POST") {
    $raw = file_get_contents("php://input");
    $body = json_decode($raw, true);

    $login = trim($body["login"] ?? "");
    $motdepasse = trim($body["motdepasse"] ?? "");

    if ($login === "" || $motdepasse === "") {
        json_error("Login et mot de passe requis.", 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($motdepasse, $user["mot_de_passe"])) {
        json_error("Login ou mot de passe incorrect.", 401);
    }

    $_SESSION["id"] = $user["id"];
    $_SESSION["nom"] = $user["nom"];
    $_SESSION["role"] = $user["role"];

    json_response(["ok" => true, "role" => $user["role"]]);
} elseif ($method === "DELETE") {
    // Logout
    session_unset();
    session_destroy();
    json_response(["ok" => true, "message" => "Déconnecté"]);
}
