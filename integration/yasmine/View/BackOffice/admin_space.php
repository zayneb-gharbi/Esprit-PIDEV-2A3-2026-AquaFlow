<?php
/** View/BackOffice/admin_space.php — Attend : $user (UserModel) */
ob_start();
?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        Connecté en tant qu'administrateur : <strong><?= e($user->getUsername()) ?></strong>.
    </div>
</div>
<?php
$content = ob_get_clean();
$title = 'Dashboard';
$activePage = 'dashboard';
require __DIR__ . '/layout_admin.php';
