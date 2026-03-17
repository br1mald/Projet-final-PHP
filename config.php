<?php

require_once __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = "localhost";
$dbname = "actualites";
$password = $_ENV["DB_PASSWORD"];
$user = $_ENV["DB_USER"];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
