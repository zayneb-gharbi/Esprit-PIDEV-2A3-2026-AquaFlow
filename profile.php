<?php
require_once 'session.php';
require_once 'crud_utilisateur.php';

// page protegee : il faut etre connecte
if (empty($_SESSION['id_user'])) {
    header('Location: user.html');
    exit;
}

// on relit l'utilisateur dans la base a chaque visite
$user = getUtilisateur($_SESSION['id_user']);

// compte supprime, bloque ou plus actif entre-temps : deconnexion
if (!$user || $user['statut'] != 'actif') {
    header('Location: logout.php');
    exit;
}

if ($user['role'] == 'A') {
    $roleLabel = 'Administrator';
} elseif ($user['role'] == 'P') {
    $roleLabel = 'Plumber';
} else {
    $roleLabel = 'Civilian';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My profile | Aqua Flow</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #F2F9FC; color: #18364A; margin: 0; }
        .card { max-width: 560px; margin: 50px auto; background: #fff; border: 1px solid #E0EAF0;
                border-radius: 14px; padding: 36px; }
        h1 { color: #073B5C; font-size: 26px; margin-top: 0; }
        .badge { display: inline-block; background: #EAF6FC; color: #168FE5; font-weight: 700;
                 font-size: 12px; padding: 4px 12px; border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 22px; }
        th, td { text-align: left; padding: 11px 8px; border-bottom: 1px solid #E0EAF0; font-size: 14px; }
        th { color: #8295A3; width: 40%; }
        .logout { display: inline-block; margin-top: 26px; background: #168FE5; color: #fff;
                  text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 13px; }
        .logout:hover { background: #073B5C; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Welcome, <?= htmlspecialchars($user['prenom'], ENT_QUOTES, 'UTF-8') ?></h1>
        <span class="badge"><?= $roleLabel ?></span>

        <table>
            <tr><th>First name</th><td><?= htmlspecialchars($user['prenom'], ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Last name</th><td><?= htmlspecialchars($user['nom'], ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($user['telephone'], ENT_QUOTES, 'UTF-8') ?></td></tr>
            <?php if ($user['role'] == 'P') { ?>
                <tr><th>Service area</th><td><?= htmlspecialchars($user['zone_service'], ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Experience</th><td><?= htmlspecialchars($user['experience'], ENT_QUOTES, 'UTF-8') ?> years</td></tr>
            <?php } ?>
            <tr><th>Member since</th><td><?= htmlspecialchars($user['date_creation'], ENT_QUOTES, 'UTF-8') ?></td></tr>
        </table>

        <a class="logout" href="logout.php">Log out</a>
    </div>
</body>
</html>