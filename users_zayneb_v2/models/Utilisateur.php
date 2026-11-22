<?php
class Utilisateur
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    public function getPlombiers(array $filters = []): array
    {
        $sql = "SELECT u.id,u.nom,u.prenom,u.email,u.telephone,u.statut,u.date_creation,r.nom_role FROM utilisateur u INNER JOIN role r ON r.id_role=u.id_role WHERE r.nom_role='plombier'";
        $params=[];
        if (($filters['q'] ?? '') !== '') { $sql .= ' AND (u.nom LIKE :q OR u.prenom LIKE :q OR u.email LIKE :q OR u.telephone LIKE :q)'; $params[':q']='%'.$filters['q'].'%'; }
        if (($filters['statut'] ?? '') !== '') { $sql .= ' AND u.statut=:statut'; $params[':statut']=$filters['statut']; }
        $sql .= ' ORDER BY u.nom,u.prenom';
        $stmt=$this->pdo->prepare($sql); $stmt->execute($params); return $stmt->fetchAll();
    }
    public function getUtilisateursParRole(string $role, array $filters = []): array
    {
        if (!in_array($role, ['plombier', 'citoyen'], true)) {
            throw new InvalidArgumentException('Rôle non autorisé pour l’export.');
        }
        $sql = "SELECT u.id,u.nom,u.prenom,u.email,u.telephone,u.statut,u.date_creation,r.nom_role FROM utilisateur u INNER JOIN role r ON r.id_role=u.id_role WHERE r.nom_role=:role";
        $params = [':role' => $role];
        if (($filters['q'] ?? '') !== '') {
            $sql .= ' AND (u.nom LIKE :q1 OR u.prenom LIKE :q2 OR u.email LIKE :q3 OR u.telephone LIKE :q4)';
            $term = '%' . $filters['q'] . '%';
            $params += [':q1'=>$term, ':q2'=>$term, ':q3'=>$term, ':q4'=>$term];
        }
        if (($filters['statut'] ?? '') !== '') {
            $sql .= ' AND u.statut=:statut';
            $params[':statut'] = $filters['statut'];
        }
        $sql .= ' ORDER BY u.nom,u.prenom';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getStatutsPlombiers(): array
    { return $this->pdo->query("SELECT DISTINCT u.statut FROM utilisateur u INNER JOIN role r ON r.id_role=u.id_role WHERE r.nom_role='plombier' ORDER BY u.statut")->fetchAll(PDO::FETCH_COLUMN); }
}
