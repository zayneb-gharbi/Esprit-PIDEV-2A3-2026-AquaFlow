<?php
require_once __DIR__ . '/../../models/Utilisateur.php';
require_once __DIR__ . '/../config/database.php';
class UtilisateurPersistant extends Utilisateur
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getById($id)
    {
        $requete = $this->db->prepare('SELECT * FROM amir_utilisateurs WHERE id=?');
        $requete->execute([$id]);
        return $this->format($requete->fetch());
    }

    public function getByEmail($email)
    {
        $requete = $this->db->prepare('SELECT id FROM amir_utilisateurs WHERE email=?');
        $requete->execute([strtolower($email)]);
        $id = $requete->fetchColumn();
        return $id === false ? null : $this->getById($id);
    }

    private function format($user)
    {
        if (!$user) {
            return null;
        }
        $user['email'] = $user['email'] ?? '';
        $user['nom_role'] = [1 => 'Citoyen', 2 => 'Plombier', 3 => 'Administrateur'][$user['id_role']];
        return $user;
    }

    public function create($nom, $email, $password, $role = 1)
    {
        if ($nom === '' || strlen($nom) > 150 || !in_array($role, [1, 2, 3], true)) {
            throw new DomainException('Informations du compte invalides.');
        }
        if ($email !== null && (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190)) {
            throw new DomainException('Adresse email invalide.');
        }
        $requete = $this->db->prepare('INSERT INTO amir_utilisateurs(nom,email,password,id_role,statut) VALUES (?,?,?,?,?)');
        $requete->execute([$nom, $email === null ? null : strtolower($email), password_hash($password, PASSWORD_DEFAULT),
                $role, 'Actif']);
        return $this->getById((int) $this->db->lastInsertId());
    }

    public function updateProfile($id, $nom, $email, $telephone, $adresse)
    {
        if ($nom === '' || strlen($nom) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190
            || strlen($telephone) > 40 || strlen($adresse) > 255) {
            throw new DomainException('Vérifie le nom, l’email et la longueur des informations.');
        }
        $existant = $this->getByEmail($email);
        if ($existant && (int) $existant['id'] !== (int) $id) {
            throw new DomainException('Cette adresse email est déjà utilisée.');
        }
        $requete = $this->db->prepare('UPDATE amir_utilisateurs SET nom=?,email=?,telephone=?,adresse=?,version=version+1 WHERE id=?');
        $requete->execute([$nom, strtolower($email), $telephone, $adresse, $id]);
        return true;
    }

    public function deactivate($id)
    {
        $requete = $this->db->prepare("UPDATE amir_utilisateurs SET statut='Inactif',version=version+1 WHERE id=?");
        $requete->execute([$id]);
        return true;
    }

    private function liste($role)
    {
        $requete = $this->db->prepare('SELECT * FROM amir_utilisateurs WHERE id_role=? ORDER BY nom,id');
        $requete->execute([$role]);
        return array_map([$this, 'format'], $requete->fetchAll());
    }

    public function getCitizens() {
        return $this->liste(1);
    }

    public function getPlumbers() {
        return $this->liste(2);
    }

    public function getStats()
    {
        return $this->db->query("SELECT COUNT(*) AS users, COALESCE(SUM(id_role=1),0) AS citizens, COALESCE(SUM(id_role=2),0) AS plumbers, COALESCE(SUM(statut='Actif'),0) AS active FROM amir_utilisateurs")->fetch();
    }

    public function identites($id)
    {
        $requete = $this->db->prepare('SELECT fournisseur FROM amir_identites WHERE utilisateur_id=?');
        $requete->execute([$id]);
        return $requete->fetchAll(PDO::FETCH_COLUMN);
    }

    public function idSocial($provider, $subject)
    {
        $requete = $this->db->prepare('SELECT utilisateur_id FROM amir_identites WHERE fournisseur=? AND identifiant=?');
        $requete->execute([$provider, $subject]);
        return $requete->fetchColumn();
    }

    public function associerSocial($id, $provider, $subject)
    {
        try {
            $requete = $this->db->prepare('INSERT INTO amir_identites(utilisateur_id,fournisseur,identifiant) VALUES (?,?,?)');
            $requete->execute([$id, $provider, $subject]);
        } catch (PDOException $e) {
            throw new DomainException('Un compte de ce service est déjà associé.');
        }
    }

    public function creerSocial($nom, $email, $provider, $subject)
    {
        $this->db->beginTransaction();
        try {
            $user = $this->create($nom, $email, bin2hex(random_bytes(32)), 1);
            $this->associerSocial($user['id'], $provider, $subject);
            $this->db->commit();
            return $user;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
