#!/usr/bin/env bash
set -eu
cd "$(dirname "$0")"
php bin/installer.php --demo
cd ..
printf '%s\n' 'Ouvrir http://127.0.0.1:8000/amir/public/index.php'
php -S 127.0.0.1:8000 -t . amir/router.php
