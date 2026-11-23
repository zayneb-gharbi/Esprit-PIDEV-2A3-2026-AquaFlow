<?php
// Donnees de demonstration. Pour integration, remplacer ces tableaux par les fonctions PDO du projet.
class UtilisateurModel
{
    private $users = array(
        array('id_user'=>1, 'prenom'=>'Mariem', 'nom'=>'Jaouadi', 'email'=>'mariem@gmail.com', 'telephone'=>'22123456', 'role'=>'C', 'statut'=>'actif'),
        array('id_user'=>2, 'prenom'=>'Ali', 'nom'=>'Ben Salah', 'email'=>'ali@gmail.com', 'telephone'=>'55123456', 'role'=>'P', 'statut'=>'actif', 'zone_service'=>'Tunis', 'experience'=>5),
        array('id_user'=>3, 'prenom'=>'Ahmed', 'nom'=>'Trabelsi', 'email'=>'ahmed@gmail.com', 'telephone'=>'98123456', 'role'=>'C', 'statut'=>'actif'),
        array('id_user'=>4, 'prenom'=>'Youssef', 'nom'=>'Gharbi', 'email'=>'youssef@gmail.com', 'telephone'=>'54123456', 'role'=>'P', 'statut'=>'actif', 'zone_service'=>'Ariana', 'experience'=>3),
        array('id_user'=>5, 'prenom'=>'Sarra', 'nom'=>'Khelifi', 'email'=>'sarra@gmail.com', 'telephone'=>'20123456', 'role'=>'C', 'statut'=>'inactif')
    );
    public function getCitizens() { return array_values(array_filter($this->users, function($u) { return $u['role']=='C'; })); }
    public function getPlumbers() { return array_values(array_filter($this->users, function($u) { return $u['role']=='P'; })); }
    public function getStats() {
        return array('total'=>count($this->users), 'citizens'=>count($this->getCitizens()), 'plumbers'=>count($this->getPlumbers()),
            'active'=>count(array_filter($this->users, function($u) { return $u['statut']=='actif'; })),
            'inactive'=>count(array_filter($this->users, function($u) { return $u['statut']=='inactif'; })));
    }
    public function deactivate($id) {
        foreach ($this->users as $key=>$user) if ($user['id_user']==$id) $this->users[$key]['statut']='inactif';
    }
    public function getDemoUser() { return $this->users[0]; }
}
?>
