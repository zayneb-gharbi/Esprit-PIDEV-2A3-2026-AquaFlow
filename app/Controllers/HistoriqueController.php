<?php
namespace App\Controllers;

require_once __DIR__ . '/../Models/Historique.php';

use App\Models\Historique;

class HistoriqueController extends Controller
{
    private Historique $model;
    public function __construct() { $this->model = new Historique(); }

    private function getFilters(): array
    {
        $date = trim($_GET['date'] ?? '');
        if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = '';
        $mode = $_GET['date_mode'] ?? 'during';
        if (!in_array($mode, ['before', 'after', 'during'], true)) $mode = 'during';
        return [
            'q' => trim($_GET['q'] ?? ''),
            'action' => trim($_GET['action'] ?? ''),
            'role' => trim($_GET['role'] ?? ''),
            'date' => $date,
            'date_mode' => $mode
        ];
    }

    public function personnel(): void
    {
        requireLogin();
        $filters = $this->getFilters();
        $page = max(1, (int)($_GET['p'] ?? 1));
        $perPage = 10;
        $result = $this->model->getPersonnel((int)$_SESSION['id_user'], $filters, $perPage, ($page - 1) * $perPage);
        $historique = $result['rows'];
        $total = $result['total'];
        $pages = max(1, (int)ceil($total / $perPage));
        $actions = $this->model->getActions();
        $this->page = 'personal-history';
        $this->title = 'Mon historique | Aqua Flow';
        $this->render('history/personal', compact('filters', 'historique', 'total', 'page', 'pages', 'actions'));
    }

    public function global(): void
    {
        requireLogin();
        if (($_SESSION['role'] ?? '') !== 'A') redirect('profile');
        $filters = $this->getFilters();
        $page = max(1, (int)($_GET['p'] ?? 1));
        $perPage = 10;
        $result = $this->model->getGlobal($filters, $perPage, ($page - 1) * $perPage);
        $historique = $result['rows'];
        $total = $result['total'];
        $pages = max(1, (int)ceil($total / $perPage));
        $actions = $this->model->getActions();
        $stats = $this->model->getStats();
        $this->page = 'global-history';
        $this->title = 'Historique global | Aqua Flow';
        $this->render('history/global', compact('filters', 'historique', 'total', 'page', 'pages', 'actions', 'stats'));
    }
}
