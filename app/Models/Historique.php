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

        $sql = "SELECT h.action, h.description,
                       DATE_FORMAT(h.date_action, '%Y-%m-%d %H:%i:%s') AS date_action
                FROM historique h{$where}
                ORDER BY h.date_action DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue(':' . ltrim($k, ':'), $v);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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

        $sql = "SELECT h.action, h.description,
                       DATE_FORMAT(h.date_action, '%Y-%m-%d %H:%i:%s') AS date_action,
                       u.id_user, u.nom, u.prenom, u.email, u.role" . $base . "
                ORDER BY h.date_action DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue(':' . ltrim($k, ':'), $v);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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
            $dayStart = $f['date'] . ' 00:00:00';
            $nextDay  = date('Y-m-d', strtotime($f['date'] . ' +1 day')) . ' 00:00:00';
            if ($f['date_mode'] === 'before') {
                $where .= " AND h.date_action < :date_before";
                $params['date_before'] = $dayStart;
            } elseif ($f['date_mode'] === 'after') {
                $where .= " AND h.date_action >= :date_after";
                $params['date_after'] = $nextDay;
            } else {
                $where .= " AND h.date_action >= :date_start AND h.date_action < :date_end";
                $params['date_start'] = $dayStart;
                $params['date_end']   = $nextDay;
            }
        }
    }

    public function getStats(): array
    {
        $total = (int)$this->db->query('SELECT COUNT(*) FROM historique')->fetchColumn();
        $today = (int)$this->db->query(
            "SELECT COUNT(*) FROM historique WHERE DATE(date_action) = CURDATE()"
        )->fetchColumn();
        $users = (int)$this->db->query(
            'SELECT COUNT(DISTINCT id_user) FROM historique'
        )->fetchColumn();
        return ['total' => $total, 'today' => $today, 'users' => $users];
    }
}
