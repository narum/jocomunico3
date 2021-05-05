<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class DeleteUser extends REST_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('DeleteUserModel');
        $this->load->model('BackupClean');
    }

    public function deleteUser_post() {
        
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $idsu = $request->idsu;
        $idusu = $request->idusu;
        $langid = $request->langid;
        
        $this->BackupClean->LaunchClean($idusu, $langid);
        $result = $this->DeleteUserModel->deleteUserBD($idsu, $idusu);
        
        $response = $result;
        $this->response($response, REST_Controller::HTTP_OK);
    }

}
