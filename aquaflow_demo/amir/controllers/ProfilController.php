<?php
class AmirProfil extends UserController
{
    private $utilisateurs;
    private $id;

    public function __construct($model, $id)
    {
        parent::__construct($model, $id);
        $this->utilisateurs = $model;
        $this->id = $id;
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->utilisateurs->updateProfile($this->id, champ($_POST, 'nom', 150), champ($_POST, 'email', 190),
                champ($_POST, 'telephone', 40), champ($_POST, 'adresse', 255));
            $user = $this->utilisateurs->getById($this->id);
            $_SESSION['auth']['version'] = (int) $user['version'];
            unset($_SESSION['setup_totp'], $_SESSION['oauth']);
            rediriger('profile');
        }
        parent::edit();
    }

    public function deactivate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->utilisateurs->deactivate($this->id);
            AmirAuth::effacer();
            rediriger('deactivated');
        }
        parent::deactivate();
    }
}
