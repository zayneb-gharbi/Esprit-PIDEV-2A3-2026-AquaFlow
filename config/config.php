<?php
/**
 * Configuration centrale du projet AquaFlow.
 * Aucun secret reel n'est versionne : les valeurs XAMPP sont les valeurs par defaut
 * et peuvent etre surchargees par variables d'environnement.
 */
return [
    'app' => [
        'name'     => 'AquaFlow',
        'timezone' => 'Africa/Tunis',
    ],
    'db' => [
        'host'    => getenv('DB_HOST') ?: '127.0.0.1',
        'port'    => getenv('DB_PORT') ?: '3306',
        'name'    => getenv('DB_NAME') ?: 'aquaflow',
        'user'    => getenv('DB_USER') ?: 'root',
        'pass'    => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
        'charset' => 'utf8mb4',
    ],
];
