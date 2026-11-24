<?php
require_once '/app/amir/config/bootstrap.php';
require_once '/app/amir/models/Installation.php';
require_once '/app/amir/models/Importateur.php';
$db=AmirDatabase::connexion();Installation::schema($db);$m=new UtilisateurPersistant($db);
$source=new PDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$source->exec('CREATE TABLE amir_utilisateurs(id INTEGER PRIMARY KEY,nom TEXT,email TEXT,password TEXT,id_role INT,statut TEXT,telephone TEXT,adresse TEXT,created_at TEXT,totp_secret TEXT,totp_last INT,version INT)');
$source->exec('CREATE TABLE amir_identites(utilisateur_id INT,fournisseur TEXT,identifiant TEXT)');
$source->exec('CREATE TABLE amir_secours(utilisateur_id INT,empreinte TEXT)');
$hash=password_hash('ImportPassword42!',PASSWORD_DEFAULT);$secret=AmirTotp::chiffrer('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ');
$source->prepare("INSERT INTO amir_utilisateurs VALUES(42,'Ancien compte','import@test.test',?,1,'Actif','22123456','Tunis','2026-10-01 10:30:00',?,123,8)")->execute([$hash,$secret]);
$source->exec("INSERT INTO amir_identites VALUES(42,'google','import-subject')");$source->prepare('INSERT INTO amir_secours VALUES(42,?)')->execute([hash('sha256','abcd1234abcd1234')]);
$sources=[['pdo'=>$source,'nom'=>'source-test','type'=>'amir','cle'=>'/app/amir/var/cle.txt']];$i=new Importateur($db);
$r=$i->executer($sources);if ($r['comptes']!==1 || $m->getByEmail('import@test.test')) { throw new RuntimeException('Simulation modifie la base.'); }
$r=$i->executer($sources,true);$user=$m->getByEmail('import@test.test');
if ($r['comptes']!==1 || !$user || !password_verify('ImportPassword42!',$user['password']) || $user['totp_secret']!==$secret || $user['version']!=8 || $user['nom']!=='Ancien compte' || $user['created_at']!=='2026-10-01 10:30:00') { throw new RuntimeException('Données perdues pendant import.'); }
if ((int)$m->idSocial('google','import-subject')!==$user['id']) { throw new RuntimeException('Identité perdue.'); }
$r=$i->executer($sources,true);if ($r['comptes']!==0 || $r['deja_importes']!==1) { throw new RuntimeException('Import non idempotent.'); }
$sources[0]['nom']='autre-source';$refused=false;try{$i->executer($sources,true);}catch(DomainException $e){$refused=true;}
if (!$refused || count($m->tous())!==1) { throw new RuntimeException('Doublon écrasé.'); }
if ($source->query('SELECT password FROM amir_utilisateurs')->fetchColumn()!==$hash) { throw new RuntimeException('Source modifiée.'); }
echo '6';
