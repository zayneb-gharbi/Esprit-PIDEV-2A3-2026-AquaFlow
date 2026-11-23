<?php
namespace App\Controllers;

use App\Models\Model;
use PDO;

/**
 * EventController — module « Gestion des événements » intégré à l'app MVC.
 *
 * Une seule authentification : la session MVC (utilisateur.id_user, role A/C/P).
 * Le profil événement correspondant est résolu via users.utilisateur_id.
 * Toutes les données événementielles restent dans la base `aquaflow`.
 */
class EventController extends Controller
{
    private const TYPES = [
        'Sensibilisation', 'Atelier technique', 'Formation plombiers',
        'Action citoyenne', 'Maintenance programmée',
    ];

    private const ZONES = [
        'Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Nabeul', 'Zaghouan',
        'Bizerte', 'Béja', 'Jendouba', 'Kairouan', 'Kasserine', 'Sidi Bouzid',
        'Sousse', 'Monastir', 'Mahdia', 'Sfax', 'Gafsa', 'Tozeur', 'Kebili',
        'Gabès', 'Medenine', 'Tataouine',
    ];

    private const AUDIENCE = [
        'all' => 'Tous', 'citizen' => 'Citoyens', 'plumber' => 'Plombiers',
    ];

    private const STATUS = [
        'draft' => 'Brouillon', 'published' => 'Publié', 'full' => 'Complet',
        'ongoing' => 'En cours', 'finished' => 'Terminé', 'cancelled' => 'Annulé',
    ];

    private const ROLE_MAP = ['A' => 'admin', 'C' => 'citizen', 'P' => 'plumber'];

    /** Vérifie la session MVC, le statut en base, le rôle et le profil événement lié. */
    private function requireRole(string ...$mvcRoles): array
    {
        requireLogin();

        require_once __DIR__ . '/UtilisateurC.php';
        $uc = new UtilisateurC();
        $mvc = $uc->getUtilisateur($_SESSION['id_user']);
        if (!$mvc || $mvc['statut'] !== 'actif') {
            redirect('logout');
        }

        $eventRole = self::ROLE_MAP[$mvc['role']] ?? '';
        $allowed = array_map(fn ($r) => self::ROLE_MAP[$r] ?? $r, $mvcRoles);
        if (!in_array($eventRole, $allowed, true)) {
            http_response_code(403);
            require __DIR__ . '/../Views/pages/errors/403.php';
            exit;
        }

        $stmt = Model::getConnexion()->prepare(
            'SELECT id, first_name, last_name, email, role FROM users
              WHERE utilisateur_id = :id LIMIT 1'
        );
        $stmt->execute(['id' => (int) $_SESSION['id_user']]);
        $profile = $stmt->fetch();

        if (!$profile) {
            http_response_code(403);
            require __DIR__ . '/../Views/pages/errors/403.php';
            exit;
        }

        return [
            'id'    => (int) $profile['id'],
            'role'  => $profile['role'],
            'name'  => trim($profile['first_name'] . ' ' . $profile['last_name']),
            'email' => $profile['email'],
            'mvc'   => $mvc,
        ];
    }

    private function csrfOrFail(): void
    {
        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            exit('Jeton CSRF invalide.');
        }
    }

    private function str(array $source, string $key): string
    {
        return isset($source[$key]) && is_string($source[$key]) ? trim($source[$key]) : '';
    }

    private function int(array $source, string $key): int
    {
        return isset($source[$key]) && is_numeric($source[$key]) ? (int) $source[$key] : 0;
    }

    private function len(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    }

    private function fmt(?string $date, bool $withTime = false): string
    {
        if (!$date) {
            return '—';
        }
        $ts = strtotime($date);
        return $ts ? date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $ts) : '—';
    }

    private function computeStatus(array $row): string
    {
        if ($row['status'] === 'cancelled') {
            return 'cancelled';
        }
        $now  = time();
        $start = strtotime((string) $row['starts_at']) ?: 0;
        $end   = strtotime((string) $row['ends_at']) ?: 0;
        if ($end < $now) {
            return 'finished';
        }
        if ($start <= $now && $end >= $now) {
            return 'ongoing';
        }
        if ((int) ($row['registered_count'] ?? 0) >= (int) $row['capacity']) {
            return 'full';
        }
        return (string) $row['status'];
    }

    private function prepare(array $row): array
    {
        $status = $this->computeStatus($row);
        $row['computed_status'] = $status;
        $row['status_label']    = self::STATUS[$status] ?? 'Brouillon';
        $row['audience_label']  = self::AUDIENCE[$row['audience']] ?? 'Tous';
        $row['starts_display']  = $this->fmt($row['starts_at'], true);
        $row['ends_display']    = $this->fmt($row['ends_at'], true);
        $row['date_display']    = $this->fmt($row['starts_at']);
        $row['registered_count'] = (int) ($row['registered_count'] ?? 0);
        $row['remaining']       = max(0, (int) $row['capacity'] - $row['registered_count']);
        $row['creator_name']    = trim(($row['creator_first_name'] ?? '') . ' ' . ($row['creator_last_name'] ?? '')) ?: '—';
        return $row;
    }

    private function fetchEvent(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        $stmt = Model::getConnexion()->prepare(
            'SELECT e.*,
                    (SELECT COUNT(*) FROM event_registrations r
                      WHERE r.event_id = e.id AND r.status = \'registered\') AS registered_count,
                    u.first_name AS creator_first_name, u.last_name AS creator_last_name
               FROM events e
               JOIN users u ON u.id = e.created_by
              WHERE e.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    private function notFound(): void
    {
        http_response_code(404);
        require __DIR__ . '/../Views/pages/errors/404.php';
        exit;
    }

    private function renderEvent(string $view, array $data, string $page = 'events'): void
    {
        $this->page = $page;
        $data['flash'] = $data['flash'] ?? takeFlash();
        $data['types'] = self::TYPES;
        $data['zones'] = self::ZONES;
        $this->render($view, $data);
    }

    // ------------------------------------------------------------------
    // FRONT OFFICE
    // ------------------------------------------------------------------

    public function events(): void
    {
        $user = $this->requireRole('C', 'P');

        $where = ["e.status = 'published'", 'e.starts_at > NOW()', 'e.audience IN (:audience, :all)'];
        $binds = [':audience' => $user['role'], ':all' => 'all'];

        $q = $this->str($_GET, 'q');
        if ($q !== '') {
            $where[] = '(e.title LIKE :q1 OR e.location LIKE :q2)';
            $binds[':q1'] = '%' . $q . '%';
            $binds[':q2'] = '%' . $q . '%';
        }
        $type = $this->str($_GET, 'event_type');
        if (in_array($type, self::TYPES, true)) {
            $where[] = 'e.event_type = :event_type';
            $binds[':event_type'] = $type;
        }
        $zone = $this->str($_GET, 'zone');
        if (in_array($zone, self::ZONES, true)) {
            $where[] = 'e.zone = :zone';
            $binds[':zone'] = $zone;
        }

        $stmt = Model::getConnexion()->prepare(
            'SELECT e.*,
                    (SELECT COUNT(*) FROM event_registrations r
                      WHERE r.event_id = e.id AND r.status = \'registered\') AS registered_count,
                    u.first_name AS creator_first_name, u.last_name AS creator_last_name
               FROM events e
               JOIN users u ON u.id = e.created_by
              WHERE ' . implode(' AND ', $where) . '
              ORDER BY e.starts_at ASC, e.id ASC'
        );
        $stmt->execute($binds);

        $events = [];
        foreach ($stmt->fetchAll() as $row) {
            $events[] = $this->prepare($row);
        }

        $this->renderEvent('events/list', [
            'title'   => 'Événements à venir — AquaFlow',
            'events'  => $events,
            'filters' => ['q' => $q, 'event_type' => $type, 'zone' => $zone],
        ]);
    }

    public function detail(): void
    {
        $user  = $this->requireRole('C', 'P');
        $event = $this->fetchEvent($this->int($_GET, 'id'));

        if ($event === null || $event['status'] === 'draft') {
            $this->notFound();
        }
        if ($event['audience'] !== 'all' && $event['audience'] !== $user['role']) {
            $this->notFound();
        }
        $event = $this->prepare($event);

        $stmt = Model::getConnexion()->prepare(
            'SELECT id FROM event_registrations
              WHERE event_id = :event_id AND user_id = :user_id AND status = \'registered\''
        );
        $stmt->execute(['event_id' => (int) $event['id'], 'user_id' => $user['id']]);
        $registered = $stmt->fetch() !== false;

        $reasons = [];
        if ($event['computed_status'] !== 'published') {
            $reasons[] = 'Statut de l\'événement : ' . $event['status_label'] . '.';
        }
        if (strtotime($event['starts_at']) <= time()) {
            $reasons[] = 'Cet événement a déjà commencé.';
        }
        if ($event['remaining'] === 0) {
            $reasons[] = 'Cet événement est complet.';
        }
        if ($event['audience'] !== 'all' && $event['audience'] !== $user['role']) {
            $reasons[] = 'Cet événement est réservé à un autre public.';
        }

        $this->renderEvent('events/detail', [
            'title'    => $event['title'] . ' — AquaFlow',
            'event'    => $event,
            'variant'  => $registered ? 'registered' : ($reasons === [] ? 'register' : 'impossible'),
            'reasons'  => $reasons,
            'canUnregister' => $registered && (strtotime($event['starts_at']) - time()) >= 86400,
        ]);
    }

    public function register(): void
    {
        $user = $this->requireRole('C', 'P');
        $this->csrfOrFail();
        $eventId = $this->int($_POST, 'event_id') ?: $this->int($_GET, 'id');

        $pdo = Model::getConnexion();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $eventId]);
            $event = $stmt->fetch();
            if ($event === false) {
                $pdo->rollBack();
                $this->notFound();
            }

            $countStmt = $pdo->prepare(
                'SELECT COUNT(*) FROM event_registrations WHERE event_id = :id AND status = \'registered\''
            );
            $countStmt->execute(['id' => $eventId]);
            $count = (int) $countStmt->fetchColumn();

            $alreadyStmt = $pdo->prepare(
                'SELECT id FROM event_registrations
                  WHERE event_id = :event_id AND user_id = :user_id AND status = \'registered\''
            );
            $alreadyStmt->execute(['event_id' => $eventId, 'user_id' => $user['id']]);
            $already = $alreadyStmt->fetch() !== false;

            if ($event['status'] !== 'published') {
                setFlash('Les inscriptions à cet événement ne sont pas ouvertes.', 'error');
            } elseif (strtotime($event['starts_at']) <= time()) {
                setFlash('Cet événement a déjà commencé.', 'error');
            } elseif ($count >= (int) $event['capacity']) {
                setFlash('Cet événement est complet.', 'error');
            } elseif ($event['audience'] !== 'all' && $event['audience'] !== $user['role']) {
                setFlash('Cet événement est réservé à un autre public.', 'error');
            } elseif ($already) {
                setFlash('Vous êtes déjà inscrit à cet événement.', 'error');
            } else {
                $insert = $pdo->prepare(
                    'INSERT INTO event_registrations (event_id, user_id, registered_at, status)
                     VALUES (:event_id, :user_id, NOW(), \'registered\')'
                );
                $insert->execute(['event_id' => $eventId, 'user_id' => $user['id']]);
                setFlash('Votre inscription est confirmée.');
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            setFlash("L'inscription n'a pas pu être enregistrée.", 'error');
        }

        redirect('event-detail&id=' . $eventId);
    }

    public function unregister(): void
    {
        $user = $this->requireRole('C', 'P');
        $this->csrfOrFail();

        $eventId  = $this->int($_POST, 'event_id') ?: $this->int($_GET, 'id');
        $returnTo = $this->str($_POST, 'return_to') === 'my-events' ? 'my-events' : 'event-detail';
        $event    = $this->fetchEvent($eventId);

        if ($event === null) {
            $this->notFound();
        }
        if (strtotime($event['starts_at']) - time() < 86400) {
            setFlash('La désinscription n\'est plus possible moins de 24 heures avant le début.', 'error');
        } else {
            $stmt = Model::getConnexion()->prepare(
                'UPDATE event_registrations
                    SET status = \'cancelled\', cancelled_at = NOW()
                  WHERE event_id = :event_id AND user_id = :user_id AND status = \'registered\''
            );
            $stmt->execute(['event_id' => $eventId, 'user_id' => $user['id']]);
            $updated = $stmt->rowCount() > 0;
            setFlash(
                $updated ? 'Vous êtes désinscrit de cet événement.' : "Vous n'êtes pas inscrit à cet événement.",
                $updated ? 'success' : 'error'
            );
        }

        redirect($returnTo === 'event-detail' ? 'event-detail&id=' . $eventId : 'my-events');
    }

    public function myEvents(): void
    {
        $user = $this->requireRole('C', 'P');

        $stmt = Model::getConnexion()->prepare(
            'SELECT e.*, r.id AS registration_id, r.registered_at,
                    (SELECT COUNT(*) FROM event_registrations x
                      WHERE x.event_id = e.id AND x.status = \'registered\') AS registered_count,
                    u.first_name AS creator_first_name, u.last_name AS creator_last_name
               FROM event_registrations r
               JOIN events e ON e.id = r.event_id
               JOIN users u ON u.id = e.created_by
              WHERE r.user_id = :user_id AND r.status = \'registered\'
              ORDER BY e.starts_at ASC, e.id ASC'
        );
        $stmt->execute(['user_id' => $user['id']]);

        $events = [];
        foreach ($stmt->fetchAll() as $row) {
            $event = $this->prepare($row);
            $event['registered_at_display'] = $this->fmt($event['registered_at'], true);
            $event['can_unregister'] = (strtotime($event['starts_at']) - time()) >= 86400;
            $events[] = $event;
        }

        $this->renderEvent('events/my_events', [
            'title'  => 'Mes participations — AquaFlow',
            'events' => $events,
        ], 'my-events');
    }

    // ------------------------------------------------------------------
    // BACK OFFICE
    // ------------------------------------------------------------------

    public function adminEvents(): void
    {
        $this->requireRole('A');

        $where = ['1 = 1'];
        $binds = [];
        $q = $this->str($_GET, 'q');
        if ($q !== '') {
            $where[] = '(e.title LIKE :q1 OR e.location LIKE :q2)';
            $binds[':q1'] = '%' . $q . '%';
            $binds[':q2'] = '%' . $q . '%';
        }
        $status = $this->str($_GET, 'status');
        if (array_key_exists($status, self::STATUS)) {
            $where[] = 'e.status = :status';
            $binds[':status'] = $status;
        }
        $type = $this->str($_GET, 'event_type');
        if (in_array($type, self::TYPES, true)) {
            $where[] = 'e.event_type = :event_type';
            $binds[':event_type'] = $type;
        }

        $stmt = Model::getConnexion()->prepare(
            'SELECT e.*,
                    (SELECT COUNT(*) FROM event_registrations r
                      WHERE r.event_id = e.id AND r.status = \'registered\') AS registered_count,
                    u.first_name AS creator_first_name, u.last_name AS creator_last_name
               FROM events e
               JOIN users u ON u.id = e.created_by
              WHERE ' . implode(' AND ', $where) . '
              ORDER BY e.starts_at DESC, e.id DESC'
        );
        $stmt->execute($binds);

        $events = [];
        foreach ($stmt->fetchAll() as $row) {
            $events[] = $this->prepare($row);
        }

        $statsStmt = Model::getConnexion()->query(
            'SELECT COUNT(*) AS total,
                    SUM(CASE WHEN status = \'published\' THEN 1 ELSE 0 END) AS published,
                    SUM(CASE WHEN status = \'draft\' THEN 1 ELSE 0 END) AS drafts,
                    (SELECT COUNT(*) FROM event_registrations WHERE status = \'registered\') AS registrations
               FROM events'
        );
        $stats = $statsStmt->fetch();

        $this->renderEvent('events/admin/list', [
            'title'   => 'Gérer les événements — AquaFlow',
            'events'  => $events,
            'stats'   => $stats,
            'filters' => ['q' => $q, 'status' => $status, 'event_type' => $type],
            'statuses' => self::STATUS,
        ], 'admin-events');
    }

    private function validate(array $post, bool $isEdit): array
    {
        $values = [
            'title'       => $this->str($post, 'title'),
            'event_type'  => $this->str($post, 'event_type'),
            'audience'    => $this->str($post, 'audience'),
            'description' => $this->str($post, 'description'),
            'starts_at'   => $this->str($post, 'starts_at'),
            'ends_at'     => $this->str($post, 'ends_at'),
            'location'    => $this->str($post, 'location'),
            'zone'        => $this->str($post, 'zone'),
            'capacity'    => $this->str($post, 'capacity'),
            'status'      => $this->str($post, 'status'),
        ];
        $errors = [];

        if ($this->len($values['title']) < 5 || $this->len($values['title']) > 120) {
            $errors['title'] = 'Le titre doit contenir entre 5 et 120 caractères.';
        }
        if (!in_array($values['event_type'], self::TYPES, true)) {
            $errors['event_type'] = 'Choisissez un type valide.';
        }
        if (!array_key_exists($values['audience'], self::AUDIENCE)) {
            $errors['audience'] = 'Choisissez un public valide.';
        }
        if ($this->len($values['description']) < 20 || $this->len($values['description']) > 1000) {
            $errors['description'] = 'La description doit contenir entre 20 et 1000 caractères.';
        }
        if ($values['location'] === '' || $this->len($values['location']) > 255) {
            $errors['location'] = 'Le lieu est obligatoire (255 caractères maximum).';
        }
        if (!in_array($values['zone'], self::ZONES, true)) {
            $errors['zone'] = 'Choisissez un gouvernorat valide.';
        }
        if (!preg_match('/^[1-9]\d{0,2}$/', $values['capacity']) || (int) $values['capacity'] > 500) {
            $errors['capacity'] = 'La capacité doit être un entier entre 1 et 500.';
        }
        if (!in_array($values['status'], ['draft', 'published'], true)) {
            $errors['status'] = 'Choisissez brouillon ou publié.';
        }

        $start = \DateTime::createFromFormat('!Y-m-d\TH:i', $values['starts_at']);
        $end   = \DateTime::createFromFormat('!Y-m-d\TH:i', $values['ends_at']);
        if ($start === false || $start->format('Y-m-d\TH:i') !== $values['starts_at']) {
            $errors['starts_at'] = 'Date de début invalide.';
        }
        if ($end === false || $end->format('Y-m-d\TH:i') !== $values['ends_at']) {
            $errors['ends_at'] = 'Date de fin invalide.';
        }
        if ($start !== false && $end !== false && $end <= $start) {
            $errors['ends_at'] = 'La fin doit être postérieure au début.';
        }
        if (!$isEdit && $start !== false && $start <= new \DateTime('now')) {
            $errors['starts_at'] = 'Un événement ne peut pas commencer dans le passé.';
        }

        $values['starts_at_db'] = $start === false ? '' : $start->format('Y-m-d H:i:s');
        $values['ends_at_db']   = $end === false ? '' : $end->format('Y-m-d H:i:s');
        return ['values' => $values, 'errors' => $errors];
    }

    private function form(?int $id): void
    {
        $user = $this->requireRole('A');
        $existing = $id !== null ? $this->fetchEvent($id) : null;
        if ($id !== null && $existing === null) {
            $this->notFound();
        }
        $isEdit = $existing !== null;

        $values = [
            'title' => '', 'event_type' => self::TYPES[0], 'audience' => 'all',
            'description' => '', 'starts_at' => '', 'ends_at' => '',
            'location' => '', 'zone' => 'Tunis', 'capacity' => '', 'status' => 'draft',
        ];
        if ($isEdit) {
            foreach (['title', 'event_type', 'audience', 'description', 'location', 'zone', 'status'] as $key) {
                $values[$key] = (string) $existing[$key];
            }
            $values['capacity']  = (string) $existing['capacity'];
            $values['starts_at'] = date('Y-m-d\TH:i', strtotime($existing['starts_at']) ?: 0);
            $values['ends_at']   = date('Y-m-d\TH:i', strtotime($existing['ends_at']) ?: 0);
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->csrfOrFail();
            $result = $this->validate($_POST, $isEdit);
            $values = array_merge($values, $result['values']);
            $errors = $result['errors'];

            if ($errors === []) {
                $pdo = Model::getConnexion();
                if ($isEdit) {
                    $sql = 'UPDATE events SET title = :title, description = :description,
                                event_type = :event_type, audience = :audience,
                                starts_at = :starts_at, ends_at = :ends_at, location = :location,
                                zone = :zone, capacity = :capacity, status = :status
                             WHERE id = :id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                } else {
                    $sql = 'INSERT INTO events
                                (title, description, event_type, audience, starts_at, ends_at,
                                 location, zone, capacity, status, created_by)
                             VALUES
                                (:title, :description, :event_type, :audience, :starts_at, :ends_at,
                                 :location, :zone, :capacity, :status, :created_by)';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':created_by', $user['id'], PDO::PARAM_INT);
                }
                foreach (['title', 'description', 'event_type', 'audience', 'location', 'zone', 'status'] as $key) {
                    $stmt->bindValue(':' . $key, $values[$key], PDO::PARAM_STR);
                }
                $stmt->bindValue(':starts_at', $values['starts_at_db'], PDO::PARAM_STR);
                $stmt->bindValue(':ends_at', $values['ends_at_db'], PDO::PARAM_STR);
                $stmt->bindValue(':capacity', (int) $values['capacity'], PDO::PARAM_INT);
                $stmt->execute();

                setFlash($isEdit ? 'Événement modifié.' : 'Événement créé.');
                logActivity((int) $_SESSION['id_user'], $isEdit ? 'Modification événement' : 'Création événement', 'Événement #' . ($id ?? 'nouveau'));
                redirect('admin-events');
            }
        }

        $this->renderEvent('events/admin/form', [
            'title'  => ($isEdit ? 'Modifier l\'événement' : 'Nouvel événement') . ' — AquaFlow',
            'isEdit' => $isEdit,
            'event'  => $existing,
            'values' => $values,
            'errors' => $errors,
        ], 'admin-events');
    }

    public function adminCreate(): void
    {
        $this->form(null);
    }

    public function adminEdit(): void
    {
        $this->form($this->int($_GET, 'id'));
    }

    public function adminDetail(): void
    {
        $this->requireRole('A');
        $event = $this->fetchEvent($this->int($_GET, 'id'));
        if ($event === null) {
            $this->notFound();
        }
        $event = $this->prepare($event);

        $stmt = Model::getConnexion()->prepare(
            'SELECT r.id AS registration_id, r.registered_at, u.first_name, u.last_name, u.email, u.role
               FROM event_registrations r
               JOIN users u ON u.id = r.user_id
              WHERE r.event_id = :event_id AND r.status = \'registered\'
              ORDER BY r.registered_at ASC, r.id ASC'
        );
        $stmt->execute(['event_id' => (int) $event['id']]);

        $participants = [];
        foreach ($stmt->fetchAll() as $p) {
            $p['name'] = trim($p['first_name'] . ' ' . $p['last_name']);
            $p['role_label'] = rtrim(self::AUDIENCE[$p['role']] ?? 'Citoyen', 's');
            $p['registered_at_display'] = $this->fmt($p['registered_at'], true);
            $participants[] = $p;
        }

        $this->renderEvent('events/admin/detail', [
            'title'       => $event['title'] . ' — AquaFlow',
            'event'       => $event,
            'participants'=> $participants,
        ], 'admin-events');
    }

    public function adminCancel(): void
    {
        $user = $this->requireRole('A');
        $event = $this->fetchEvent($this->int($_GET, 'id'));
        if ($event === null) {
            $this->notFound();
        }
        $event  = $this->prepare($event);
        $errors = [];
        $reason = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->csrfOrFail();
            $reason = $this->str($_POST, 'cancel_reason');
            if ($this->len($reason) === 0) {
                $errors['cancel_reason'] = 'Le motif est obligatoire.';
            } elseif ($this->len($reason) > 500) {
                $errors['cancel_reason'] = 'Le motif ne peut pas dépasser 500 caractères.';
            }
            if ($errors === []) {
                $stmt = Model::getConnexion()->prepare(
                    'UPDATE events SET status = \'cancelled\', cancel_reason = :reason,
                            cancelled_by = :admin_id, cancelled_at = NOW()
                      WHERE id = :id'
                );
                $stmt->execute([
                    'reason'  => $reason,
                    'admin_id'=> $user['id'],
                    'id'      => (int) $event['id'],
                ]);
                setFlash('Événement annulé.');
                logActivity((int) $_SESSION['id_user'], 'Annulation événement', 'Événement #' . $event['id'] . ' — ' . $reason);
                redirect('admin-events');
            }
        }

        $this->renderEvent('events/admin/cancel', [
            'title'  => 'Annuler l\'événement — AquaFlow',
            'event'  => $event,
            'reason' => $reason,
            'errors' => $errors,
        ], 'admin-events');
    }

    public function adminDelete(): void
    {
        $this->requireRole('A');
        $this->csrfOrFail();

        $id = $this->int($_POST, 'event_id');
        $event = $this->fetchEvent($id);
        if ($event === null) {
            $this->notFound();
        }

        $stmt = Model::getConnexion()->prepare('SELECT COUNT(*) FROM event_registrations WHERE event_id = :id');
        $stmt->execute(['id' => $id]);
        $registrations = (int) $stmt->fetchColumn();

        if ($event['status'] !== 'draft') {
            setFlash('Seuls les brouillons peuvent être supprimés. Annulez cet événement.', 'error');
        } elseif ($registrations > 0) {
            setFlash('Ce brouillon comporte des inscriptions : annulez-le plutôt que de le supprimer.', 'error');
        } else {
            $delete = Model::getConnexion()->prepare("DELETE FROM events WHERE id = :id AND status = 'draft'");
            $delete->execute(['id' => $id]);
            setFlash('Brouillon supprimé.');
            logActivity((int) $_SESSION['id_user'], 'Suppression événement', 'Événement #' . $id);
        }
        redirect('admin-events');
    }

    public function adminRemoveParticipant(): void
    {
        $this->requireRole('A');
        $this->csrfOrFail();

        $eventId = $this->int($_POST, 'event_id');
        $registrationId = $this->int($_POST, 'registration_id');

        $stmt = Model::getConnexion()->prepare(
            'UPDATE event_registrations SET status = \'cancelled\', cancelled_at = NOW()
              WHERE id = :registration_id AND event_id = :event_id AND status = \'registered\''
        );
        $stmt->execute(['registration_id' => $registrationId, 'event_id' => $eventId]);
        setFlash($stmt->rowCount() > 0 ? 'Participant retiré.' : 'Inscription introuvable.', $stmt->rowCount() > 0 ? 'success' : 'error');
        redirect('admin-event-detail&id=' . $eventId);
    }
}
