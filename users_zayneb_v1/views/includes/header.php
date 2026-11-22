<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AquaFlow — Activité utilisateurs</title><link rel="stylesheet" href="assets/style.css">
</head><body>
<header class="topbar"><a class="brand" href="index.php"><span class="drop">◆</span> AquaFlow</a>
<nav>
<?php if (!empty($_SESSION['user'])): ?>
<a href="index.php?page=personal-history">Mon historique</a>
<?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
<a href="index.php?page=global-history">Historique global</a><a href="index.php?page=plumbers">Liste des plombiers</a>
<?php endif; ?>
<a href="index.php?page=demo-login">Changer de compte</a>
<?php endif; ?>
</nav></header><main class="container">
