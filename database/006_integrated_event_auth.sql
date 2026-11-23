-- Intégration du module Événements dans le login MVC unique.
-- Aucune donnée événementielle n'est supprimée : les 103 users, 8 events et
-- 154 event_registrations restent intact. On ajoute seulement un pont explicite
-- entre un compte MVC (utilisateur.id_user) et son profil événement (users.id).
--
-- Les emails des deux tables ne se recoupent pas (0 correspondance), d'où ce
-- mapping explicite plutôt qu'un JOIN par email.

USE aquaflow;

ALTER TABLE users
    ADD COLUMN utilisateur_id INT NULL UNIQUE AFTER id,
    ADD CONSTRAINT fk_users_utilisateur
        FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id_user);

-- Rôles cohérents : A -> admin, C -> citizen, P -> plumber.
UPDATE users SET utilisateur_id = 2 WHERE id = 1; -- mariem.citoyen  -> MVC citoyen (2)
UPDATE users SET utilisateur_id = 5 WHERE id = 2; -- karim.plombier  -> MVC plombier (5)
UPDATE users SET utilisateur_id = 1 WHERE id = 3; -- sonia.admin     -> MVC admin (1)
UPDATE users SET utilisateur_id = 3 WHERE id = 4; -- participant001  -> MVC bloque (3)
UPDATE users SET utilisateur_id = 4 WHERE id = 5; -- participant002  -> MVC desactive (4)
UPDATE users SET utilisateur_id = 6 WHERE id = 6; -- participant003  -> MVC plombier2 (6)

-- Vérification :
-- SELECT u.id, u.email, u.role, u.utilisateur_id, t.email AS mvc_email
-- FROM users u JOIN utilisateur t ON t.id_user = u.utilisateur_id ORDER BY u.id;
