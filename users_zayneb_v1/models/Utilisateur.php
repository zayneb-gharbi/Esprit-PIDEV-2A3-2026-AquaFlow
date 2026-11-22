<?php
class Utilisateur
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getPlombiers(): array
    {
        $sql = "SELECT u.id, u.nom, u.prenom, u.email, u.telephone, u.statut, u.date_creation, r.nom_role
                FROM utilisateur u INNER JOIN role r ON r.id_role = u.id_role
                WHERE r.nom_role = 'plombier' ORDER BY u.nom, u.prenom";
        return $this->pdo->query($sql)->fetchAll();
    }
}
