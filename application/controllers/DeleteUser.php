<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class DeleteUser extends REST_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('DeleteUserModel');
        $this->load->model('BackupClean');
    }

    public function deleteUser_get() {
        
        $this->BackupClean->LaunchClean();
        $result = $this->DeleteUserModel->deleteUserBD();
        $response = [
            'result' => $result
        ];
        $response = $result;
        $this->response($response, REST_Controller::HTTP_OK);
    }

}
