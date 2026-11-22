<?php
class UserController {
    private $model;
    private $idUser;

    public function __construct($model, $idUser) {
        $this->model = $model;
        $this->idUser = $idUser;
    }

    public function profile() {
        $user = $this->model->getById($this->idUser);
        include 'views/user/profile.php';
    }

    public function edit() {
        $user = $this->model->getById($this->idUser);
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $email = trim($_POST['email']);
            $telephone = trim($_POST['telephone']);
            $adresse = trim($_POST['adresse']);
            if ($nom !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->model->updateProfile($this->idUser, $nom, $email, $telephone, $adresse);
                header('Location: index.php?page=profile&success=1');
                exit;
            } else {
                $message = 'Veuillez saisir un nom et un email valides.';
            }
        }
        include 'views/user/edit.php';
    }


    public function deactivate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->deactivate($this->idUser);
            session_destroy();
            header('Location: index.php?page=deactivated');
            exit;
        }
        include 'views/user/deactivate.php';
    }
}
?>
