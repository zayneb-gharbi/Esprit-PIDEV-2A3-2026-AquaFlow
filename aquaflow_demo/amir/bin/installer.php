<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../models/UtilisateurPersistant.php';
try {
    $c = amirConfig();
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $c['db_name'])) { throw new RuntimeException('Nom de base invalide.'); }
    $server = new PDO('mysql:host=' . $c['db_host'] . ';port=' . (int) $c['db_port'] . ';charset=utf8mb4', $c['db_user'], $c['db_password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $server->exec('CREATE DATABASE IF NOT EXISTS `' . $c['db_name'] . '` CHARACTER SET utf8mb4');
    $db = AmirDatabase::connexion();
    $db->exec(file_get_contents(__DIR__ . '/../database/mysql.sql'));
    $var = __DIR__ . '/../var';
    if (!is_dir($var)) { mkdir($var, 0700, true); }
    if (!is_file($var . '/cle.txt')) {
        if ((int) $db->query('SELECT COUNT(*) FROM amir_utilisateurs WHERE totp_secret IS NOT NULL')->fetchColumn() > 0) { throw new RuntimeException('Restaure var/cle.txt depuis ta sauvegarde avant de continuer.'); }
        file_put_contents($var . '/cle.txt', bin2hex(random_bytes(32)), LOCK_EX);
        chmod($var . '/cle.txt', 0600);
    }
    $model = new UtilisateurPersistant($db);
    $accounts = [[3,'Administrateur de test','admin@example.test'],[1,'Citoyen de test','citoyen@example.test'],[2,'Plombier de test','plombier@example.test']];
    $text = '';
    foreach ($accounts as [$role,$name,$email]) {
        if (!$model->getByEmail($email)) {
            $password = 'Af!' . bin2hex(random_bytes(8));
            $model->create($name, $email, $password, $role);
            $text .= $name . "\nEmail : " . $email . "\nMot de passe : " . $password . "\n\n";
        }
    }
    if ($text !== '') {
        file_put_contents($var . '/MES_COMPTES.txt', $text, FILE_APPEND | LOCK_EX);
        chmod($var . '/MES_COMPTES.txt', 0600);
    }
    echo "Installation terminee. Comptes de test : amir/var/MES_COMPTES.txt\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Installation impossible. Verifie que MySQL fonctionne, que PHP dispose de pdo_mysql et que config/local.php contient les bons parametres.\n");
    exit(1);
}
