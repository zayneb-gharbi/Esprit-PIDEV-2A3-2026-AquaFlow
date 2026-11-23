<?php
namespace App\Models;

use PDO;
use Exception;

/**
 * Model — unique point de connexion PDO a la base `aquaflow`.
 * Les 5 tables des deux modules (utilisateurs + evenements) vivent dans cette base.
 */
class Model
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $config = require dirname(__DIR__, 2) . '/config/config.php';
            $db = $config['db'];
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset={$db['charset']}";

            try {
                self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (Exception $e) {
                die('Erreur de connexion a la base de donnees : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
