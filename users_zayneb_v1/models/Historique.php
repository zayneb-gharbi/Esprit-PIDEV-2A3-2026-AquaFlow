<?php
class Historique
{
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function ajouter(int $idUtilisateur, string $action, ?string $description = null): bool
    {
        $sql = 'INSERT INTO historique (id_utilisateur, action, description) VALUES (:id, :action, :description)';
        return $this->pdo->prepare($sql)->execute([
            ':id' => $idUtilisateur, ':action' => $action, ':description' => $description
        ]);
    }

    public function getHistoriquePersonnel(int $idUtilisateur): array
    {
        $sql = 'SELECT action, description, date_action FROM historique WHERE id_utilisateur = :id ORDER BY date_action DESC, id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $idUtilisateur]);
        return $stmt->fetchAll();
    }

    public function getHistoriqueGlobal(array $filters = []): array
    {
        $sql = 'SELECT h.id, h.action, h.description, h.date_action, u.id AS id_utilisateur,
                       u.nom, u.prenom, u.email, r.nom_role
                FROM historique h
                INNER JOIN utilisateur u ON u.id = h.id_utilisateur
                INNER JOIN role r ON r.id_role = u.id_role
                WHERE 1=1';
        $params = [];
        if (!empty($filters['q'])) {
            $sql .= ' AND (u.nom LIKE :q OR u.prenom LIKE :q OR u.email LIKE :q OR h.action LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['action'])) {
            $sql .= ' AND h.action = :action';
            $params[':action'] = $filters['action'];
        }
        if (!empty($filters['date'])) {
            $sql .= ' AND DATE(h.date_action) = :date';
            $params[':date'] = $filters['date'];
        }
        $sql .= ' ORDER BY h.date_action DESC, h.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getActions(): array
    {
        return $this->pdo->query('SELECT DISTINCT action FROM historique ORDER BY action')->fetchAll(PDO::FETCH_COLUMN);
    }
}
