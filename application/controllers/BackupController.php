<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class BackupController extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("BackupInserts");
        $this->load->model("RecoverBackup");
        $this->load->model("BackupClean");
    }
    //crea la carpeta para los backups y todos sus contenidos
public function index_get(){
  
  $data=$this->BackupInserts->createBackupFolder();
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}

public function dobackup_post(){
    
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $idsu = $request->idsu;
    $idusu = $request->idusu;
    $langid = $request->langid;
  
  $data=$this->BackupInserts->createBackupFolder($idsu, $idusu, $langid);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}


public function getkeycounts_post(){
    
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $idusu = $request->idusu;
    
  $data=$this->RecoverBackup->getKeyCounts($idusu);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}
//recupera las imagenes y las inserta en la nueva base de datos
public function recimages_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");

  $idusu = $this->post("idusu");
  
  if($overwrite) $this->BackupClean->LaunchParcialClean_images($idusu);
  
  $data=$this->RecoverBackup->LaunchParcialRecover_images($Fname, $idusu);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}
public function recpictos_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");
  $idusu = $this->post("idusu");
  $langid = $this->post("langid");
  $keycounts=$this->post("keycounts");
    
  if($overwrite) $this->BackupClean->LaunchParcialClean_Pictograms($idusu, $langid);

  $data=$this->RecoverBackup->LaunchParcialRecover_Pictograms($Fname, $overwrite, $idusu, $langid, $keycounts["pcont"]);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}
//recupera el vocabulario y las inserta en la nueva base de datos
public function recvocabulary_post(){
  $overwrite=false;
  $Fname=$this->post("folder");
  $idusu = $this->post("idusu");
  $langid = $this->post("langid");
  $keycounts=$this->post("keycounts");
  
  $data=$this->RecoverBackup->LaunchParcialRecover_Pictograms($Fname, $overwrite, $idusu, $langid, $keycounts["pcont"]);
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
  $idusu = $this->post("idusu");
  
  if($overwrite) $this->BackupClean->LaunchParcialClean_Folder($idusu);
  
  $data=$this->RecoverBackup->LaunchParcialRecover_Folder($Fname,$keycounts["fcont"],$keycounts["scont"],$keycounts["hcont"], $overwrite, $idusu);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}

//recupera las cfg y las inserta en la nueva base de datos
public function reccfg_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");
  $idusu = $this->post("idusu");
  $idsu = $this->post("idsu");
  
  $this->RecoverBackup->LaunchParcialRecover_cfg($overwrite,$Fname,$idsu,$idusu);
  $response = [
      'data' => $Fname
  ];
}
//recupera las panels y las inserta en la nueva base de datos
public function recpanels_post(){
  $overwrite=$this->post('overwrite');
  $Fname=$this->post("folder");
  $keycounts=$this->post("keycounts");
  $idusu = $this->post("idusu");
  
  if($overwrite){
    $mainGboard=true;
    $this->BackupClean->LaunchParcialClean_panels($idusu);
  }else{
    $mainGboard=false;
  }
  
  $data=$this->RecoverBackup->LaunchParcialRecover_panels($mainGboard,$Fname, $keycounts["gbcont"], $keycounts["bcont"], $keycounts["scont"], $keycounts["fcont"], $keycounts["pcont"], $overwrite, $idusu);
  $response = [
      'data' => $data
  ];
  $this->response($response, REST_Controller::HTTP_OK);
}

// NOT IN USE
public function recbackup_post(){
    
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $idsu = $request->idsu;
    $idusu = $request->idusu;
    $langid = $request->langid;
    
  $this->BackupClean->LaunchClean($idusu, $langid);
  
  $data=$this->RecoverBackup->LaunchTotalRecover($idsu, $idusu, $langid);
  $response = [
      'data' => $data
  ];
  $this->response($data, REST_Controller::HTTP_OK);
}
}
