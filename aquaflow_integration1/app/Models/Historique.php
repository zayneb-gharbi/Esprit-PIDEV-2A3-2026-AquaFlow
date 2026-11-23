<?php
namespace App\Models;

use PDO;
use PDOException;

require_once __DIR__ . '/Model.php';

class Historique
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Model::getConnexion();
    }

    public function ajouter(int $idUser, string $action, ?string $description = null): bool
    {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO historique (id_user, action, description, date_action)
                 VALUES (:id_user, :action, :description, CURRENT_TIMESTAMP)'
            );
            return $stmt->execute([
                'id_user' => $idUser,
                'action' => $action,
                'description' => $description
            ]);
        } catch (PDOException $e) {
            error_log('Historique: ' . $e->getMessage());
            return false;
        }
    }

    public function getActions(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT action FROM historique ORDER BY action');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPersonnel(int $idUser, array $f, int $limit, int $offset): array
    {
        $where = ' WHERE h.id_user = :id_user';
        $params = ['id_user' => $idUser];
        $this->filters($where, $params, $f, 'h.');
        $count = $this->db->prepare('SELECT COUNT(*) FROM historique h' . $where);
        $count->execute($params);
        $total = (int)$count->fetchColumn();

        $sql = "SELECT action, description, date_action FROM (
                    SELECT ordered_rows.*, ROWNUM AS rnum FROM (
                        SELECT h.action, h.description, TO_CHAR(h.date_action, 'YYYY-MM-DD HH24:MI:SS') AS date_action
                        FROM historique h{$where}
                        ORDER BY h.date_action DESC
                    ) ordered_rows WHERE ROWNUM <= :upper_bound
                ) WHERE rnum > :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue(':' . ltrim($k, ':'), $v);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':upper_bound', $offset + $limit, PDO::PARAM_INT);
        $stmt->execute();
        return ['rows' => $stmt->fetchAll(), 'total' => $total];
    }

    public function getGlobal(array $f, int $limit, int $offset): array
    {
        $where = ' WHERE 1=1';
        $params = [];
        $fNoSearch = $f; $fNoSearch['q'] = '';
        $this->filters($where, $params, $fNoSearch, 'h.');
        if ($f['role'] !== '') {
            $where .= ' AND u.role = :role';
            $params['role'] = $f['role'];
        }
        if ($f['q'] !== '') {
            $where .= ' AND (u.nom LIKE :q1 OR u.prenom LIKE :q2 OR u.email LIKE :q3 OR h.action LIKE :q4 OR h.description LIKE :q5)';
            $term = '%' . $f['q'] . '%';
            foreach (['q1','q2','q3','q4','q5'] as $key) $params[$key] = $term;
        }

        $base = ' FROM historique h INNER JOIN utilisateur u ON u.id_user = h.id_user' . $where;
        $count = $this->db->prepare('SELECT COUNT(*)' . $base);
        $count->execute($params);
        $total = (int)$count->fetchColumn();

        $sql = "SELECT action, description, date_action, id_user, nom, prenom, email, role FROM (
                    SELECT ordered_rows.*, ROWNUM AS rnum FROM (
                        SELECT h.action, h.description, TO_CHAR(h.date_action, 'YYYY-MM-DD HH24:MI:SS') AS date_action,
                               u.id_user, u.nom, u.prenom, u.email, u.role" . $base . "
                        ORDER BY h.date_action DESC
                    ) ordered_rows WHERE ROWNUM <= :upper_bound
                ) WHERE rnum > :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue(':' . ltrim($k, ':'), $v);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':upper_bound', $offset + $limit, PDO::PARAM_INT);
        $stmt->execute();
        return ['rows' => $stmt->fetchAll(), 'total' => $total];
    }

    private function filters(string &$where, array &$params, array $f, string $prefix): void
    {
        if ($f['q'] !== '' && $prefix === 'h.') {
            // Global search is handled in getGlobal; personal search is limited to activity text.
            if (!isset($params['q1'])) {
                $where .= ' AND (h.action LIKE :q1 OR h.description LIKE :q2)';
                $term = '%' . $f['q'] . '%';
                $params['q1'] = $term;
                $params['q2'] = $term;
            }
        }
        if ($f['action'] !== '') {
            $where .= ' AND h.action = :action';
            $params['action'] = $f['action'];
        }
        if ($f['date'] !== '') {
            if ($f['date_mode'] === 'before') {
                $where .= " AND h.date_action < TO_TIMESTAMP(:date_before, 'YYYY-MM-DD')";
                $params['date_before'] = $f['date'];
            } elseif ($f['date_mode'] === 'after') {
                $where .= " AND h.date_action >= TO_TIMESTAMP(:date_after, 'YYYY-MM-DD') + INTERVAL '1' DAY";
                $params['date_after'] = $f['date'];
            } else {
                $where .= " AND h.date_action >= TO_TIMESTAMP(:date_start, 'YYYY-MM-DD')
                            AND h.date_action < TO_TIMESTAMP(:date_end, 'YYYY-MM-DD') + INTERVAL '1' DAY";
                $params['date_start'] = $f['date'];
                $params['date_end'] = $f['date'];
            }
        }
    }

    public function getStats(): array
    {
        $total = (int)$this->db->query('SELECT COUNT(*) FROM historique')->fetchColumn();
        $today = (int)$this->db->query(
            "SELECT COUNT(*) FROM historique WHERE TRUNC(date_action) = TRUNC(SYSDATE)"
        )->fetchColumn();
        $users = (int)$this->db->query(
            'SELECT COUNT(DISTINCT id_user) FROM historique'
        )->fetchColumn();
        return ['total' => $total, 'today' => $today, 'users' => $users];
    }
}
