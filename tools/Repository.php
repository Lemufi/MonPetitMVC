<?php
declare (strict_types=1);
namespace Tools;

use PDO;

abstract class Repository {
    
    private string $classeNameLong;
    private string $classeNamespace;
    private string $table;
    private PDO $connexion;
    
    private function __construct(string $entity){
        $tablo = explode("\\", $entity);
        $this->table = array_pop($tablo);
        $this->classeNamespace = implode("\\", $tablo);
        $this->classeNameLong = $entity;
        $this->connexion = Connexion::getConnexion();
    }
    
    public static function getRepository(string $entity): Repository {
        $repositoryName = str_replace('Entity', 'Repository', $entity) . 'Repository';
        $repository = new $repositoryName($entity);
        return $repository;
    }
    
    public function findAll() : array {
        $sql = "select * from " . $this->table;
        $lignes = $this->connexion->query($sql);
        $lignes->setFetchMode(PDO::FETCH_CLASS, $this->classeNameLong, null);
        return $lignes->fetchAll();
    }
    
    public function insert(object $objet) : void {
        // conversion d'un objet en tableau
        $attributs = (array) $objet;
        array_shift($attributs);
        $colonnes = "(";
        $colonnesParams = "(";
        $parametres = array();
        foreach ($attributs as $cle => $valeur) {
            $cle = str_replace("\0", "", $cle);
            $c = str_replace($this->classeNameLong, "", $cle);
            $p = ":" . $c;
            if ($c != "id") {
                $colonnes .= $c . ",";
                $colonnesParams .= " ? ,";
                $parametres[] = $valeur;
            }
        }
        $cols = substr($colonnes, 0, -1);
        $colsParams = substr($colonnesParams, 0, -1);
        $sql = "insert into " . $this->table . " " . $cols . ") values " . $colsParams . ")";
        $unObjetPDO = Connexion::getConnexion();
        $req = $unObjetPDO->prepare($sql);
        $req->execute($parametres);
    }
    
    public function countRows() : int {
        return count($this->findAll());
    }
    
    public function executeSQL(string $sql): ?array {
        $resultat= $this->connexion->query($sql);
        return $resultat->fetchAll(PDO::FETCH_ASSOC);
    }
}