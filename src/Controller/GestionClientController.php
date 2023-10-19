<?php
declare(strict_types=1);
namespace App\Controller;

use App\Model\GestionClientModel;
use ReflectionClass;
use App\Exceptions\AppException;

class GestionClientController{
    public function chercheUn(array $params){
        $modele = new GestionClientModel();
        $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
        $unClient = $modele->find($id);
        if ($unClient){
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "/unClient.html.twig";
            MyTwig::affiche_Vue($vue, array('unClient' => $unClient));
        }
        else {
            throw new AppException("Client " . $id . " inconnu");
        }
    }
    
    public function cherchetous(){
        $modele = new GestionClientModel();
        $clients = $modele->findAll();
        if($clients){
            $r = new ReflectionClass($this);
            include_once PATH_VIEW . str_replace('Controller', 'View', $r->getShortName()) . "/plusieursClients.php";
        } else {
            throw new AppException("Aucun Client à afficher");
        }
    }
}
