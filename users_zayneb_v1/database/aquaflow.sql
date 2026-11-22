CREATE DATABASE IF NOT EXISTS aquaflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aquaflow;

DROP TABLE IF EXISTS historique;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS role;

CREATE TABLE role (
  id_role INT AUTO_INCREMENT PRIMARY KEY,
  nom_role VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO role (id_role, nom_role) VALUES
(1, 'admin'), (2, 'citoyen'), (3, 'plombier');

CREATE TABLE utilisateur (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  telephone VARCHAR(30) DEFAULT NULL,
  id_role INT NOT NULL,
  statut VARCHAR(30) NOT NULL DEFAULT 'actif',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_utilisateur_role FOREIGN KEY (id_role) REFERENCES role(id_role)
) ENGINE=InnoDB;

CREATE TABLE historique (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL,
  action VARCHAR(100) NOT NULL,
  description TEXT DEFAULT NULL,
  date_action DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_historique_utilisateur FOREIGN KEY (id_utilisateur)
    REFERENCES utilisateur(id) ON DELETE RESTRICT,
  INDEX idx_historique_user_date (id_utilisateur, date_action),
  INDEX idx_historique_date (date_action)
) ENGINE=InnoDB;

-- Comptes de démonstration. Mot de passe commun : password
INSERT INTO utilisateur (id, nom, prenom, email, mot_de_passe, telephone, id_role, statut, date_creation) VALUES
(1,'Admin','AquaFlow','admin@aquaflow.tn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','22111222',1,'actif','2026-11-18 08:00:00'),
(2,'Ben Ali','Ahmed','ahmed@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','22123456',2,'actif','2026-11-19 09:00:00'),
(3,'Trabelsi','Mariem','mariem@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','55123456',3,'actif','2026-11-19 10:30:00'),
(4,'Mansouri','Youssef','youssef@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','99123456',2,'actif','2026-11-20 11:00:00'),
(5,'Saidi','Sami','sami@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','23123456',3,'actif','2026-11-20 12:00:00'),
(6,'Khalfallah','Nour','nour@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','98123456',3,'actif','2026-11-21 08:15:00');

INSERT INTO historique (id_utilisateur, action, description, date_action) VALUES
(1,'Connexion','Connexion administrateur réussie','2026-11-20 08:30:00'),
(1,'Approbation compte','Approbation du compte de Sami Saidi','2026-11-20 09:00:00'),
(2,'Connexion','Connexion au compte','2026-11-20 09:15:00'),
(2,'Modification du profil','Modification du numéro de téléphone','2026-11-20 10:00:00'),
(2,'Déconnexion','Déconnexion du compte','2026-11-20 10:30:00'),
(3,'Connexion','Connexion au compte plombier','2026-11-20 11:00:00'),
(3,'Modification du profil','Mise à jour des informations personnelles','2026-11-20 11:30:00'),
(4,'Création de compte','Inscription d’un citoyen','2026-11-20 12:00:00'),
(5,'Connexion','Connexion au compte plombier','2026-11-20 13:00:00'),
(5,'Changement mot de passe','Mot de passe modifié','2026-11-20 14:00:00'),
(6,'Connexion','Connexion au compte plombier','2026-11-21 08:30:00'),
(6,'Consultation du profil','Consultation des informations personnelles','2026-11-21 09:00:00');
