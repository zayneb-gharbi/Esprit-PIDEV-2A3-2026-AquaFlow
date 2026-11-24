<?php
class Importateur
{
    private $db;
    private $model;
    public function __construct($db) { $this->db=$db;$this->model=new UtilisateurPersistant($db); }
    private function rows($source,$table,$optional=false)
    {
        try { return $source->query('SELECT * FROM '.$table)->fetchAll(PDO::FETCH_ASSOC); }
        catch (PDOException $e) {
            if ($optional && (in_array($e->getCode(),['42S02','42P01'],true) || str_contains($e->getMessage(),'no such table') || str_contains($e->getMessage(),'ORA-00942'))) { return []; }
            throw $e;
        }
    }
    private function date($value)
    {
        return $value ? (new DateTimeImmutable((string)$value))->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
    }
    public function executer($sources,$apply=false)
    {
        $report=['comptes'=>0,'identites'=>0,'secours'=>0,'historiques'=>0,'deja_importes'=>0];$key=null;
        $keyPath=__DIR__.'/../var/cle.txt';$oldKey=is_file($keyPath)?trim(file_get_contents($keyPath)):null;
        $hasTotp=(int)$this->db->query('SELECT COUNT(*) FROM '.$this->model->table().' WHERE totp_secret IS NOT NULL')->fetchColumn()>0;
        $this->db->beginTransaction();
        try {
            foreach ($sources as $source) {
                $type=$source['type'];$name=$source['nom'];
                if (!in_array($type,['amir','yasmine','groupe'],true) || !preg_match('/^[a-zA-Z0-9_-]{1,40}$/',$name)) { throw new DomainException('Source invalide.'); }
                $table=['amir'=>'amir_utilisateurs','yasmine'=>'users','groupe'=>'utilisateur'][$type];$map=[];$new=[];
                foreach ($this->rows($source['pdo'],$table) as $r) {
                    $r=array_change_key_case($r,CASE_LOWER);$oldId=(int)($r['id_user'] ?? $r['id']);
                    $q=$this->db->prepare('SELECT nouveau_id FROM integration_imports WHERE source=? AND ancien_id=?');$q->execute([$name,$oldId]);$known=$q->fetchColumn();
                    if ($known!==false) { if (!$this->model->getById($known)) { throw new DomainException('Un compte déjà importé a été supprimé.'); } $map[$oldId]=(int)$known;$report['deja_importes']++;continue; }
                    $email=$r['email'] ?? null;
                    if ($email && $this->model->getByEmail($email)) { throw new DomainException('Doublon email dans la source '.$name.' (compte '.$oldId.'). Aucun compte fusionné automatiquement.'); }
                    $role=['1'=>1,'2'=>2,'3'=>3,'C'=>1,'P'=>2,'A'=>3,'citoyen'=>1,'plombier'=>2,'admin'=>3][(string)($r['id_role'] ?? $r['role'])] ?? null;
                    if (!$role) { throw new DomainException('Rôle inconnu dans la source '.$name.'.'); }
                    $nom=$type==='groupe'?trim(($r['prenom'] ?? '').' '.$r['nom']):($r['nom_complet'] ?? $r['nom'] ?? $r['username']);
                    $u=$this->model->create($nom,$email,bin2hex(random_bytes(32)),$role);$id=$u['id'];
                    $statut=strtolower($r['statut'] ?? $r['status'] ?? 'bloque');$statut=['actif'=>'actif','en_attente'=>'en_attente','bloque'=>'bloque','inactif'=>'bloque','desactive'=>'bloque'][$statut] ?? 'bloque';
                    $secret=$r['totp_secret'] ?? null;
                    if ($secret) {
                        $sourceKey=is_file($source['cle'] ?? '')?trim(file_get_contents($source['cle'])):'';
                        if (!preg_match('/^[a-f0-9]{64}$/',$sourceKey) || ($key!==null && !hash_equals($key,$sourceKey)) || ($hasTotp && !hash_equals($oldKey ?? '',$sourceKey))) { throw new DomainException('Clé 2FA absente ou différente. Import annulé ; conserver les clés existantes.'); }
                        $key=$sourceKey;
                    }
                    $hash=$r['mot_de_passe'] ?? $r['password'];
                    if (empty(password_get_info($hash)['algo'])) { throw new DomainException('Mot de passe non haché dans la source '.$name.'.'); }
                    $created=$this->date($r['created_at'] ?? $r['date_creation'] ?? null);
                    $q=$this->db->prepare('UPDATE '.$this->model->table().' SET '.$this->model->passwordColumn().'=?,'.$this->model->statusColumn().'=?,totp_secret=?,totp_last=?,version=?,telephone=?,adresse=?,'.(integrationMode()==='groupe'?'date_creation':'created_at').'=? WHERE '.$this->model->idColumn().'=?');
                    $q->execute([$hash,$statut,$secret,$r['totp_last'] ?? -1,$r['version'] ?? 1,($r['telephone'] ?? '') ?: null,$r['adresse'] ?? '',$created,$id]);
                    if (integrationMode()==='groupe') {
                        $q=$this->db->prepare('UPDATE utilisateur SET nom=?,prenom=?,zone_service=?,experience=? WHERE id_user=?');
                        $q->execute([$r['nom'] ?? $nom,$r['prenom'] ?? '',$r['zone_service'] ?? null,$r['experience'] ?? null,$id]);
                    }
                    $this->db->prepare('INSERT INTO integration_imports(source,ancien_id,nouveau_id) VALUES (?,?,?)')->execute([$name,$oldId,$id]);
                    $map[$oldId]=$id;$new[$oldId]=$id;$report['comptes']++;
                }
                foreach (['amir_identites'=>'identites','amir_secours'=>'secours'] as $table=>$counter) {
                    foreach ($this->rows($source['pdo'],$table,true) as $r) {
                        $r=array_change_key_case($r,CASE_LOWER);$oldId=(int)$r['utilisateur_id'];if (!isset($new[$oldId])) { continue; }
                        if ($table==='amir_identites') { $this->model->associerSocial($new[$oldId],$r['fournisseur'],$r['identifiant']); }
                        else { $this->db->prepare('INSERT INTO amir_secours(utilisateur_id,empreinte) VALUES (?,?)')->execute([$new[$oldId],$r['empreinte']]); }
                        $report[$counter]++;
                    }
                }
                if (integrationMode()==='groupe') {
                    foreach ($this->rows($source['pdo'],'historique',true) as $r) {
                        $r=array_change_key_case($r,CASE_LOWER);$oldId=(int)$r['id_user'];if (!isset($new[$oldId])) { continue; }
                        $this->db->prepare('INSERT INTO historique(id_user,action,description,date_action) VALUES (?,?,?,?)')->execute([$new[$oldId],$r['action'],$r['description'] ?? null,$this->date($r['date_action'])]);$report['historiques']++;
                    }
                }
            }
            if ($apply) {
                if ($key!==null && !hash_equals($key,$oldKey ?? '')) {
                    if (file_put_contents($keyPath,$key,LOCK_EX)!==64) { throw new RuntimeException('Impossible de conserver la clé 2FA.'); }
                    chmod($keyPath,0600);
                }
                $this->db->commit();
            } else { $this->db->rollBack(); }
            return $report;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) { $this->db->rollBack(); }
            if ($apply && $oldKey!==null) { file_put_contents($keyPath,$oldKey,LOCK_EX); }
            throw $e;
        }
    }
}
