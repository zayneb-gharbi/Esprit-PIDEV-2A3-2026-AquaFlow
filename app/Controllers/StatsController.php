<?php
namespace App\Controllers;

require_once __DIR__ . '/AdminBaseController.php';

// StatsController.php : statistiques utilisateurs (administrateur)
class StatsController extends AdminBaseController
{
    protected string $page = 'stats';
    protected string $title = 'Statistiques | Aqua Flow';

    public function index(): void
    {
        $uc = new UtilisateurC();
        $this->requireAdmin($uc);

        $this->render('admin/stats', [
            'stats' => $uc->getStatistiques()
        ]);
    }
}
