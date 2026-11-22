<?php
// ------------------------------------------------------------
// CRUD UTILISATEUR : uniquement ce qui est dans les chapitres 5, 6 et 7
// ------------------------------------------------------------

// ---------- Connexion (Chapitre 6 : config.php) ----------
class config
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

// ---------- Classe (Chapitre 5 : attributs private + constructeur + getters) ----------
class Utilisateur
{
    private $id_user;
    private $nom;
    private $prenom;
    private $email;
    private $telephone;
    private $mot_de_passe;
    private $role;
    private $zone_service;
    private $experience;
    private $statut;

    public function __construct($id_user, $nom, $prenom, $email, $telephone, $mot_de_passe, $role,
                                $zone_service = null, $experience = null, $statut = 'actif')
    {
        $this->id_user = $id_user;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->mot_de_passe = $mot_de_passe;
        $this->role = $role;
        $this->zone_service = $zone_service;
        $this->experience = $experience;
        $this->statut = $statut;
    }

    public function getIdUser() { return $this->id_user; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getTelephone() { return $this->telephone; }
    public function getMotDePasse() { return $this->mot_de_passe; }
    public function getRole() { return $this->role; }
    public function getZoneService() { return $this->zone_service; }
    public function getExperience() { return $this->experience; }
    public function getStatut() { return $this->statut; }
}

// ---------- Create (INSERT INTO) ----------
// id_user est NULL : le trigger Oracle genere le numero automatiquement
function addUtilisateur($utilisateur)
{
    $sql = "INSERT INTO utilisateur (id_user, nom, prenom, email, telephone, mot_de_passe, role, statut, zone_service, experience)
            VALUES (NULL, :nom, :prenom, :email, :telephone, :mot_de_passe, :role, :statut, :zone_service, :experience)";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute([
            'nom'          => $utilisateur->getNom(),
            'prenom'       => $utilisateur->getPrenom(),
            'email'        => $utilisateur->getEmail(),
            'telephone'    => $utilisateur->getTelephone(),
            'mot_de_passe' => $utilisateur->getMotDePasse(),
            'role'         => $utilisateur->getRole(),
            'statut'       => $utilisateur->getStatut(),
            'zone_service' => $utilisateur->getZoneService(),
            'experience'   => $utilisateur->getExperience()
        ]);
        echo $query->rowCount() . " record(s) INSERTED";
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

// ---------- Read (SELECT) : tous les utilisateurs ----------
function getUtilisateurs()
{
    $sql = "SELECT * FROM utilisateur";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

// ---------- Read (SELECT) : un seul utilisateur (fetch) ----------
function getUtilisateur($id)
{
    $sql = "SELECT * FROM utilisateur WHERE id_user = :id";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->fetch();
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

// ---------- Update (UPDATE) ----------
function updateUtilisateur($utilisateur, $id)
{
    try {
        $db = config::getConnexion();
        $query = $db->prepare('UPDATE utilisateur SET
                nom = :nom, prenom = :prenom, email = :email,
                telephone = :telephone, mot_de_passe = :mot_de_passe, role = :role
                WHERE id_user = :id');
        $query->execute([
            'id'           => $id,
            'nom'          => $utilisateur->getNom(),
            'prenom'       => $utilisateur->getPrenom(),
            'email'        => $utilisateur->getEmail(),
            'telephone'    => $utilisateur->getTelephone(),
            'mot_de_passe' => $utilisateur->getMotDePasse(),
            'role'         => $utilisateur->getRole()
        ]);
        echo $query->rowCount() . " records UPDATED";
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

// ---------- Delete (DELETE) ----------
function deleteUtilisateur($id)
{
    try {
        $db = config::getConnexion();
        $query = $db->prepare('DELETE FROM utilisateur WHERE id_user = :id');
        $query->execute(['id' => $id]);
        echo $query->rowCount() . " record(s) DELETED";
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

// ---------- Read : un utilisateur par email OU telephone (pour le login) ----------
function getUtilisateurByIdentifiant($identifiant)
{
    $sql = "SELECT * FROM utilisateur WHERE email = :email OR telephone = :telephone";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute(['email' => $identifiant, 'telephone' => $identifiant]);
        return $query->fetch();
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}