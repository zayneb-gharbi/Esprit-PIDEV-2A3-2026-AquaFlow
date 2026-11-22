<?php
require_once 'session.php';
require_once 'crud_utilisateur.php';

// ---------- 1) Page reservee aux administrateurs ----------
if (empty($_SESSION['id_user'])) {
    header('Location: user.html');
    exit;
}

// on relit l'utilisateur dans la base (le role de la session ne suffit pas)
$admin = getUtilisateur($_SESSION['id_user']);

if (!$admin || $admin['role'] != 'A' || $admin['statut'] != 'actif') {
    header('Location: user.html');
    exit;
}

// ---------- 2) Token CSRF (workshop) ----------
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// ---------- 3) Traitement du bouton Approve / Reject ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token  = $_POST['csrf_token'] ?? '';
    $action = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id_user'] ?? 0);

    if (!hash_equals($csrfToken, $token)) {
        $_SESSION['flash'] = "Invalid request.";
    } elseif ($id <= 0) {
        $_SESSION['flash'] = "Invalid account.";
    } elseif ($action == 'approve') {
        if (approuverUtilisateur($id) == 1) {
            $_SESSION['flash'] = "Account approved.";
        } else {
            $_SESSION['flash'] = "Nothing changed (the account is no longer pending).";
        }
    } elseif ($action == 'reject') {
        if (refuserUtilisateur($id) == 1) {
            $_SESSION['flash'] = "Account rejected and deleted.";
        } else {
            $_SESSION['flash'] = "Nothing changed (the account is no longer pending).";
        }
    } else {
        $_SESSION['flash'] = "Unknown action.";
    }

    // on recharge la page : evite de renvoyer le formulaire avec F5
    header('Location: admin.php');
    exit;
}

// ---------- 4) Donnees a afficher ----------
$message = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$enAttente = getUtilisateursEnAttente();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Aqua Flow</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #F2F9FC; color: #18364A; margin: 0; }
        .card { max-width: 980px; margin: 40px auto; background: #fff; border: 1px solid #E0EAF0;
                border-radius: 14px; padding: 36px; }
        .top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        h1 { color: #073B5C; font-size: 26px; margin: 0; }
        .links a { color: #168FE5; font-weight: 700; font-size: 13px; text-decoration: none; margin-left: 18px; }
        .links a:hover { text-decoration: underline; }
        .sub { color: #8295A3; font-size: 13px; margin: 8px 0 22px; }
        .msg { background: #E5F8F0; color: #16734B; padding: 12px 14px; border-radius: 8px;
               font-size: 13px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 11px 8px; border-bottom: 1px solid #E0EAF0; font-size: 13px; }
        th { color: #8295A3; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; }
        .empty { color: #8295A3; padding: 24px 0; font-size: 14px; }
        .actions { display: flex; gap: 8px; }
        .actions form { margin: 0; }
        button { border: none; border-radius: 8px; padding: 9px 16px; font-weight: 700;
                 font-size: 12px; cursor: pointer; color: #fff; }
        .approve { background: #16A36B; }
        .approve:hover { background: #0F7D52; }
        .reject { background: #D64545; }
        .reject:hover { background: #A93232; }
    </style>
</head>
<body>
    <div class="card">

        <div class="top">
            <h1>Pending plumber accounts</h1>
            <div class="links">
                <a href="profile.php">My profile</a>
                <a href="logout.php">Log out</a>
            </div>
        </div>
        <p class="sub">
            Logged in as <?= htmlspecialchars($admin['prenom'], ENT_QUOTES, 'UTF-8') ?>
            <?= htmlspecialchars($admin['nom'], ENT_QUOTES, 'UTF-8') ?> (administrator)
        </p>

        <?php if ($message != '') { ?>
            <div class="msg"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php } ?>

        <?php if (empty($enAttente)) { ?>
            <div class="empty">No account is waiting for approval.</div>
        <?php } else { ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Service area</th>
                    <th>Experience</th>
                    <th>Registered</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($enAttente as $u) { ?>
                    <tr>
                        <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($u['telephone'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($u['zone_service'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($u['experience'] ?? '', ENT_QUOTES, 'UTF-8') ?> years</td>
                        <td><?= htmlspecialchars($u['date_creation'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="admin.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id_user" value="<?= (int) $u['id_user'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="approve">Approve</button>
                                </form>
                                <form method="POST" action="admin.php"
                                      onsubmit="return confirm('Reject and permanently delete this account?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="id_user" value="<?= (int) $u['id_user'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="reject">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>

    </div>
</body>
</html>