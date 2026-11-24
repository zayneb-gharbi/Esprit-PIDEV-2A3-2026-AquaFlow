<?php
require_once __DIR__ . '/../config/database.php';
class UtilisateurPersistant
{
    private $db;
    public function __construct($db) { $this->db = $db; }
    public function table() { return integrationMode() === 'groupe' ? 'utilisateur' : 'users'; }
    public function idColumn() { return integrationMode() === 'groupe' ? 'id_user' : 'id'; }
    public function passwordColumn() { return integrationMode() === 'groupe' ? 'mot_de_passe' : 'password'; }
    public function statusColumn() { return integrationMode() === 'groupe' ? 'statut' : 'status'; }
    public function getById($id)
    {
        $q = $this->db->prepare('SELECT * FROM ' . $this->table() . ' WHERE ' . $this->idColumn() . '=?');
        $q->execute([$id]);
        return $this->format($q->fetch());
    }
    public function getByEmail($email)
    {
        $q = $this->db->prepare('SELECT * FROM ' . $this->table() . ' WHERE LOWER(email)=?');
        $q->execute([strtolower($email)]);
        return $this->format($q->fetch());
    }
    public function getByIdentifier($value)
    {
        $user = $this->getByEmail($value);
        if (!$user && integrationMode() === 'groupe') {
            $q = $this->db->prepare('SELECT * FROM utilisateur WHERE telephone=?');
            $q->execute([$value]);
            $user = $this->format($q->fetch());
        }
        return $user;
    }
    private function format($row)
    {
        if (!$row) { return null; }
        $group = integrationMode() === 'groupe';
        $row['id'] = (int) $row[$this->idColumn()];
        $row['nom'] = $group ? trim(($row['prenom'] ?? '') . ' ' . $row['nom']) : ($row['nom_complet'] ?: $row['username']);
        $row['password'] = $row[$this->passwordColumn()];
        $row['id_role'] = ['C'=>1,'P'=>2,'A'=>3,'citoyen'=>1,'plombier'=>2,'admin'=>3][$row['role']] ?? 0;
        $row['nom_role'] = [1=>'Citoyen',2=>'Plombier',3=>'Administrateur'][$row['id_role']] ?? 'Inconnu';
        $row['statut_code']=$row[$this->statusColumn()];
        $row['statut'] = $row[$this->statusColumn()] === 'actif' ? 'Actif' : 'Inactif';
        $row['created_at'] = $group ? $row['date_creation'] : $row['created_at'];
        $row['email'] = $row['email'] ?? '';
        $row['telephone'] = $row['telephone'] ?? '';
        $row['adresse'] = $row['adresse'] ?? '';
        $row['empreinte'] = hash('sha256', $row['password'] . '|' . $row['email'] . '|' . $row['id_role'] . '|' . $row['statut']);
        return $row;
    }
    public function create($nom, $email, $password, $role = 1)
    {
        if ($nom === '' || strlen($nom)>150 || !in_array($role,[1,2,3],true)) { throw new DomainException('Informations invalides.'); }
        if ($email !== null && (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>150)) { throw new DomainException('Adresse email invalide.'); }
        $email=$email===null?null:strtolower(trim($email));
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if (integrationMode() === 'groupe') {
            $q = $this->db->prepare("INSERT INTO utilisateur(nom,prenom,email,mot_de_passe,role,statut) VALUES (?,'',?,?,?,'actif')");
            $q->execute([$nom,$email,$hash,[1=>'C',2=>'P',3=>'A'][$role]]);
        } else {
            $username = mbSafeName($nom);
            $q = $this->db->prepare("INSERT INTO users(username,nom_complet,email,password,role,status) VALUES (?,?,?,?,?,'actif')");
            $q->execute([$username,$nom,$email,$hash,[1=>'citoyen',2=>'plombier',3=>'admin'][$role]]);
        }
        return $this->getById((int)$this->db->lastInsertId());
    }
    public function changerPassword($id, $password)
    {
        $q = $this->db->prepare('UPDATE ' . $this->table() . ' SET ' . $this->passwordColumn() . '=?,version=version+1 WHERE ' . $this->idColumn() . '=?');
        $q->execute([password_hash($password,PASSWORD_DEFAULT),$id]);
    }
    public function tous()
    {
        return array_map([$this,'format'],$this->db->query('SELECT * FROM ' . $this->table() . ' ORDER BY ' . $this->idColumn())->fetchAll());
    }
    public function getStats()
    {
                $users=$this->tous();
        return ['users'=>count($users),'citizens'=>count(array_filter($users,fn($u)=>$u['id_role']===1)),'plumbers'=>count(array_filter($users,fn($u)=>$u['id_role']===2)),'active'=>count(array_filter($users,fn($u)=>$u['statut']==='Actif'))];
    }
    public function updateProfile($id,$nom,$email,$telephone,$adresse,$prenom='')
    {
        if ($nom==='' || strlen($nom)>150 || strlen($prenom)>150 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>150 || strlen($telephone)>40 || strlen($adresse)>255) { throw new DomainException('Vérifiez les informations saisies.'); }
        $other=$this->getByEmail($email);
        if ($other && $other['id']!==(int)$id) { throw new DomainException('Cette adresse email est déjà utilisée.'); }
        if (integrationMode()==='groupe') {
            if ($telephone!=='' && !preg_match('/^(?:\+216|00216)?[2-9][0-9]{7}$/',$telephone)) { throw new DomainException('Téléphone tunisien invalide.'); }
            $q=$this->db->prepare('UPDATE utilisateur SET nom=?,prenom=?,email=?,telephone=?,adresse=?,version=version+1 WHERE id_user=?');
            $q->execute([$nom,$prenom,strtolower($email),$telephone ?: null,$adresse,$id]);
        } else {
            $q=$this->db->prepare('UPDATE users SET nom_complet=?,email=?,telephone=?,adresse=?,version=version+1 WHERE id=?');
            $q->execute([$nom,strtolower($email),$telephone,$adresse,$id]);
        }
    }
    public function deactivate($id)
    {
        $q=$this->db->prepare('UPDATE '.$this->table()." SET ".$this->statusColumn()."='bloque',version=version+1 WHERE ".$this->idColumn().'=?');$q->execute([$id]);
    }
    public function identites($id)
    {
        $q=$this->db->prepare('SELECT fournisseur FROM amir_identites WHERE utilisateur_id=?');$q->execute([$id]);return $q->fetchAll(PDO::FETCH_COLUMN);
    }
    public function idSocial($provider,$subject)
    {
        $q=$this->db->prepare('SELECT utilisateur_id FROM amir_identites WHERE fournisseur=? AND identifiant=?');$q->execute([$provider,$subject]);return $q->fetchColumn();
    }
    public function associerSocial($id,$provider,$subject)
    {
        try { $q=$this->db->prepare('INSERT INTO amir_identites(utilisateur_id,fournisseur,identifiant) VALUES (?,?,?)');$q->execute([$id,$provider,$subject]); }
        catch (PDOException $e) { throw new DomainException('Un compte de ce service est déjà associé.'); }
    }
    public function creerSocial($nom,$email,$provider,$subject)
    {
        $this->db->beginTransaction();
        try {
            $user=$this->create($nom,$email,bin2hex(random_bytes(32)),1);
            $this->associerSocial($user['id'],$provider,$subject);
            $this->db->commit();return $user;
        } catch (Throwable $e) { $this->db->rollBack();throw $e; }
    }
}
function mbSafeName($name)
{
    return (function_exists('mb_strcut') ? mb_strcut($name,0,40,'UTF-8') : substr(preg_replace('/[^a-zA-Z0-9 _-]/','',$name),0,40)) . '-' . bin2hex(random_bytes(4));
}
