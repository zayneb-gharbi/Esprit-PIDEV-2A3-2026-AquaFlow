<?php
class Historique
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function ajouter(int $idUtilisateur, string $action, ?string $description = null): bool
    {
        $sql = 'INSERT INTO historique (id_utilisateur, action, description) VALUES (:id, :action, :description)';
        return $this->pdo->prepare($sql)->execute([':id'=>$idUtilisateur, ':action'=>$action, ':description'=>$description]);
    }

    public function getHistoriquePersonnel(int $idUtilisateur, array $filters = [], int $limit = 10, int $offset = 0): array
    {
        $where = ' WHERE id_utilisateur = :id';
        $params = [':id'=>$idUtilisateur];
        if ($filters['q'] !== '') {
            $where .= ' AND (action LIKE :q1 OR description LIKE :q2)';
            $params[':q1'] = '%'.$filters['q'].'%'; $params[':q2'] = '%'.$filters['q'].'%';
        }
        if ($filters['action'] !== '') { $where .= ' AND action = :action'; $params[':action'] = $filters['action']; }
        $this->applyDateFilter($where, $params, 'date_action', $filters);
        $count = $this->pdo->prepare('SELECT COUNT(*) FROM historique'.$where);
        $count->execute($params);
        $total = (int)$count->fetchColumn();
        $sql = 'SELECT action, description, date_action FROM historique'.$where.' ORDER BY date_action DESC, id DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key=>$value) $stmt->bindValue($key, $value);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); $stmt->bindValue(':offset', $offset, PDO::PARAM_INT); $stmt->execute();
        return ['rows'=>$stmt->fetchAll(), 'total'=>$total];
    }

    public function getHistoriqueGlobal(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        $where = ' WHERE 1=1'; $params = [];
        if ($filters['q'] !== '') { $where .= ' AND (u.nom LIKE :q1 OR u.prenom LIKE :q2 OR u.email LIKE :q3 OR h.action LIKE :q4 OR h.description LIKE :q5)'; $term='%'.$filters['q'].'%'; $params=[':q1'=>$term,':q2'=>$term,':q3'=>$term,':q4'=>$term,':q5'=>$term]; }
        if ($filters['action'] !== '') { $where .= ' AND h.action = :action'; $params[':action'] = $filters['action']; }
        if ($filters['role'] !== '') { $where .= ' AND r.nom_role = :role'; $params[':role'] = $filters['role']; }
        $this->applyDateFilter($where, $params, 'h.date_action', $filters);
        $base = ' FROM historique h INNER JOIN utilisateur u ON u.id=h.id_utilisateur INNER JOIN role r ON r.id_role=u.id_role'.$where;
        $count = $this->pdo->prepare('SELECT COUNT(*)'.$base); $count->execute($params); $total = (int)$count->fetchColumn();
        $sql = 'SELECT h.id, h.action, h.description, h.date_action, u.id AS id_utilisateur, u.nom, u.prenom, u.email, r.nom_role'.$base.' ORDER BY h.date_action DESC, h.id DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key=>$value) $stmt->bindValue($key, $value);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); $stmt->bindValue(':offset', $offset, PDO::PARAM_INT); $stmt->execute();
        return ['rows'=>$stmt->fetchAll(), 'total'=>$total];
    }


    private function applyDateFilter(string &$where, array &$params, string $column, array $filters): void
    {
        if (empty($filters['date'])) return;
        $date = $filters['date'];
        $nextDay = (new DateTimeImmutable($date))->modify('+1 day')->format('Y-m-d');
        if (($filters['date_mode'] ?? 'during') === 'before') {
            $where .= " AND {$column} < :selected_date";
            $params[':selected_date'] = $date . ' 00:00:00';
        } elseif (($filters['date_mode'] ?? 'during') === 'after') {
            $where .= " AND {$column} >= :next_day";
            $params[':next_day'] = $nextDay . ' 00:00:00';
        } else {
            $where .= " AND {$column} >= :day_start AND {$column} < :day_end";
            $params[':day_start'] = $date . ' 00:00:00';
            $params[':day_end'] = $nextDay . ' 00:00:00';
        }
    }

    public function getActions(): array { return $this->pdo->query('SELECT DISTINCT action FROM historique ORDER BY action')->fetchAll(PDO::FETCH_COLUMN); }
    public function getStats(): array
    {
        return [
            'total'=>(int)$this->pdo->query('SELECT COUNT(*) FROM historique')->fetchColumn(),
            'today'=>(int)$this->pdo->query('SELECT COUNT(*) FROM historique WHERE DATE(date_action)=CURDATE()')->fetchColumn(),
            'users'=>(int)$this->pdo->query('SELECT COUNT(DISTINCT id_utilisateur) FROM historique')->fetchColumn()
        ];
    }
}
