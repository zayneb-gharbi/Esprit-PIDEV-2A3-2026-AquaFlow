-- À exécuter une seule fois dans le schéma Oracle AQUAFLOW.
-- Cette migration ajoute uniquement la table d'historique et ne supprime aucune table existante.

CREATE TABLE historique (
    id_historique NUMBER(10) PRIMARY KEY,
    id_user NUMBER(10) NOT NULL,
    action VARCHAR2(100) NOT NULL,
    description VARCHAR2(1000),
    date_action DATE DEFAULT SYSDATE NOT NULL,
    CONSTRAINT fk_historique_utilisateur
        FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
);

CREATE SEQUENCE seq_historique START WITH 1 INCREMENT BY 1 NOCACHE;

CREATE OR REPLACE TRIGGER trg_historique_id
BEFORE INSERT ON historique
FOR EACH ROW
WHEN (NEW.id_historique IS NULL)
BEGIN
    SELECT seq_historique.NEXTVAL INTO :NEW.id_historique FROM dual;
END;
/

CREATE INDEX idx_historique_user_date ON historique(id_user, date_action);
CREATE INDEX idx_historique_date ON historique(date_action);
