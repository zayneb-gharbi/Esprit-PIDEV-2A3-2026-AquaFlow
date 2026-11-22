<?php
class AdminController {
    private $model;
    public function __construct($model) {
        $this->model = $model;
    }
    public function citizens() {
        $users = $this->model->getCitizens();
        include 'views/admin/citizens.php';
    }
    public function plumbers() {
        $users = $this->model->getPlumbers();
        include 'views/admin/plumbers.php';
    }
    public function stats() {
        $stats = $this->model->getStats();
        include 'views/admin/stats.php';
    }
}
?>
