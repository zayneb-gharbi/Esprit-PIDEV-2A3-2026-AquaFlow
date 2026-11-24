<?php
namespace App\Controllers;

require_once __DIR__ . '/UsersListController.php';

// CitoyensController.php : liste des citoyens
class CitoyensController extends UsersListController
{
    protected string $page = 'citoyens';
    protected string $title = 'Citoyens | Aqua Flow';
    protected string $role = 'C';
    protected string $heading = 'Liste des citoyens';
}
