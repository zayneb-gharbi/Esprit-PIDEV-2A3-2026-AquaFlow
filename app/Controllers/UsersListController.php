<?php
namespace App\Controllers;

require_once __DIR__ . '/AdminBaseController.php';

// UsersListController.php : liste des infos personnelles d'un type d'utilisateur (administrateur)
abstract class UsersListController extends AdminBaseController
{
    protected string $role = 'C';          // C = citoyen, P = plombier
    protected string $heading = '';

    public function index(): void
    {
        $uc = new UtilisateurC();
        $this->requireAdmin($uc);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleBlockToggle($uc, $this->page);   // message flash puis redirection
        }

        $this->render('users/list', [
            'users'   => $uc->getUtilisateursByRole($this->role),
            'role'    => $this->role,
            'heading' => $this->heading,
            'page'    => $this->page,
            'flash'   => takeFlash()
        ], 'users/list_scripts');
    }
}
