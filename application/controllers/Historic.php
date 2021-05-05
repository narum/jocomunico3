
<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Historic extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('HistoricInterface');
        $this->load->library('session');
    }

    public function index_get() {

    }

    public function getSFolder_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idusu = $request->idusu;
        
        $sFolder = $this->HistoricInterface->getSFolders($idusu);
        
        if ($sFolder == NULL) {
            $sFolder = array();
        }

        $response = [
            'sFolder' => $sFolder
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function getHistoric_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $day = $request->day;
        $pagHistoric = $request->pagHistoric;
        $idusu = $request->idusu;

        $historicArray = array();
        $historic = $this->HistoricInterface->getHistoric($idusu, $day);
        
        for ($i = $pagHistoric; $i < count($historic) && $i < $pagHistoric + 10; $i++){
            $arrayProv[0] = $historic[$i];
            $arrayProv[1] = $this->HistoricInterface->getPictosHistoric($historic[$i]->ID_SHistoric, $idusu);
            array_push($historicArray,$arrayProv);
        }
        $count = $this->HistoricInterface->getCountHistoric($idusu, $day);

        $response = [
            'historic' => $historicArray,
            'count' => $count
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }
    
    public function getFolder_post(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idfolder = $request->folder;
        $pagHistoric = $request->pagHistoric;
        $idusu = $request->idusu;
        
        $sentenceArray = array();
        $sentenece = $this->HistoricInterface->getSentenceFolder($idusu, $idfolder);

        for ($i = $pagHistoric; $i < count($sentenece) && $i < $pagHistoric + 10; $i++){
            $arrayProv[0] = $sentenece[$i];
            $arrayProv[1] = $this->HistoricInterface->getPictosFolder($sentenece[$i]->ID_SSentence, $idusu);
            array_push($sentenceArray,$arrayProv);
        }
        $count = $this->HistoricInterface->getCountSentenceFolder($idusu, $idfolder);

        $response = [
            'historic' => $sentenceArray,
            'count' => $count
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function getHistorialState_post() {
        
        $idusu = $this->post('idusu');
        
        $state = $this->HistoricInterface->getHistorialState($idusu);
        $response = [
            'state' => $state
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function changeHistorialState_post() {
        $newstate = $this->post('newState');
        $idusu = $this->post('idusu');
        $prevstate = $this->HistoricInterface->getHistorialState($idusu);
        $this->HistoricInterface->changeHistorialState($newstate, $idusu);
        if ($newstate != $prevstate) {
            $this->HistoricInterface->deleteHistoric($idusu);
        }
        $this->response(null, REST_Controller::HTTP_OK);
    }
    
    /*
     * NOT IN USE
     */
    public function deleteHistoric_post(){
        $idusu = $this->post('idusu');
        $this->HistoricInterface->deleteHistoric($idusu);
    }
    
}
