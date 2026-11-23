<?php
namespace App\Models;

use PDO;
use Exception;

// Model.php : connexion PDO (Chapitre 6 : config.php)
class Model
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new PDO(
                    'oci:dbname=//localhost:1521/XE',   // XE = your Oracle service name
                    'AQUAFLOW',
                    'aquaflow69@',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_CASE => PDO::CASE_LOWER   // Oracle returns column names in UPPERCASE
                    ]
                );
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
