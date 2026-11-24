<?php
if (PHP_SAPI!=='cli') { http_response_code(404);exit; }
require_once __DIR__.'/../models/UtilisateurPersistant.php';
require_once __DIR__.'/../models/Installation.php';
try {
    $c=amirConfig();
    if (!preg_match('/^[a-zA-Z0-9_]+$/',$c['db_name'])) { throw new RuntimeException('Nom de base invalide.'); }
    if (!getenv('AQUAFLOW_TEST_DB')) {
        $server=new PDO('mysql:host='.$c['db_host'].';port='.(int)$c['db_port'].';charset=utf8mb4',$c['db_user'],$c['db_password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
        $server->exec('CREATE DATABASE IF NOT EXISTS `'.$c['db_name'].'` CHARACTER SET utf8mb4');
    }
    $db=AmirDatabase::connexion();Installation::schema($db);
    $var=__DIR__.'/../var';if (!is_dir($var)) { mkdir($var,0700,true); }
    if (!is_file($var.'/cle.txt')) {
        $m=new UtilisateurPersistant($db);
        if ((int)$db->query('SELECT COUNT(*) FROM '.$m->table().' WHERE totp_secret IS NOT NULL')->fetchColumn()>0) { throw new RuntimeException('Restaure la clé existante.'); }
        file_put_contents($var.'/cle.txt',bin2hex(random_bytes(32)),LOCK_EX);chmod($var.'/cle.txt',0600);
    }
    if (in_array('--demo',$argv,true)) {
        $model=new UtilisateurPersistant($db);$text='';
        foreach ([[3,'Administrateur','admin@example.test'],[1,'Citoyen','citoyen@example.test'],[2,'Plombier','plombier@example.test']] as [$role,$name,$email]) {
            if (!$model->getByEmail($email)) {
                $password='Af!'.bin2hex(random_bytes(8));$model->create($name,$email,$password,$role);
                $text.=$name."\nEmail : ".$email."\nMot de passe : ".$password."\n\n";
            }
        }
        if ($text!=='') { file_put_contents($var.'/MES_COMPTES.txt',$text,FILE_APPEND|LOCK_EX);chmod($var.'/MES_COMPTES.txt',0600); }
    }
    echo "Installation terminée. Aucune base existante supprimée.\n";
} catch (Throwable $e) { fwrite(STDERR,"Installation impossible : vérifier MySQL, PDO et config/local.php. Utiliser une base distincte pour l'intégration.\n");exit(1); }
