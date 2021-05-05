<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class WordDocument extends REST_Controller {

  public function __construct(){
    parent::__construct('rest', TRUE);
    $this->load->model('WordDocument_Model'); 
  }

  public function index_post(){
    //Response WordDocument Path
      
    $aux = $this->WordDocument_Model->getWordDocument($this->post('sentences'));
    $response = [
        "documentPath" => $aux
      ];
      
    $this->response(
      $response, REST_Controller::HTTP_OK);
  }

}
