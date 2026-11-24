<?php
require_once __DIR__ . '/../config/database.php';

class SecuriteUtilisateur
{
    private $db;
    private $table;
    private $id;
    private $status;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = integrationMode() === "groupe" ? "utilisateur" : "users";
        $this->id = integrationMode() === "groupe" ? "id_user" : "id";
        $this->status = integrationMode() === "groupe" ? "statut" : "status";
    }

    public function limiter($cle, $maximum)
    {
        $cle = hash('sha256', $cle);
        $this->db->prepare('DELETE FROM amir_tentatives WHERE instant<?')->execute([time() - 900]);
        $requete = $this->db->prepare('SELECT COUNT(*) FROM amir_tentatives WHERE cle=?');
        $requete->execute([$cle]);
        if ((int) $requete->fetchColumn() >= $maximum) {
            throw new DomainException('Trop de tentatives. Réessaie dans 15 minutes.');
        }
        $this->db->prepare('INSERT INTO amir_tentatives(cle,instant) VALUES (?,?)')->execute([$cle, time()]);
    }

    public function utilisateurActif($id, $version)
    {
        $requete = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->id}=? AND version=? AND {$this->status}='actif'");
        $requete->execute([$id, $version]);
        return $requete->fetch();
    }

    public function activer2fa($id, $version, $secret, $step, $empreintes)
    {
        $this->db->beginTransaction();
        try {
            $requete = $this->db->prepare("UPDATE {$this->table} SET totp_secret=?,totp_last=?,version=version+1 WHERE {$this->id}=? AND version=? AND {$this->status}='actif' AND totp_secret IS NULL");
            $requete->execute([$secret, $step, $id, $version]);
            if ($requete->rowCount() !== 1) {
                throw new DomainException('Le compte a changé. Reconnecte-toi.');
            }
            $requete = $this->db->prepare('INSERT INTO amir_secours(utilisateur_id,empreinte) VALUES (?,?)');
            foreach ($empreintes as $empreinte) {
                $requete->execute([$id, $empreinte]);
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function consommerSecours($id, $version, $empreinte)
    {
        $requete = $this->db->prepare("DELETE FROM amir_secours WHERE utilisateur_id=? AND empreinte=? AND EXISTS (SELECT 1 FROM {$this->table} WHERE {$this->id}=? AND version=? AND {$this->status}='actif' AND totp_secret IS NOT NULL)");
        $requete->execute([$id, $empreinte, $id, $version]);
        return $requete->rowCount() === 1;
    }

    public function consommerTotp($id, $version, $secret, $step)
    {
        $requete = $this->db->prepare("UPDATE {$this->table} SET totp_last=? WHERE {$this->id}=? AND version=? AND totp_last<? AND {$this->status}='actif' AND totp_secret=?");
        $requete->execute([$step, $id, $version, $step, $secret]);
        return $requete->rowCount() === 1;
    }

    public function desactiver2fa($id, $version)
    {
        $this->db->beginTransaction();
        try {
            $requete = $this->db->prepare("UPDATE {$this->table} SET totp_secret=NULL,totp_last=-1,version=version+1 WHERE {$this->id}=? AND version=? AND {$this->status}='actif'");
            $requete->execute([$id, $version]);
            if ($requete->rowCount() !== 1) {
                throw new DomainException('Le compte a changé. Reconnecte-toi.');
            }
            $this->db->prepare('DELETE FROM amir_secours WHERE utilisateur_id=?')->execute([$id]);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
