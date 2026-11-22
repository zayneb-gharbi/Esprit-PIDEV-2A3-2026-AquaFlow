CREATE TABLE amir_utilisateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT, nom TEXT NOT NULL, email TEXT UNIQUE,
    telephone TEXT NOT NULL DEFAULT '', adresse TEXT NOT NULL DEFAULT '', password TEXT NOT NULL,
    id_role INTEGER NOT NULL DEFAULT 1, statut TEXT NOT NULL DEFAULT 'Actif', version INTEGER NOT NULL DEFAULT 1,
    totp_secret TEXT, totp_last INTEGER NOT NULL DEFAULT -1, created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE amir_identites (
    utilisateur_id INTEGER NOT NULL REFERENCES amir_utilisateurs(id) ON DELETE CASCADE,
    fournisseur TEXT NOT NULL, identifiant TEXT NOT NULL,
    PRIMARY KEY(fournisseur,identifiant), UNIQUE(utilisateur_id,fournisseur)
);
CREATE TABLE amir_secours (
    utilisateur_id INTEGER NOT NULL REFERENCES amir_utilisateurs(id) ON DELETE CASCADE,
    empreinte TEXT NOT NULL, PRIMARY KEY(utilisateur_id,empreinte)
);
CREATE TABLE amir_tentatives (id INTEGER PRIMARY KEY AUTOINCREMENT, cle TEXT NOT NULL, instant INTEGER NOT NULL);
CREATE INDEX tentatives_cle ON amir_tentatives(cle,instant);
