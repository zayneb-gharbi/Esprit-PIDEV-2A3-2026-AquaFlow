<?php
class HistoriqueController
{
    private Historique $model;
    public function __construct(Historique $model) { $this->model=$model; }
    private function filters(): array
    {
        $date = trim($_GET['date'] ?? '');
        if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = '';
        $dateMode = $_GET['date_mode'] ?? 'during';
        if (!in_array($dateMode, ['before', 'after', 'during'], true)) $dateMode = 'during';
        return [
            'q' => trim($_GET['q'] ?? ''),
            'action' => trim($_GET['action'] ?? ''),
            'role' => trim($_GET['role'] ?? ''),
            'date' => $date,
            'date_mode' => $dateMode
        ];
    }
    public function personnel(): void
    {
        if (empty($_SESSION['user'])) { header('Location: index.php?page=demo-login'); exit; }
        $filters=$this->filters(); $page=max(1,(int)($_GET['p']??1)); $perPage=10;
        $result=$this->model->getHistoriquePersonnel((int)$_SESSION['user']['id'],$filters,$perPage,($page-1)*$perPage);
        $historique=$result['rows']; $total=$result['total']; $pages=max(1,(int)ceil($total/$perPage)); $actions=$this->model->getActions();
        require __DIR__.'/../views/historique/personnel.php';
    }
    public function global(): void
    {
        require_admin(); $filters=$this->filters(); $page=max(1,(int)($_GET['p']??1)); $perPage=10;
        $result=$this->model->getHistoriqueGlobal($filters,$perPage,($page-1)*$perPage);
        $historique=$result['rows']; $total=$result['total']; $pages=max(1,(int)ceil($total/$perPage));
        $actions=$this->model->getActions(); $stats=$this->model->getStats();
        require __DIR__.'/../views/historique/global.php';
    }
}
