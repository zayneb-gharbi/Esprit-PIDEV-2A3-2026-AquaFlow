<?php
// Configuration WAMP par défaut. Modifiez ces valeurs si votre MySQL utilise un mot de passe.
$host = 'localhost';
$dbname = 'aquaflow';
$user = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES => false]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Erreur de connexion à la base de données. Vérifiez config/database.php et importez database/aquaflow.sql.');
}
