<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class BackupController extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("BackupInserts");
        $this->load->model("RecoverBackup");
        $this->load->model("Main_model");
        $this->load->model("BackupClean");
        $this->load->model('BoardInterface');
        $this->load->library('session');
    }
    //crea la carpeta para los backups
public function index_get(){
  
  $data=$this->BackupInserts->createBackupFolder();
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}


public function getkeycounts_get(){
  $data=$this->RecoverBackup->getKeyCounts();
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}
//recupera las imagenes y las inserta en la nueva base de datos
public function recimages_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");
  if($overwrite) $this->BackupClean->LaunchParcialClean_images();
  
  $data=$this->RecoverBackup->LaunchParcialRecover_images($Fname);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}
public function recpictos_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");
  if($overwrite) $this->BackupClean->LaunchParcialClean_Pictograms();

  $data=$this->RecoverBackup->LaunchParcialRecover_Pictograms($Fname, $overwrite);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}
//recupera el vocabulario y las inserta en la nueva base de datos
public function recvocabulary_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");
  
  $data=$this->RecoverBackup->LaunchParcialRecover_Pictograms($Fname, $overwrite);
  $response = [
      'data' => $data
  ];
   $this->response($response, REST_Controller::HTTP_OK);
}

//recupera las folder y las inserta en la nueva base de datos
public function recfolder_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");
  $keycounts=$this->post("keycounts");
  
  if($overwrite) $this->BackupClean->LaunchParcialClean_Folder();
  
  $data=$this->RecoverBackup->LaunchParcialRecover_Folder($Fname,$keycounts["fcont"],$keycounts["scont"],$keycounts["hcont"], $overwrite);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}

//recupera las cfg y las inserta en la nueva base de datos
public function reccfg_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");
  
  $this->RecoverBackup->LaunchParcialRecover_cfg($overwrite,$Fname);
  $response = [
      'data' => $Fname
  ];
}
//recupera las panels y las inserta en la nueva base de datos
public function recpanels_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");
  $keycounts=$this->post("keycounts");
  if($overwrite){
    $mainGboard=true;
    $this->BackupClean->LaunchParcialClean_panels();
  }else{
    $mainGboard=false;
  }
  
  $data=$this->RecoverBackup->LaunchParcialRecover_panels($mainGboard,$Fname, $keycounts["gbcont"], $keycounts["bcont"], $keycounts["scont"], $keycounts["fcont"], $keycounts["pcont"], $overwrite);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}
public function recbackup_get(){
  $this->BackupClean->LaunchClean();
  
  $data=$this->RecoverBackup->LaunchTotalRecover();
  $response = [
      'data' => $data
  ];
  $this->response($data, REST_Controller::HTTP_OK);
}
}
