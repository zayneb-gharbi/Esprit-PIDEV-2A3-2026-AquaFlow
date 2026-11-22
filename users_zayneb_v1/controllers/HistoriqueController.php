<?php
class HistoriqueController
{
    private Historique $model;
    public function __construct(Historique $model) { $this->model = $model; }

    public function personnel(): void
    {
        if (empty($_SESSION['user'])) { header('Location: index.php?page=demo-login'); exit; }
        $historique = $this->model->getHistoriquePersonnel((int)$_SESSION['user']['id']);
        require __DIR__ . '/../views/historique/personnel.php';
    }

    public function global(): void
    {
        require_admin();
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'action' => trim($_GET['action'] ?? ''),
            'date' => trim($_GET['date'] ?? '')
        ];
        $historique = $this->model->getHistoriqueGlobal($filters);
        $actions = $this->model->getActions();
        require __DIR__ . '/../views/historique/global.php';
    }
}
