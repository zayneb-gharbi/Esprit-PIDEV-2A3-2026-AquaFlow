AquaFlow - Module Gestion des utilisateurs (demo MVC)

Pages incluses :
- Desactiver mon compte (Utilisateur)
- Liste citoyens (Administrateur)
- Liste plombiers (Administrateur)
- Statistiques utilisateurs (Administrateur)

DEMO LOCALE
1. Placez le dossier aquaflow_final dans D:\xampp\htdocs (ou votre DocumentRoot Apache).
2. Placez le logo fourni dans assets/img/logo.png.
3. Ouvrez http://localhost/aquaflow_final/
Aucune base de donnees requise. Les tableaux du Model sont des donnees de demonstration.

INTEGRATION
- Module isole : index.php, models/UtilisateurModel.php, controllers/UserController.php, controllers/AdminController.php, views/ et assets.
- Ne pas remplacer les fichiers existants de votre equipe sans fusion.
- Le Model demo contient des tableaux temporaires. Pour la version connectee, remplacer getCitizens/getPlumbers/getStats/deactivate par les requetes PDO du CRUD de l'equipe.
- Les roles dans le code de votre coequipier : A=Administrateur, P=Plombier, C=Citoyen. Statut: actif/inactif/en_attente.
- La deactivation demo est temporaire et n'est pas persistante.

Syntaxe utilisee: classes simples, constructeur, private, fonctions, tableaux, foreach, array_filter, include/require_once, GET/POST, session non necessaire, HTML/CSS, JS DOM basique.
