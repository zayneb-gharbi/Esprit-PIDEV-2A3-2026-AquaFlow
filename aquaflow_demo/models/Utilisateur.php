<?php
class Utilisateur {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (!isset($_SESSION['demo_users'])) {
            $_SESSION['demo_users'] = [
                1 => ['id'=>1,'nom'=>'Mariem Trabelsi','email'=>'mariem@gmail.com','telephone'=>'22 123 456','adresse'=>'Tunis','id_role'=>1,'nom_role'=>'Citoyen','statut'=>'Actif'],
                2 => ['id'=>2,'nom'=>'Ahmed Ben Ali','email'=>'ahmed@gmail.com','telephone'=>'55 234 567','adresse'=>'Ariana','id_role'=>1,'nom_role'=>'Citoyen','statut'=>'Actif'],
                3 => ['id'=>3,'nom'=>'Sami Khelifi','email'=>'sami@gmail.com','telephone'=>'98 345 678','adresse'=>'Nabeul','id_role'=>1,'nom_role'=>'Citoyen','statut'=>'Inactif'],
                4 => ['id'=>4,'nom'=>'Ali Mansouri','email'=>'ali@gmail.com','telephone'=>'20 111 222','adresse'=>'Tunis','id_role'=>2,'nom_role'=>'Plombier','statut'=>'Actif'],
                5 => ['id'=>5,'nom'=>'Youssef','email'=>'youssef@gmail.com','telephone'=>'55 333 444','adresse'=>'Ariana','id_role'=>2,'nom_role'=>'Plombier','statut'=>'Actif'],
                6 => ['id'=>6,'nom'=>'Admin AquaFlow','email'=>'admin@aquaflow.tn','telephone'=>'71 000 000','adresse'=>'Tunis','id_role'=>3,'nom_role'=>'Administrateur','statut'=>'Actif']
            ];
        }
    }
    public function getById($id) { return isset($_SESSION['demo_users'][$id]) ? $_SESSION['demo_users'][$id] : null; }
    public function updateProfile($id,$nom,$email,$telephone,$adresse) {
        if (!isset($_SESSION['demo_users'][$id])) return false;
        $_SESSION['demo_users'][$id]['nom']=$nom; $_SESSION['demo_users'][$id]['email']=$email;
        $_SESSION['demo_users'][$id]['telephone']=$telephone; $_SESSION['demo_users'][$id]['adresse']=$adresse; return true;
    }
    public function deactivate($id) { if(isset($_SESSION['demo_users'][$id])) $_SESSION['demo_users'][$id]['statut']='Inactif'; return true; }
    public function getCitizens() { return array_values(array_filter($_SESSION['demo_users'],fn($u)=>$u['id_role']==1)); }
    public function getPlumbers() { return array_values(array_filter($_SESSION['demo_users'],fn($u)=>$u['id_role']==2)); }
    public function getStats() { $u=$_SESSION['demo_users']; return ['users'=>count($u),'citizens'=>count($this->getCitizens()),'plumbers'=>count($this->getPlumbers()),'active'=>count(array_filter($u,fn($x)=>$x['statut']=='Actif'))]; }
}
?>
