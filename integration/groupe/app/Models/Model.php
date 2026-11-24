<?php
namespace App\Models;
class Model
{
    public static function getConnexion() { return \AmirDatabase::connexion(); }
}
