<?php
namespace App\Controllers;

// Controller.php : controller de base (workshop). render() assemble la page dans layout.php
abstract class Controller
{
    /** Cle de la page courante (lien actif du menu) */
    protected string $page = 'signin';

    /** Titre affiche dans <title> */
    protected string $title = 'Aqua Flow';

    /** Afficher la barre de navigation + le footer ? (false pour la page de connexion) */
    protected bool $withNav = true;

    /**
     * Rend une vue (app/Views/pages/{view}.php) dans le layout commun.
     *
     * @param string      $view         nom de la vue (sans .php)
     * @param array       $data         variables envoyees a la vue
     * @param string|null $scriptsView  vue de scripts specifiques (sans .php), optionnelle
     */
    protected function render(string $view, array $data = [], ?string $scriptsView = null): void
    {
        $content      = $this->capture(__DIR__ . '/../Views/pages/' . $view . '.php', $data);
        $extraScripts = $scriptsView
            ? $this->capture(__DIR__ . '/../Views/pages/' . $scriptsView . '.php', $data)
            : '';

        $title       = $this->title;
        $currentPage = $this->page;
        $withNav     = $this->withNav;

        require_once __DIR__ . '/../Views/layout.php';
    }

    /** Execute une vue et renvoie son HTML dans une variable */
    private function capture(string $file, array $data): string
    {
        extract($data);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}
