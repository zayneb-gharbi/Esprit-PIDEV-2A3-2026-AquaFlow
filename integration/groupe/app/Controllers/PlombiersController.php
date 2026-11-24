<?php
namespace App\Controllers;

require_once __DIR__ . '/UsersListController.php';

// PlombiersController.php : liste des plombiers
class PlombiersController extends UsersListController
{
    protected string $page = 'plombiers';
    protected string $title = 'Plombiers | Aqua Flow';
    protected string $role = 'P';
    protected string $heading = 'Liste des plombiers';
}
