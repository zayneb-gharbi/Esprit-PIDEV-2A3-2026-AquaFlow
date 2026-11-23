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
}
