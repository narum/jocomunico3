<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class AddWord extends REST_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('panelInterface');
        $this->load->model('BoardInterface');
        $this->load->model('main_model');
        $this->load->model('AddWordInterface');
        $this->load->model('InsertVocabulari');
    }

    // NOT IN USE ANYMORE
    public function index_get() {
        // CHECK COOKIES
        if (!$this->session->userdata('uname')) {
            redirect(base_url(), 'location');
        } else {
            if (!$this->session->userdata('cfguser')) {
                $this->BoardInterface->loadCFG();
                $this->load->view('MainBoard', true);
            } else {
                $this->load->view('MainBoard', true);
            }
        }
    }

    private static function cmp($a, $b) {
        $a = strtolower($a['text']);
        $b = strtolower($b['text']);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }
        private static function cmpclass($a, $b) {
        $a = strtolower($a['class']);
        $b = strtolower($b['class']);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    public function EditWordRemove_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idPicto = $request->id;
        $type = $request->type;
        $user = $request->idusu;
        $langid = $request->langid;
        
        $this->InsertVocabulari->deletePictogram($idPicto, $type, $user, $langid);

        $response = [];

        $this->response($response, REST_Controller::HTTP_OK);
    }
    public function EditWordType_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idPicto = $request->id;
        $Type = $this->AddWordInterface->getTypePicto($idPicto);

        $response = [
            "data" => $Type
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }
    public function EditWordGetData_post(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idPicto = $request->id;
        $type = $request->type;
        $idusu = $request->idusu;
        $langabbr = $request->langabbr;
        
        switch($type){
            case('name'):
                $data = $this->AddWordInterface->EditWordNoms($idPicto, $idusu, $langabbr);
                break;
            case('adj'):
                $data = $this->AddWordInterface->EditWordAdj($idPicto, $idusu, $langabbr);
                break;
        }
        $response = [
            "data" => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }
    public function EditWordGetClass_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idPicto = $request->id;
        $type = $request->type;
        $langabbr = $request->langabbr;
        
        switch ($type) {
            case('name'):
                $data = $this->AddWordInterface->getDBClassNames($idPicto, $langabbr);
                break;
            case('adj'):
                $data = $this->AddWordInterface->getDBClassAdj($idPicto, $langabbr);
                break;
        }
        usort($data, array('SearchWord', 'cmpclass'));

        $response = [
            "data" => $data
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function InsertWordData_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $objAdd = $request->objAdd;
        $user = $request->idusu;
        $language = $request->langabbr;
        
        $this->InsertVocabulari->insertPicto($objAdd, $user, $language);
    }

    public function getAllVerbs_post(){

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $user = $request->idusu;
        $language = $request->langabbr; // Expansion Abbr
        $idlang = $request->idlang; // Interface

        $Verbs = $this->AddWordInterface->getDBVerbs($user, $language, $idlang);

        $response = [
            "data" => $Verbs
        ];
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code


    }

    public function getDBAll_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr; // Expansion Abbr
        $idlang = $request->idlang; // Interface


        // Controller search all names from all picto table
        $Names = $this->AddWordInterface->getDBNamesLike($startswith, $user, $language, $idlang);
        $Verbs = $this->AddWordInterface->getDBVerbsLike($startswith, $user, $language, $idlang);
        $Adj = $this->AddWordInterface->getDBAdjLike($startswith, $user, $language, $idlang);
        $Exprs = $this->AddWordInterface->getDBExprsLike($startswith, $user, $language, $idlang);
        $Advs = $this->AddWordInterface->getDBAdvsLike($startswith, $user, $language, $idlang);
        $Modifs = $this->AddWordInterface->getDBModifsLike($startswith, $user, $language, $idlang);
        $QuestionPart = $this->AddWordInterface->getDBQuestionPartLike($startswith, $user, $language, $idlang);

        // Marge all arrays to one
        $DataArray = array_merge($Names, $Verbs, $Adj, $Exprs, $Advs, $Modifs, $QuestionPart);

        usort($DataArray, array('SearchWord', 'cmp'));

        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBNames_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr; // Expansion Abbr
        $idlang = $request->idlang; // Interface
        
        // Controller search all names from all picto table
        $DataArray = $this->AddWordInterface->getDBNamesLike($startswith, $user, $language, $idlang);
        usort($DataArray, array('SearchWord', 'cmp'));
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBVerbs_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr; // Expansion Abbr
        $idlang = $request->idlang; // Interface

        // Controller search all names from all picto table
        $DataArray = $this->AddWordInterface->getDBVerbsLike($startswith, $user, $language, $idlang);
        usort($DataArray, array('SearchWord', 'cmp'));
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBAdj_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr; // Expansion Abbr
        $idlang = $request->idlang; // Interface

        // Controller search all names from all picto table
        $DataArray = $this->AddWordInterface->getDBAdjLike($startswith, $user, $language, $idlang);
        usort($DataArray, array('SearchWord', 'cmp'));
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBExprs_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr; // Expansion Abbr
        $idlang = $request->idlang; // Interface

        // Controller search all names from all picto table
        $DataArray = $this->AddWordInterface->getDBExprsLike($startswith, $user, $language, $idlang);
        usort($DataArray, array('SearchWord', 'cmp'));
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBOthers_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr; // Expansion Abbr
        $idlang = $request->idlang; // Interface

        // Controller search all names from all picto table
        $Advs = $this->AddWordInterface->getDBAdvsLike($startswith, $user, $language, $idlang);
        $Modifs = $this->AddWordInterface->getDBModifsLike($startswith, $user, $language, $idlang);
        $QuestionPart = $this->AddWordInterface->getDBQuestionPartLike($startswith, $user, $language, $idlang);

        $DataArray = array_merge($Advs, $Modifs, $QuestionPart);
        usort($DataArray, array('SearchWord', 'cmp'));
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function EditWordSelect_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $ID_GBoard = $request->idGroupBoard;

        $primaryBoard = $this->BoardInterface->getInfoGroupBoard($ID_GBoard);

        $response = [
            'ID_GB' => $primaryBoard[0]->ID_GB,
            'ID_GBUser' => $primaryBoard[0]->ID_GBUser,
            'GBname' => $primaryBoard[0]->GBname,
            'primaryGroupBoard' => $primaryBoard[0]->primaryGroupBoard,
            'defWidth' => $primaryBoard[0]->defWidth,
            'defHeight' => $primaryBoard[0]->defHeight,
            'imgGB' => $primaryBoard[0]->imgGB
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }

    public function GetWordSelect_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $ID_GBoard = $request->idGroupBoard;

        $primaryBoard = $this->BoardInterface->getInfoGroupBoard($ID_GBoard);

        $response = [
            'ID_GB' => $primaryBoard[0]->ID_GB,
            'ID_GBUser' => $primaryBoard[0]->ID_GBUser,
            'GBname' => $primaryBoard[0]->GBname,
            'primaryGroupBoard' => $primaryBoard[0]->primaryGroupBoard,
            'defWidth' => $primaryBoard[0]->defWidth,
            'defHeight' => $primaryBoard[0]->defHeight,
            'imgGB' => $primaryBoard[0]->imgGB
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }
    
    // NOT IN USE ANYMORE (USED IN JOCOMUNICO 1.0)
    public function copyUserVocabulary_post() {

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idusu = $request->user;

        $this->BoardInterface->initTrans();
        $idusuorigen = $this->session->userdata('idusu');
        $vocabulary = $this->AddWordInterface->copyVocabulary($idusuorigen,$idusu);
        $this->BoardInterface->commitTrans();
        $response = [
            "data" => $vocabulary
        ];
        $this->response($response, REST_Controller::HTTP_OK);
    }
}
