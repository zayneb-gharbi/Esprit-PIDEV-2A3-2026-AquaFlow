-- À exécuter une seule fois dans le schéma Oracle AQUAFLOW.
-- PR 1 : distinguer "bloqué par l'administrateur" de "désactivé par l'utilisateur".
-- Aucune donnée n'est modifiée : les comptes déjà 'bloque' restent des blocages admin.
-- Les désactivations volontaires écriront désormais 'desactive' (voir ProfileController).

SET DEFINE OFF;

-- Si une contrainte CHECK porte déjà sur STATUT avec l'ancienne liste de valeurs,
-- on la supprime avant de poser la nouvelle (Oracle n'a pas de "DROP CONSTRAINT IF EXISTS").
-- On identifie la contrainte par sa colonne, pas par SEARCH_CONDITION (type LONG selon les versions).
BEGIN
    FOR c IN (
        SELECT DISTINCT con.constraint_name
        FROM user_constraints con
        JOIN user_cons_columns col ON col.constraint_name = con.constraint_name
        WHERE con.table_name = 'UTILISATEUR'
          AND con.constraint_type = 'C'
          AND col.column_name = 'STATUT'
    ) LOOP
        EXECUTE IMMEDIATE 'ALTER TABLE utilisateur DROP CONSTRAINT "' || c.constraint_name || '"';
    END LOOP;
END;
/

ALTER TABLE utilisateur
    ADD CONSTRAINT chk_utilisateur_statut
    CHECK (statut IN ('en_attente', 'actif', 'bloque', 'desactive'));

-- Vérification rapide après migration :
-- SELECT statut, COUNT(*) FROM utilisateur GROUP BY statut ORDER BY statut;
