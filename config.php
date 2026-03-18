<?php

require_once __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = "localhost";
$dbname = "actualites";
$password = $_ENV["DB_PASSWORD"];
$user = $_ENV["DB_USER"];

?>
