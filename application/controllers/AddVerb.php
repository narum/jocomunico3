<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class AddVerb extends REST_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('AddVerbModel');
        $this->load->model('PanelInterface');
        $this->load->model('Lexicon');
        $this->load->model('BoardInterface');
        $this->load->model('AddWordInterface');
    }

    public function getConjugations_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $verb = $request->verb;
        $result = $this->AddVerbModel->conjugateVerb($verb);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function verbExist_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $verb = $request->verb;
        $result = $this->AddVerbModel->verbExist($verb);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function insertData_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $result = $this->AddVerbModel->insertData($request);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function getAllData_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $verbID = $request->verbID;
        $result = $this->AddVerbModel->getAllData($verbID);
        $this->response($result, REST_Controller::HTTP_OK);
    }

}
