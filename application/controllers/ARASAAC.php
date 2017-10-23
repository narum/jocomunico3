<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ARASAAC extends REST_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('ARASAAC_model');
    }

    
    public function search_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $imgName = $request->name;
        $searchType = $request->type;
        $images = $this->ARASAAC_model->getServerImages($imgName, $searchType);

        $response = [
            "data" => $images
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }
    
    public function upload_post() {
        $idsu = $this->session->userdata('idsu');
        
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $imgName = $request->name;
        $imgPath = $request->path;
        $status = $this->ARASAAC_model->downloadServerImage($idsu, $imgPath, $imgName);

        $response = [
            "status" => $status
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }
    
    public function searchDelete_post() {
        $idsu = $this->session->userdata('idsu');
        
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $imgName = $request->name;
        $images = $this->ARASAAC_model->getUserImages($idsu, $imgName);

        $response = [
            "data" => $images
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }
    
    public function deleteImg_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $imgId = $request->id;
        $imgPath = $request->path;
        $status = $this->ARASAAC_model->deleteImage($imgId, $imgPath);

        $response = [
            "status" => $status
        ];

        $this->response($response, REST_Controller::HTTP_OK);
    }
    
}
