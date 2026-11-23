<?php
namespace App\Models;

// Utilisateur.php : l'entite (Chapitre 5 : attributs private + constructeur + getters)
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
