<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DeleteTemp extends CI_Controller {

  public function __construct(){
      parent::__construct();
      $this->load->helper('file');
  }

  // Deletes all temporary files
  public function index(){
    delete_files('Temp', TRUE);
    delete_files('backups', TRUE); 
  }
  
}
