-- AquaFlow — schema MySQL/MariaDB pour XAMPP (port local de la base Oracle).
-- A importer UNE fois dans phpMyAdmin (base `aquaflow`) ou via :
--   C:\xampp\mysql\bin\mysql.exe -uroot < database\aquaflow_mysql.sql
-- Sans suppression de donnees : CREATE TABLE IF NOT EXISTS + INSERT IGNORE.

CREATE DATABASE IF NOT EXISTS aquaflow
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aquaflow;

CREATE TABLE IF NOT EXISTS utilisateur (
    id_user       INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(50)  NOT NULL,
    prenom        VARCHAR(50)  NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    telephone     VARCHAR(20)  NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255) NOT NULL,
    role          CHAR(1)      NOT NULL,
    statut        VARCHAR(20)  NOT NULL DEFAULT 'actif',
    zone_service  VARCHAR(100) NULL,
    experience    INT          NULL,
    date_creation DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_utilisateur_role   CHECK (role IN ('A', 'C', 'P')),
    CONSTRAINT chk_utilisateur_statut CHECK (statut IN ('en_attente', 'actif', 'bloque', 'desactive')),
    INDEX idx_utilisateur_role (role),
    INDEX idx_utilisateur_statut (statut)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS historique (
    id_historique INT AUTO_INCREMENT PRIMARY KEY,
    id_user       INT          NOT NULL,
    action        VARCHAR(100) NOT NULL,
    description   VARCHAR(1000) NULL,
    date_action   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historique_utilisateur
        FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
    INDEX idx_historique_user_date (id_user, date_action),
    INDEX idx_historique_date (date_action)
) ENGINE=InnoDB;

-- Comptes de test. Mot de passe commun : Test1234!
-- Hash bcrypt identique pour les 6 comptes (genere avec password_hash).
INSERT IGNORE INTO utilisateur
    (id_user, nom, prenom, email, telephone, mot_de_passe, role, statut, zone_service, experience)
VALUES
    (1, 'Admin',     'Amina',    'admin@aquaflow.tn',      '20000001',
     '$2y$10$ymFeQF3pK5D29Vunh4wGCuQGUZMNLPVfZh0ticg6IDGD60FM.q3z6', 'A', 'actif',      NULL, NULL),
    (2, 'Benali',    'Karim',    'citoyen@aquaflow.tn',    '20000002',
     '$2y$10$ymFeQF3pK5D29Vunh4wGCuQGUZMNLPVfZh0ticg6IDGD60FM.q3z6', 'C', 'actif',      NULL, NULL),
    (3, 'Trabelsi',  'Sofia',    'bloque@aquaflow.tn',     '20000003',
     '$2y$10$ymFeQF3pK5D29Vunh4wGCuQGUZMNLPVfZh0ticg6IDGD60FM.q3z6', 'C', 'bloque',     NULL, NULL),
    (4, 'Mansouri',  'Hugo',     'desactive@aquaflow.tn',  '20000004',
     '$2y$10$ymFeQF3pK5D29Vunh4wGCuQGUZMNLPVfZh0ticg6IDGD60FM.q3z6', 'C', 'desactive',  NULL, NULL),
    (5, 'Gharbi',    'Nadia',    'plombier@aquaflow.tn',   '20000005',
     '$2y$10$ymFeQF3pK5D29Vunh4wGCuQGUZMNLPVfZh0ticg6IDGD60FM.q3z6', 'P', 'en_attente', 'Tunis', 3),
    (6, 'Hermi',     'Youssef',  'plombier2@aquaflow.tn',  '20000006',
     '$2y$10$ymFeQF3pK5D29Vunh4wGCuQGUZMNLPVfZh0ticg6IDGD60FM.q3z6', 'P', 'actif',      'Sfax', 8);

-- Verification rapide :
-- SELECT id_user, role, statut, email FROM utilisateur ORDER BY id_user;
