<?php
// register.php : recoit le formulaire "Create account" et appelle addUtilisateur()

require_once 'crud_utilisateur.php';

if (isset($_POST['role'])) {

    if ($_POST['role'] == 'plumber') {
        $role   = 'P';
        $statut = 'en_attente';              // le plombier attend l'approbation de l'admin
        $zone   = $_POST['service_area'];
        $exp    = $_POST['experience'];
    } else {
        $role   = 'C';
        $statut = 'actif';
        $zone   = null;
        $exp    = null;
    }

    // id_user = null : genere par Oracle (sequence + trigger)
    $u = new Utilisateur(
        null,
        $_POST['last_name'],     // nom
        $_POST['first_name'],    // prenom
        $_POST['email'],
        $_POST['phone'],
        password_hash($_POST['password'], PASSWORD_DEFAULT),   // mot_de_passe
        $role,
        $zone,
        $exp,
        $statut
    );

    addUtilisateur($u);

} else {
    echo "Aucune donnee recue.";
}
?>
<br><br>
<a href="user.html">Retour</a>