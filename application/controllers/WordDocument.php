<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class WordDocument extends REST_Controller {

  public function __construct(){
    parent::__construct();
    $this->load->model('WordDocument_Model');
  }

  public function index_post(){
    //Response WordDocument Path
    $this->response(
      [
        'documentPath' => $this->WordDocument_Model->getWordDocument($this->post('sentences'))
      ],
      REST_Controller::HTTP_OK);
  }

}
