<?php
/**
 * Table de routage : ?page=... => [Controller, action].
 * Un seul controller par page, une seule vue par action : architecture MVC stricte.
 */
$routes = [
    // Authentification
    'signin'          => ['SigninController'],
    'signup'          => ['SignupController'],
    'logout'          => ['LogoutController'],

    // Accueil / tableau de bord
    'dashboard'       => ['DashboardController'],

    // Profil
    'profile'         => ['ProfileController'],
    'profile-edit'    => ['ProfileEditController'],

    // Administration des utilisateurs
    'admin'           => ['AdminController'],
    'stats'           => ['StatsController'],
    'citoyens'        => ['CitoyensController'],
    'plombiers'       => ['PlombiersController'],
    'export-users'    => ['ExportController'],

    // Historique
    'personal-history'=> ['HistoriqueController', 'personnel'],
    'global-history'  => ['HistoriqueController', 'global'],

    // Module Événements (intégré au login MVC)
    'events'                      => ['EventController', 'events'],
    'event-detail'                => ['EventController', 'detail'],
    'event-register'              => ['EventController', 'register'],
    'event-unregister'            => ['EventController', 'unregister'],
    'my-events'                   => ['EventController', 'myEvents'],
    'admin-events'                => ['EventController', 'adminEvents'],
    'admin-event-create'          => ['EventController', 'adminCreate'],
    'admin-event-edit'            => ['EventController', 'adminEdit'],
    'admin-event-detail'          => ['EventController', 'adminDetail'],
    'admin-event-cancel'          => ['EventController', 'adminCancel'],
    'admin-event-delete'          => ['EventController', 'adminDelete'],
    'admin-event-remove-participant' => ['EventController', 'adminRemoveParticipant'],
];
