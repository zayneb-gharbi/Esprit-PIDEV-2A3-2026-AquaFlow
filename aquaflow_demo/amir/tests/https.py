import http.client
import json
import os
from pathlib import Path
import shutil
import socket
import ssl
import subprocess
import sys
import tempfile
import time

caddy, php, php_cgi = map(str, map(Path.resolve, map(Path, sys.argv[1:4])))
amir = Path(__file__).resolve().parents[1]
checks = []


def verify(condition, label):
    if not condition:
        raise AssertionError(label)
    checks.append(label)


with tempfile.TemporaryDirectory(prefix="aquaflow https ") as directory:
    temporary = Path(directory)
    site = temporary / "site"
    data = temporary / "data"
    for folder in ["amir/public", "amir/config", "amir/controllers", "amir/models", "amir/var", "assets"]:
        (site / folder).mkdir(parents=True, exist_ok=True)
    for name in ["config/application.php", "config/database.php", "controllers/AuthController.php", "controllers/SocialController.php", "models/SecuriteUtilisateur.php"]:
        shutil.copyfile(amir / name, site / "amir" / name)
    local = site / "amir/config/local.php"
    original = "<?php return ['base_url'=>'http://127.0.0.1:8000/amir/public','google_id'=>'test-id','google_secret'=>'test-google-secret','facebook_id'=>'test-fb-id','facebook_secret'=>'test-facebook-secret','db_password'=>'test-db-password','custom'=>['port'=>123,'enabled'=>true]];"
    local.write_text(original)
    helper = str(amir / "bin/https_configuration.php").replace("'", "\\'")
    command = f"require '{helper}'; amirConfigurerHttps($argv[1], $argv[2]);"
    for _ in range(2):
        subprocess.run([php, "-n", "-r", command, str(local), str(site / "amir/var")], check=True)
    saved = json.loads(subprocess.check_output([php, "-n", "-r", "echo json_encode(require $argv[1]);", str(local)]))
    verify(saved.pop("base_url") == "https://127.0.0.1:8443/amir/public", "Adresse HTTPS enregistree")
    verify(saved == {"google_id": "test-id", "google_secret": "test-google-secret", "facebook_id": "test-fb-id", "facebook_secret": "test-facebook-secret", "db_password": "test-db-password", "custom": {"port": 123, "enabled": True}}, "Tous les autres reglages conserves")
    backups = list((site / "amir/var").glob("local-avant-https-*.php"))
    verify(len(backups) == 1 and backups[0].read_text() == original, "Sauvegarde exacte et relancement sans remplacement")
    (site / "amir/var/cle.txt").write_text("private-test-key")
    for asset in ["assets/style.css", "assets/script.js", "amir/public/security.css"]:
        (site / asset).write_text("test-asset")
    (site / "amir/public/index.php").write_text("""<?php
require __DIR__.'/../controllers/AuthController.php';
require __DIR__.'/../controllers/SocialController.php';
AmirAuth::demarrer();
header('Content-Type: application/json');
echo json_encode(['https'=>$_SERVER['HTTPS']??null,'script'=>$_SERVER['SCRIPT_NAME'],'query'=>$_GET,'method'=>$_SERVER['REQUEST_METHOD'],'form'=>$_POST,'google'=>AmirSocial::callback('google'),'facebook'=>AmirSocial::callback('facebook')]);
""")
    environment = os.environ | {"AMIR_SITE_ROOT": str(site), "AMIR_HTTPS_STORAGE": str(data)}
    config = ["--config", str(amir / "config/Caddyfile"), "--adapter", "caddyfile"]
    servers = []
    with (temporary / "server.log").open("w+") as log:
        try:
            subprocess.run([caddy, "validate", *config], env=environment, stdout=log, stderr=log, check=True)
            servers.append(subprocess.Popen([php_cgi, "-n", "-d", "cgi.fix_pathinfo=0", "-d", f"session.save_path={temporary}", "-b", "127.0.0.1:9001"], stdout=log, stderr=log))
            servers.append(subprocess.Popen([caddy, "run", *config], env=environment, stdout=log, stderr=log))
            for port in [9001, 8443]:
                for attempt in range(100):
                    if any(server.poll() is not None for server in servers):
                        raise RuntimeError("Un serveur s'est arrete")
                    try:
                        with socket.create_connection(("127.0.0.1", port), timeout=0.1):
                            break
                    except OSError:
                        time.sleep(0.1)
                else:
                    raise RuntimeError("Delai de demarrage depasse")
            context = ssl.create_default_context(cafile=str(data / "pki/authorities/local/root.crt"))

            def request(path, method="GET", body=None):
                connection = http.client.HTTPSConnection("127.0.0.1", 8443, context=context, timeout=5)
                try:
                    connection.request(method, path, body=body, headers={"Content-Type": "application/x-www-form-urlencoded"})
                    response = connection.getresponse()
                    return response.status, dict(response.getheaders()), response.read()
                finally:
                    connection.close()

            time.sleep(0.3)
            status, headers, body = request("/amir/public/index.php?page=login")
            value = json.loads(body)
            verify(status == 200 and value["https"] == "on", "Connexion TLS verifiee et HTTPS transmis a PHP")
            verify(value["script"] == "/amir/public/index.php" and value["query"] == {"page": "login"}, "Point d'entree et parametres transmis")
            cookie = headers.get("Set-Cookie", "").lower()
            verify("secure" in cookie and "httponly" in cookie and "samesite=lax" in cookie, "Cookie de session protege")
            for provider in ["google", "facebook"]:
                expected = "https://127.0.0.1:8443/amir/public/index.php?page=oauth_callback&provider=" + provider
                verify(value[provider] == expected, "Retour OAuth HTTPS " + provider)
                status, _, body = request(expected.split(":8443", 1)[1] + "&code=fake-code&state=fake-state")
                received = json.loads(body)["query"]
                verify(status == 200 and received["provider"] == provider and received["state"] == "fake-state", "Parametres du retour " + provider + " conserves")
            status, _, body = request("/amir/public/index.php?page=login", "POST", "email=test%40example.test&csrf=test-token")
            verify(status == 200 and json.loads(body)["form"]["email"] == "test@example.test", "Formulaires POST transmis")
            for path in ["/", "/amir/public", "/amir/public/"]:
                status, headers, _ = request(path)
                verify(status == 302 and headers.get("Location") == "/amir/public/index.php", "Redirection " + path)
            for asset in ["/assets/style.css", "/assets/script.js", "/amir/public/security.css"]:
                status, _, body = request(asset)
                verify(status == 200 and body == b"test-asset", "Ressource " + asset)
            for path in ["/amir/config/local.php", "/amir/var/cle.txt", "/amir/bin/demarrer_https.php", "/index.php", "/amir/public/index.php/extra", "/amir/public/../config/local.php", "/amir/config/Caddyfile", "/assets/../amir/var/cle.txt"]:
                status, _, body = request(path)
                verify(status == 404 and b"test-key" not in body and b"test-secret" not in body, "Fichier prive refuse " + path)
        except Exception:
            log.flush()
            log.seek(0)
            print(log.read(), file=sys.stderr)
            raise
        finally:
            for server in reversed(servers):
                server.terminate()
                try:
                    server.wait(timeout=5)
                except subprocess.TimeoutExpired:
                    server.kill()
                    server.wait()
print(json.dumps({"checks": len(checks), "status": "OK", "details": checks}, ensure_ascii=False, indent=2))
