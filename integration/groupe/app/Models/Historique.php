<?php
namespace App\Models;
use PDO;
require_once __DIR__.'/Model.php';
class Historique
{
    private PDO $db;
    public function __construct() { $this->db=Model::getConnexion(); }
    public function ajouter(int $idUser,string $action,?string $description=null): bool
    {
        $q=$this->db->prepare('INSERT INTO historique(id_user,action,description,date_action) VALUES (?,?,?,CURRENT_TIMESTAMP)');
        return $q->execute([$idUser,$action,$description]);
    }
    public function getActions(): array { return $this->db->query('SELECT DISTINCT action FROM historique ORDER BY action')->fetchAll(PDO::FETCH_COLUMN); }
    private function filters(array $f,array &$params,bool $global): string
    {
        $where='';
        if (($f['q'] ?? '')!=='') {
            $columns=$global?['u.nom','u.prenom','u.email','h.action','h.description']:['h.action','h.description'];$parts=[];
            foreach ($columns as $i=>$column) { $parts[]=$column.' LIKE :q'.$i;$params['q'.$i]='%'.$f['q'].'%'; }
            $where.=' AND ('.implode(' OR ',$parts).')';
        }
        if (($f['action'] ?? '')!=='') { $where.=' AND h.action=:action';$params['action']=$f['action']; }
        if ($global && ($f['role'] ?? '')!=='') { $where.=' AND u.role=:role';$params['role']=$f['role']; }
        if (($f['date'] ?? '')!=='') {
            $day=$f['date'].' 00:00:00';$next=(new \DateTimeImmutable($f['date']))->modify('+1 day')->format('Y-m-d').' 00:00:00';
            if (($f['date_mode'] ?? '')==='before') { $where.=' AND h.date_action<:date_start';$params['date_start']=$day; }
            elseif (($f['date_mode'] ?? '')==='after') { $where.=' AND h.date_action>=:date_next';$params['date_next']=$next; }
            else { $where.=' AND h.date_action>=:date_start AND h.date_action<:date_next';$params['date_start']=$day;$params['date_next']=$next; }
        }
        return $where;
    }
    private function lire(array $f,int $limit,int $offset,?int $id): array
    {
        $global=$id===null;$params=[];
        $from=' FROM historique h'.($global?' INNER JOIN utilisateur u ON u.id_user=h.id_user':'').' WHERE 1=1';
        if (!$global) { $from.=' AND h.id_user=:id';$params['id']=$id; }
        $from.=$this->filters($f,$params,$global);
        $count=$this->db->prepare('SELECT COUNT(*)'.$from);$count->execute($params);$total=(int)$count->fetchColumn();
        $columns='h.action,h.description,h.date_action'.($global?',u.id_user,u.nom,u.prenom,u.email,u.role':'');
        $q=$this->db->prepare('SELECT '.$columns.$from.' ORDER BY h.date_action DESC,h.id_historique DESC LIMIT :lim OFFSET :off');
        foreach ($params as $key=>$value) { $q->bindValue(':'.$key,$value); }
        $q->bindValue(':lim',max(1,min(100,$limit)),PDO::PARAM_INT);$q->bindValue(':off',max(0,$offset),PDO::PARAM_INT);$q->execute();
        return ['rows'=>$q->fetchAll(),'total'=>$total];
    }
    public function getPersonnel(int $idUser,array $f,int $limit,int $offset): array { return $this->lire($f,$limit,$offset,$idUser); }
    public function getGlobal(array $f,int $limit,int $offset): array { return $this->lire($f,$limit,$offset,null); }
    public function getStats(): array
    {
        return ['total'=>(int)$this->db->query('SELECT COUNT(*) FROM historique')->fetchColumn(),
            'today'=>(int)$this->db->query('SELECT COUNT(*) FROM historique WHERE DATE(date_action)=CURRENT_DATE')->fetchColumn(),
            'users'=>(int)$this->db->query('SELECT COUNT(DISTINCT id_user) FROM historique')->fetchColumn()];
    }
}
