@echo off
setlocal
cd /d "%~dp0"
if not exist config\local.php copy config\local.example.php config\local.php >nul
echo Ce fichier reste sur ton PC et est exclu de Git.
echo Google : google_id et google_secret.
echo Facebook : facebook_id et facebook_secret.
echo Conserve les apostrophes et les virgules. Enregistre avec Ctrl+S.
start /wait "" notepad.exe config\local.php
