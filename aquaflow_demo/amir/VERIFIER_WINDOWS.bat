@echo off
setlocal
cd /d "%~dp0"
set "AMIR_PHP=C:\xampp\php\php.exe"
if not exist "%AMIR_PHP%" set "AMIR_PHP=php"
"%AMIR_PHP%" bin\verifier_installation.php
pause
