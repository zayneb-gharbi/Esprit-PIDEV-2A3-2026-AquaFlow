<?php
class AmirDetails
{
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function consulter($admin, $id)
    {
        AmirAuth::administrateur($admin);
        if (!is_string($id) || !ctype_digit($id) || (int) $id < 1 || strlen($id) > 10) {
            http_response_code(400);
            throw new DomainException('Identifiant utilisateur invalide.');
        }
        $user = $this->model->getById((int) $id);
        if (!$user) {
            http_response_code(404);
            throw new DomainException('Utilisateur introuvable.');
        }
        $details = array_intersect_key($user, array_flip(['id','nom','email','telephone','adresse','nom_role','statut',
                'created_at','zone_service','experience']));
        $details['statut']=['actif'=>'Actif','en_attente'=>'En attente','bloque'=>'Bloqué','desactive'=>'Désactivé'][$user['statut_code']] ?? $user['statut'];
        $details['double_auth'] = $user['totp_secret'] ? 'Activée' : 'Désactivée';
        $details['services'] = $this->model->identites($user['id']);
        return $details;
    }
}
