<?php
class Installation
{
    public static function schema($db)
    {
        $sqlite=$db->getAttribute(PDO::ATTR_DRIVER_NAME)==='sqlite';
        $pk=$sqlite?'INTEGER PRIMARY KEY AUTOINCREMENT':'INT AUTO_INCREMENT PRIMARY KEY';
        $end=$sqlite?'':' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
        $common="totp_secret TEXT NULL,totp_last BIGINT NOT NULL DEFAULT -1,version INT NOT NULL DEFAULT 1,adresse VARCHAR(255) NOT NULL DEFAULT ''";
        $db->exec(integrationMode()==='groupe'
            ? "CREATE TABLE IF NOT EXISTS utilisateur(id_user $pk,nom VARCHAR(150) NOT NULL,prenom VARCHAR(150) NOT NULL DEFAULT '',email VARCHAR(150) UNIQUE,telephone VARCHAR(40) UNIQUE,mot_de_passe VARCHAR(255) NOT NULL,role VARCHAR(10) NOT NULL DEFAULT 'C',statut VARCHAR(20) NOT NULL DEFAULT 'actif',zone_service VARCHAR(150),experience INT,date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,$common)$end"
            : "CREATE TABLE IF NOT EXISTS users(id $pk,username VARCHAR(50) NOT NULL UNIQUE,nom_complet VARCHAR(150) NOT NULL DEFAULT '',email VARCHAR(150) UNIQUE,password VARCHAR(255) NOT NULL,role VARCHAR(10) NOT NULL DEFAULT 'citoyen',status VARCHAR(20) NOT NULL DEFAULT 'actif',telephone VARCHAR(40),created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,$common)$end");
        $table=integrationMode()==='groupe'?'utilisateur':'users';$id=integrationMode()==='groupe'?'id_user':'id';
        $db->exec("CREATE TABLE IF NOT EXISTS password_resets(id $pk,user_id INT NOT NULL,token_hash CHAR(64) NOT NULL UNIQUE,expires_at DATETIME NOT NULL,used_at DATETIME,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES $table($id) ON DELETE CASCADE)$end");
        $db->exec("CREATE TABLE IF NOT EXISTS amir_identites(utilisateur_id INT NOT NULL,fournisseur VARCHAR(20) NOT NULL,identifiant VARCHAR(255) NOT NULL,PRIMARY KEY(fournisseur,identifiant),UNIQUE(utilisateur_id,fournisseur),FOREIGN KEY(utilisateur_id) REFERENCES $table($id) ON DELETE CASCADE)$end");
        $db->exec("CREATE TABLE IF NOT EXISTS amir_secours(utilisateur_id INT NOT NULL,empreinte CHAR(64) NOT NULL,PRIMARY KEY(utilisateur_id,empreinte),FOREIGN KEY(utilisateur_id) REFERENCES $table($id) ON DELETE CASCADE)$end");
        $db->exec("CREATE TABLE IF NOT EXISTS amir_tentatives(id $pk,cle CHAR(64) NOT NULL,instant BIGINT NOT NULL)$end");
        if (integrationMode()==='groupe') {
            $db->exec("CREATE TABLE IF NOT EXISTS historique(id_historique $pk,id_user INT NOT NULL,action VARCHAR(100) NOT NULL,description TEXT,date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(id_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE)$end");
        }
        $db->exec("CREATE TABLE IF NOT EXISTS integration_imports(source VARCHAR(40) NOT NULL,ancien_id INT NOT NULL,nouveau_id INT NOT NULL,PRIMARY KEY(source,ancien_id))$end");
        $db->query("SELECT totp_secret,totp_last,version,adresse FROM $table WHERE 1=0");
    }
}
