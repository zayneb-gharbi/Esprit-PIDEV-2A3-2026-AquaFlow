<?php
namespace App\Controllers;

require_once __DIR__ . '/UtilisateurC.php';

// ProfileEditController.php : "Modifier mes informations" (PR 4)
class ProfileEditController extends Controller
{
    protected string $page  = 'profile';
    protected string $title = 'Modifier mon profil | Aqua Flow';

    private array $user   = [];
    private array $errors = [];

    public function index(): void
    {
        requireLogin();

        $uc   = new UtilisateurC();
        $user = $uc->getUtilisateur($_SESSION['id_user']);

        // l'id vient uniquement de la session ; on relit le statut dans la base
        if (!$user || $user['statut'] != 'actif') {
            redirect('logout');
        }
        $this->user = $user;

        $values = $this->defaultValues($user);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
                setFlash('Requête invalide.', 'error');
                redirect('profile-edit');
            }

            $values = [
                'prenom'       => trim($_POST['prenom'] ?? ''),
                'nom'          => trim($_POST['nom'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'telephone'    => str_replace([' ', '-'], '', trim($_POST['telephone'] ?? '')),
                'zone_service' => trim($_POST['zone_service'] ?? ''),
                'experience'   => trim($_POST['experience'] ?? '')
            ];

            $this->errors = $this->validate($values, $uc);

            if (!$this->errors) {
                $isPlombier = $user['role'] === 'P';
                $updated = $uc->updateInfosPersonnelles(
                    (int) $_SESSION['id_user'],
                    $values['nom'],
                    $values['prenom'],
                    $values['email'],
                    $values['telephone'],
                    $isPlombier ? $values['zone_service'] : null,
                    $isPlombier ? (int) $values['experience'] : null
                );

                if ($updated === 1) {
                    $_SESSION['nom']     = $values['nom'];
                    $_SESSION['prenom']  = $values['prenom'];
                    logActivity((int) $_SESSION['id_user'], 'Modification profil',
                                'Mise à jour des informations personnelles');
                    setFlash('Vos informations ont été mises à jour.');
                    redirect('profile');
                }
                $this->errors['general'] = 'La mise à jour a échoué. Veuillez réessayer.';
            }
        }

        $this->render('profile_edit', [
            'user'   => $user,
            'values' => $values,
            'errors' => $this->errors,
            'flash'  => takeFlash()
        ], 'profile_edit_scripts');
    }

    private function defaultValues(array $user): array
    {
        return [
            'prenom'       => $user['prenom'] ?? '',
            'nom'          => $user['nom'] ?? '',
            'email'        => $user['email'] ?? '',
            'telephone'    => $user['telephone'] ?? '',
            'zone_service' => $user['zone_service'] ?? '',
            'experience'   => $user['experience'] ?? ''
        ];
    }

    private function validate(array $v, UtilisateurC $uc): array
    {
        $errors = [];

        foreach (['prenom', 'nom'] as $field) {
            $length = mb_strlen($v[$field]);
            if ($v[$field] === '') {
                $errors[$field] = 'Ce champ est obligatoire.';
            } elseif ($length < 2 || $length > 50) {
                $errors[$field] = 'Ce champ doit contenir entre 2 et 50 caractères.';
            } elseif (!preg_match('/^[\p{L}\p{M}\-\' ]+$/u', $v[$field])) {
                $errors[$field] = 'Lettres uniquement (accents, espaces, apostrophes et tirets autorisés).';
            }
        }

        if ($v['email'] === '') {
            $errors['email'] = 'Ce champ est obligatoire.';
        } elseif (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Veuillez saisir une adresse e-mail valide.';
        } elseif (strlen($v['email']) > 150) {
            $errors['email'] = 'L’e-mail ne doit pas dépasser 150 caractères.';
        }

        if ($v['telephone'] === '') {
            $errors['telephone'] = 'Ce champ est obligatoire.';
        } elseif (!preg_match('/^(?:\+216|00216)?[2-9][0-9]{7}$/', $v['telephone'])) {
            $errors['telephone'] = 'Veuillez saisir un numéro de téléphone tunisien valide.';
        }

        if (!isset($errors['email']) || !isset($errors['telephone'])) {
            if ($uc->emailOuTelephonePrisParAutre($v['email'], $v['telephone'], (int) $_SESSION['id_user'])) {
                $errors['general'] = 'Cet e-mail ou ce numéro de téléphone est déjà utilisé.';
            }
        }

        if ($this->user['role'] === 'P') {
            if ($v['zone_service'] === '') {
                $errors['zone_service'] = 'Veuillez sélectionner votre zone de service.';
            }
            $experience = filter_var($v['experience'], FILTER_VALIDATE_INT,
                                     ['options' => ['min_range' => 0, 'max_range' => 70]]);
            if ($experience === false) {
                $errors['experience'] = 'L’expérience doit être un nombre entier entre 0 et 70.';
            }
        }

        return $errors;
    }
}
