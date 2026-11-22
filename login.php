<?php
require_once 'session.php';
require_once 'crud_utilisateur.php';

$message = '';

if (isset($_POST['identifiant']) && isset($_POST['password'])) {

    $identifiant = trim($_POST['identifiant']);
    $password    = $_POST['password'];

    if ($identifiant === '' || $password === '') {
        $message = "Please enter your email or phone and your password.";
    } else {
        $user = getUtilisateurByIdentifiant($identifiant);

        // message generique : on ne revele jamais si c'est l'identifiant ou le mot de passe
        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            $message = "Incorrect email/phone or password.";
        } elseif ($user['statut'] == 'en_attente') {
            $message = "Your account is waiting for administrator approval.";
        } elseif ($user['statut'] == 'bloque') {
            $message = "Your account is blocked.";
        } else {
            // tout est bon : on ouvre la session
            session_regenerate_id(true);
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['prenom']  = $user['prenom'];
            $_SESSION['role']    = $user['role'];

            // pour l'instant tout le monde va sur profile.php
            // (plus tard : si role == 'A' -> admin.php)
            header('Location: profile.php');
            exit;
        }
    }
} else {
    $message = "No data received.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="user.css">
</head>
<body style="padding:30px;">
    <div class="message error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <br>
    <a href="user.html">Retour</a>
</body>
</html>