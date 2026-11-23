<?php
namespace App\Controllers;

require_once __DIR__ . '/../Models/Model.php';
require_once __DIR__ . '/../Models/Utilisateur.php';

use App\Models\Model;
use PDOException;

// UtilisateurC.php : le CRUD (comme PersonneC du Chapitre 6)
class UtilisateurC
{
    // ---------- Create (INSERT INTO) ----------
    // id_user est NULL : le trigger Oracle genere le numero automatiquement
    // renvoie true si l'insertion a reussi, false sinon
    public function addUtilisateur($utilisateur)
    {
        $sql = "INSERT INTO utilisateur (id_user, nom, prenom, email, telephone, mot_de_passe, role, statut, zone_service, experience)
                VALUES (NULL, :nom, :prenom, :email, :telephone, :mot_de_passe, :role, :statut, :zone_service, :experience)";
        $db = Model::getConnexion();
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
            return $query->rowCount() == 1;
        } catch (PDOException $e) {
            error_log($e->getMessage());   // journal (Chapitre 6 : bonnes pratiques)
            return false;
        }
    }

    // ---------- Read (SELECT) : tous les utilisateurs ----------
    public function getUtilisateurs()
    {
        $sql = "SELECT * FROM utilisateur";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // ---------- Read (SELECT) : un seul utilisateur (fetch) ----------
    public function getUtilisateur($id)
    {
        $sql = "SELECT * FROM utilisateur WHERE id_user = :id";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // ---------- Read : un utilisateur par email OU telephone (pour le login) ----------
    public function getUtilisateurByIdentifiant($identifiant)
    {
        $sql = "SELECT * FROM utilisateur WHERE email = :email OR telephone = :telephone";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => $identifiant, 'telephone' => $identifiant]);
            return $query->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // ---------- Read : les comptes en attente d'approbation ----------
    public function getUtilisateursEnAttente()
    {
        $sql = "SELECT * FROM utilisateur WHERE statut = 'en_attente' ORDER BY id_user";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // ---------- Update (UPDATE) ----------
    public function updateUtilisateur($utilisateur, $id)
    {
        try {
            $db = Model::getConnexion();
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
            return $query->rowCount();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // ---------- Update : approuver un compte (en_attente -> actif) ----------
    public function approuverUtilisateur($id)
    {
        $sql = "UPDATE utilisateur SET statut = 'actif' WHERE id_user = :id AND statut = 'en_attente'";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // ---------- Delete (DELETE) ----------
    public function deleteUtilisateur($id)
    {
        try {
            $db = Model::getConnexion();
            $query = $db->prepare('DELETE FROM utilisateur WHERE id_user = :id');
            $query->execute(['id' => $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // ---------- Delete : refuser un compte (supprime la demande, seulement si en attente) ----------
    public function refuserUtilisateur($id)
    {
        $sql = "DELETE FROM utilisateur WHERE id_user = :id AND statut = 'en_attente'";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // ---------- Read : les utilisateurs d'un role (C = citoyen, P = plombier) ----------
    public function getUtilisateursByRole($role)
    {
        $sql = "SELECT * FROM utilisateur WHERE role = :role ORDER BY id_user";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['role' => $role]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // ---------- Read : statistiques des utilisateurs (citoyens + plombiers) ----------
    public function getStatistiques()
    {
        $sql = "SELECT COUNT(*) AS total,
                       COUNT(CASE WHEN role = 'C' THEN 1 END) AS citoyens,
                       COUNT(CASE WHEN role = 'P' THEN 1 END) AS plombiers,
                       COUNT(CASE WHEN statut = 'actif' THEN 1 END) AS actifs,
                       COUNT(CASE WHEN statut IN ('bloque', 'desactive') THEN 1 END) AS inactifs,
                       COUNT(CASE WHEN statut = 'bloque' THEN 1 END) AS bloques,
                       COUNT(CASE WHEN statut = 'desactive' THEN 1 END) AS desactives
                FROM utilisateur
                WHERE role IN ('C', 'P')";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return ['total' => 0, 'citoyens' => 0, 'plombiers' => 0, 'actifs' => 0, 'inactifs' => 0, 'bloques' => 0, 'desactives' => 0];
        }
    }

    // ---------- Update : desactiver un compte citoyen/plombier (actif -> bloque) ----------
    // Utilisé par l'administrateur : "bloque" = blocage admin (PR 2 / PR 3).
    public function desactiverUtilisateur($id)
    {
        $sql = "UPDATE utilisateur SET statut = 'bloque' WHERE id_user = :id AND role IN ('C', 'P') AND statut = 'actif'";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // ---------- Update : desactivation volontaire (actif -> desactive) ----------
    // Utilisé uniquement par le profil de l'utilisateur : distinct d'un blocage admin.
    public function desactiverMonCompte($id)
    {
        $sql = "UPDATE utilisateur SET statut = 'desactive' WHERE id_user = :id AND role IN ('C', 'P') AND statut = 'actif'";
        $db = Model::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
}
