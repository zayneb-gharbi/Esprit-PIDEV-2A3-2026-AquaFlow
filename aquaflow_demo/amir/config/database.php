<?php
require_once __DIR__ . '/application.php';
class AmirDatabase
{
    public static function connexion()
    {
        static $db = null;
        if ($db === null) {
            $c = amirConfig();
            $test = getenv('AQUAFLOW_TEST_DB');
            $dsn = $test ? 'sqlite:' . $test : 'mysql:host=' . $c['db_host'] . ';port=' . (int) $c['db_port'] . ';dbname=' . $c['db_name'] . ';charset=utf8mb4';
            $db = new PDO($dsn, $test ? null : $c['db_user'], $test ? null : $c['db_password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            if ($test) { $db->exec('PRAGMA foreign_keys=ON'); }
        }
        return $db;
    }
}
