# AquaFlow — Gestion des événements (Templates MVC)

Projet de templates HTML/PHP statiques pour le module **Gestion des événements** d'AquaFlow, réalisé selon le design system (charte graphique) et l'architecture MVC demandée.

## Aperçu
- **Focus :** Module 5 — Gestion des événements (Back Office et Front Office)
- **Pages communes :** accueil citoyen et tableau de bord administrateur
- **Contenu :** templates statiques (données exemples), aucune logique backend/base de données
- **Design :** tokens, composants et mise en page conformes à `NOVA_DESIGN_SYSTEM.md`

## Structure MVC
```text
AquaFlow/
├── Controllers/
│   ├── EvenementController.php
│   ├── HomeController.php
│   └── ParticipationController.php
├── Models/
│   ├── Evenement.php
│   ├── Participation.php
│   └── Utilisateur.php
└── Views/
    ├── assets/
    │   └── css/style.css
    ├── includes/                # Partials Front Office (citoyen)
    │   ├── head.php
    │   ├── sidebar.php
    │   ├── topbar.php
    │   └── scripts.php
    ├── partials/                # Partials Back Office (admin)
    │   ├── head.php
    │   ├── sidebar.php
    │   ├── footer.php
    │   └── scripts.php
    ├── layout.php
    └── pages/
        ├── front/
        │   ├── index.php                # Accueil citoyen
        │   ├── evenements.php           # Catalogue des événements
        │   ├── evenement-details.php    # Détail + inscription
        │   └── mes-participations.php   # Mes participations
        └── admin/
            ├── index.php                # Tableau de bord
            ├── evenements.php           # Liste + KPIs + modals
            ├── evenement-form.php       # Création / modification
            └── evenement-details.php    # Détails + participants (JOIN)
```

## Pages réalisées
### Front Office (Citoyen)
- `Views/pages/front/index.php` — accueil avec raccourci vers les événements
- `Views/pages/front/evenements.php` — catalogue, filtres/tabs, états (inscrit, complet, annulé)
- `Views/pages/front/evenement-details.php` — détail, jauge de places, inscription/désinscription, messages
- `Views/pages/front/mes-participations.php` — liste des participations, état vide et modal de désinscription

### Back Office (Administrateur)
- `Views/pages/admin/index.php` — tableau de bord avec accès rapide à la gestion des événements
- `Views/pages/admin/evenements.php` — KPIs, filtres, tableau des événements, modals supprimer/annuler
- `Views/pages/admin/evenement-form.php` — formulaire création/modification (validation et règle de capacité)
- `Views/pages/admin/evenement-details.php` — détail + participants inscrits (JOIN Événement → Participation → Utilisateur), présence et total

## Données exemples (Module 5)
Les pages utilisent les mêmes données cohérentes (E01–E06) : Journée de l'eau, Nettoyage des plages, Atelier économie d'eau (complet), Sensibilisation dans les écoles (terminé), Randonnée des sources (annulé), Atelier détection de fuites (en cours). Participants affichés sur la page admin détail : Ahmed, Mariem, Sami, Salma, Nour.

