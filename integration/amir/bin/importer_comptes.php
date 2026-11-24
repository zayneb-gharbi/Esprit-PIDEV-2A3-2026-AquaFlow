<?php
if (PHP_SAPI!=='cli') { http_response_code(404);exit; }
require_once __DIR__.'/../models/UtilisateurPersistant.php';
require_once __DIR__.'/../models/Importateur.php';
try {
    $file=__DIR__.'/../config/import.local.php';
    if (!is_file($file)) { throw new DomainException('Copier config/import.example.php en config/import.local.php puis renseigner les sources locales.'); }
    $sources=require $file;
    foreach ($sources as &$source) {
        $source['pdo']=new PDO($source['dsn'],$source['user'] ?? null,$source['password'] ?? null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_CASE=>PDO::CASE_LOWER]);
        unset($source['dsn'],$source['user'],$source['password']);
    }
    unset($source);
    $apply=in_array('--appliquer',$argv,true);
    $report=(new Importateur(AmirDatabase::connexion()))->executer($sources,$apply);
    echo ($apply?'Import terminé.':'Vérification terminée, aucun changement conservé. Relancer avec --appliquer pour importer.').PHP_EOL;
    foreach ($report as $key=>$count) { echo $key.' : '.$count.PHP_EOL; }
} catch (DomainException $e) { fwrite(STDERR,$e->getMessage().PHP_EOL);exit(1); }
catch (Throwable $e) { fwrite(STDERR,"Import annulé. Vérifier les connexions, les types de sources et les éventuels doublons. Aucun mot de passe affiché.\n");exit(1); }
