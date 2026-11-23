<?php
namespace App\Models;

use PDO;
use Exception;

// Model.php : connexion PDO MySQL (port XAMPP de la base Oracle d'origine).
// Les identifiants ne sont pas ecrits dans le depot : valeurs par defaut XAMPP
// + surcharge possible via variables d'environnement (voir README_ISRA).
class Model
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $name = getenv('DB_NAME') ?: 'aquaflow';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS');
            if ($pass === false) {
                $pass = '';   // XAMPP : mot de passe vide par defaut
            }

            try {
                self::$pdo = new PDO(
                    "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (Exception $e) {
                die('Erreur de connexion a la base de donnees : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
