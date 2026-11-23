<?php
class AdminController
{
    private $model;
    public function __construct($model) { $this->model=$model; }
    public function index($page) {
        if ($page=='citizens') $users=$this->model->getCitizens();
        if ($page=='plumbers') $users=$this->model->getPlumbers();
        if ($page=='stats') $stats=$this->model->getStats();
        include 'views/includes/header.php';
        if ($page=='stats') include 'views/admin/stats.php';
        else include 'views/admin/list.php';
        include 'views/includes/footer.php';
    }
}
?>
