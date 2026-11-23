-- AquaFlow — schema MySQL/MariaDB UNIQUE pour XAMPP.
-- Une seule base `aquaflow` contient les 5 tables des deux modules :
--   * module Utilisateurs/Historique (MVC) : utilisateur, historique
--   * module Evenements                   : users, events, event_registrations
-- Aucun conflit de noms : `users` (evenements) != `utilisateur` (MVC).
--
-- Import : C:\xampp\mysql\bin\mysql.exe -uroot < database\aquaflow_mysql.sql
-- Sans suppression de donnees : CREATE TABLE IF NOT EXISTS + INSERT IGNORE.

CREATE DATABASE IF NOT EXISTS aquaflow
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aquaflow;

-- ---------------------------------------------------------------------------
-- 1. Module Utilisateurs (MVC inherite de origin/zayneb + PR Isra)
-- ---------------------------------------------------------------------------
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

-- ---------------------------------------------------------------------------
-- 2. Module Evenements (schema exact de aquaflow_events, sans ecraser ses donnees)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name    VARCHAR(80)  NOT NULL,
    last_name     VARCHAR(80)  NOT NULL,
    email         VARCHAR(190) NOT NULL,
    role          ENUM('citizen','plumber','admin') NOT NULL,
    status        ENUM('active','inactive') NOT NULL DEFAULT 'active',
    password_hash VARCHAR(255) DEFAULT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role (role)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title         VARCHAR(120) NOT NULL,
    description   TEXT NOT NULL,
    event_type    VARCHAR(40) NOT NULL,
    audience      ENUM('all','citizen','plumber') NOT NULL DEFAULT 'all',
    starts_at     DATETIME NOT NULL,
    ends_at       DATETIME NOT NULL,
    location      VARCHAR(255) NOT NULL,
    zone          VARCHAR(100) NOT NULL,
    capacity      SMALLINT UNSIGNED NOT NULL,
    status        ENUM('draft','published','cancelled') NOT NULL DEFAULT 'draft',
    cancel_reason VARCHAR(500) DEFAULT NULL,
    created_by    INT UNSIGNED NOT NULL,
    cancelled_by  INT UNSIGNED DEFAULT NULL,
    cancelled_at  DATETIME DEFAULT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_events_status_starts (status, starts_at),
    KEY idx_events_starts (starts_at),
    KEY idx_events_type (event_type),
    KEY idx_events_zone (zone),
    KEY idx_events_creator (created_by),
    KEY fk_events_cancelled_by (cancelled_by),
    CONSTRAINT fk_events_cancelled_by FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_events_creator FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT chk_events_capacity CHECK (capacity BETWEEN 1 AND 500),
    CONSTRAINT chk_events_dates CHECK (ends_at > starts_at),
    CONSTRAINT chk_events_cancel_reason
        CHECK (status <> 'cancelled' OR (cancel_reason IS NOT NULL AND CHAR_LENGTH(cancel_reason) > 0))
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS event_registrations (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id       INT UNSIGNED NOT NULL,
    user_id        INT UNSIGNED NOT NULL,
    registered_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status         ENUM('registered','cancelled') NOT NULL DEFAULT 'registered',
    cancelled_at   DATETIME DEFAULT NULL,
    active_user_id INT UNSIGNED GENERATED ALWAYS AS
        (CASE WHEN status = 'registered' THEN user_id ELSE NULL END) STORED,
    PRIMARY KEY (id),
    UNIQUE KEY uq_active_registration (event_id, active_user_id),
    KEY idx_reg_event_status (event_id, status),
    KEY idx_reg_user_status (user_id, status),
    CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_reg_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- 3. Comptes de test du module Utilisateurs (mot de passe : Test1234!)
-- ---------------------------------------------------------------------------
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

-- ---------------------------------------------------------------------------
-- 4. Pont d'identite entre le login MVC et le profil Evenements.
--    Les emails ne se recoupent pas : mapping explicite, sans toucher aux donnees.
--    (voir aussi database/006_integrated_event_auth.sql)
-- ---------------------------------------------------------------------------
ALTER TABLE users
    ADD COLUMN utilisateur_id INT NULL UNIQUE AFTER id,
    ADD CONSTRAINT fk_users_utilisateur
        FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id_user);

UPDATE users SET utilisateur_id = 2 WHERE id = 1;
UPDATE users SET utilisateur_id = 5 WHERE id = 2;
UPDATE users SET utilisateur_id = 1 WHERE id = 3;
UPDATE users SET utilisateur_id = 3 WHERE id = 4;
UPDATE users SET utilisateur_id = 4 WHERE id = 5;
UPDATE users SET utilisateur_id = 6 WHERE id = 6;

-- Verification :
-- SELECT table_name FROM information_schema.tables WHERE table_schema='aquaflow';
