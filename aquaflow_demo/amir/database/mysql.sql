CREATE TABLE IF NOT EXISTS amir_utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    email VARCHAR(190) NULL UNIQUE,
    telephone VARCHAR(40) NOT NULL DEFAULT '',
    adresse VARCHAR(255) NOT NULL DEFAULT '',
    password VARCHAR(255) NOT NULL,
    id_role INT NOT NULL DEFAULT 1,
    statut VARCHAR(20) NOT NULL DEFAULT 'Actif',
    version INT NOT NULL DEFAULT 1,
    totp_secret TEXT NULL,
    totp_last BIGINT NOT NULL DEFAULT -1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS amir_identites (
    utilisateur_id INT NOT NULL,
    fournisseur VARCHAR(20) NOT NULL,
    identifiant VARCHAR(190) COLLATE utf8mb4_bin NOT NULL,
    PRIMARY KEY (fournisseur,identifiant),
    UNIQUE KEY compte_service (utilisateur_id,fournisseur),
    FOREIGN KEY (utilisateur_id) REFERENCES amir_utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS amir_secours (
    utilisateur_id INT NOT NULL,
    empreinte CHAR(64) NOT NULL,
    PRIMARY KEY (utilisateur_id,empreinte),
    FOREIGN KEY (utilisateur_id) REFERENCES amir_utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS amir_tentatives (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    cle CHAR(64) NOT NULL,
    instant BIGINT NOT NULL,
    INDEX tentatives_cle (cle,instant)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
