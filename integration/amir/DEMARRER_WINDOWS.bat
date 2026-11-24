@echo off
setlocal
cd /d "%~dp0"
set "AMIR_PHP=C:\xampp\php\php.exe"
if not exist "%AMIR_PHP%" set "AMIR_PHP=php"
"%AMIR_PHP%" bin\installer.php --demo
if errorlevel 1 (
    echo Demarre MySQL dans XAMPP puis relance ce fichier.
    pause
    exit /b 1
)
if exist var\MES_COMPTES.txt start "" notepad var\MES_COMPTES.txt
start "" http://127.0.0.1:8000/amir/public/index.php
cd ..
"%AMIR_PHP%" -S 127.0.0.1:8000 -t . amir\router.php
pause
