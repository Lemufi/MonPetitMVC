<?php

declare (strict_types=1);

namespace App\Controller;

use App\Model\GestionClientModel;
use App\Entity\Client;
use ReflectionClass;
use App\Exceptions\AppException;
use Tools\MyTwig;
use Tools\Repository;

class GestionClientController {

    public function chercheUn(array $params) {
        $modele = new GestionClientModel();
        // on récupère tous les id des clients
        $ids = $modele->findIds();
        // on place les ids trouvés dans le tableau de paramètres à envoyer à la vue
        $params['lesId'] = $ids;
        // on teste si l'id du client à chercher à été passé dans l'URL
        if (array_key_exists('id', $params)) {
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $unClient = $modele->find($id);
            if ($unClient) {
                // le client a été trouvé
                $params['unClient'] = $unClient;
            } else {
                // le client a été cherché mais pas trouvé
                $params['message'] = "Client " . $id . " inconnu";
            }
        }
        $r = new ReflectionClass($this);
        $vue = str_replace('Controller', 'View', $r->getShortName()) . "/unClient.html.twig";
        MyTwig::afficheVue($vue, $params);
    }

    public function cherchetous() : void {
        // récupération d'un objet ClientRepository
        $repository = Repository::getRepository("App\Entity\Client");
        $clients = $repository->findAll();
        if ($clients) {
            $r = new ReflectionClass($this);
            $vue = str_replace('Controller', 'View', $r->getShortName()) . "/plusieursClients.html.twig";
            MyTwig::afficheVue($vue, array("desClients"=>$clients));
        } else {
            throw new AppException("Aucun Client à afficher");
        }
    }

    public function creerClient(array $params) : void {
        if (empty($params)) { 
            $vue = "GestionClientView\\creerClient.html.twig";
            MyTwig::afficheVue($vue, array());
        } else {
            try {
                $params = $this->verificationSaisieClient($params);
                // création de l'objet client à partir des données du formulaire
                $client = new Client($params);
                $repository = Repository::getRepository("App\Entity\Client");
                $repository->insert($client);
                $this->cherchetous();
            } catch (Exception) {
                throw new AppException("Erreur à l'enregistrement d'un nouveau client");
            }
        }
    }

    public function enregistreClient(array $params) {
        try {
            // création de l'objet client à partir des données du formulaire
            $client = new Client($params);
            $modele = new GestionClientModel();
            $modele->enregistreClient($client);
        } catch (Exception) {
            throw new AppException("Erreur à l'enregistrement d'un nouveau client");
        }
    }
    
    public function nbClients(array $params) : void{
        $repository = Repository::getRepository("App\Entity\Client");
        $nbClients = $repository->countRows();
        echo "nombre de clients : " . $nbClients;
    }

    public function statsClients(){
        $vue = "GestionClientView\\statsClients.html.twig";
        MyTwig::afficheVue($vue, array());
    }
    
    public function chercheUnAjax(array $params) {
        $modele = new GestionClientModel();
        // on récupère tous les id des clients
        $ids = $modele->findIds();
        // on place les ids trouvés dans le tableau de paramètres à envoyer à la vue
        $params['lesId'] = $ids;
        if (array_key_exists('id', $params)) {
            $id = filter_var(intval($params["id"]), FILTER_VALIDATE_INT);
            $unClient = $modele->find($id);
            if ($unClient) {
                // le client a été trouvé
                $params['unClient'] = $unClient;
            } else {
                // le client a été cherché mais pas trouvé
                $params['message'] = "Client " . $id . " inconnu";
            }
        }
        $vue = "GestionClientView\\unClientAjax.html.twig";
        MyTwig::afficheVue($vue, array());
    }
}
