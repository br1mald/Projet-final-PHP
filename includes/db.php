<?php

/** @var string $host */
/** @var string $dbname */
/** @var string $user */
/** @var string $password */
require_once __DIR__ . "/../config.php";

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

?>
