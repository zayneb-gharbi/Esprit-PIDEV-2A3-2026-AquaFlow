<?php
class UserController
{
    private $model;
    public function __construct($model) { $this->model=$model; }
    public function index() {
        $user=$this->model->getDemoUser();
        include 'views/includes/header.php';
        include 'views/user/deactivate.php';
        include 'views/includes/footer.php';
    }
    public function deactivate() {
        if (isset($_POST['confirm'])) {
            $this->model->deactivate(1);
            $message='Compte désactivé (simulation de démonstration).';
        }
        $user=$this->model->getDemoUser();
        include 'views/includes/header.php';
        include 'views/user/deactivate.php';
        include 'views/includes/footer.php';
    }
}
?>
