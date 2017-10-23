<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class DownloadBackupWin extends CI_Controller {
  public function __construct(){
      parent::__construct();
      $this->load->library('zip');
  }
  function unzipBackup(){
    $this->load->library('unzip');
  // Optional: Only take out these files, anything else is ignored
$this->unzip->allow(array('css', 'js', 'png', 'gif', 'jpeg', 'jpg', 'tpl', 'html', 'swf'));
// Give it one parameter and it will extract to the same folder
$this->unzip->extract('uploads/my_archive.zip');
// or specify a destination directory
$this->unzip->extract('uploads/my_archive.zip', '/path/to/directory/');
  }
  function backup($name){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      $Fname="/xampp/htdocs/backups/".urldecode($name);
    } else {
      $Fname="./backups/".urldecode($name);
    }
    $ID_Language=$this->session->uinterfacelangauge;
    switch($ID_Language){
      case 1:
      $Adjtable=file_get_contents($Fname."/AdjectiveCA.json");
      $Adjclass=file_get_contents($Fname."/AdjClassCA.json");
      $Adj_name="AdjectiveCA.json";
      $Adjclass_name="AdjClassCA.json";
      $Nametable=file_get_contents($Fname."/NameCA.json");
      $Nameclass=file_get_contents($Fname."/NameClassCA.json");
      $Name_name="NameCA.json";
      $Nameclass_name="NameClassCA.json";
      break;
      case 2:
      $Adjtable=file_get_contents($Fname."/AdjectiveES.json");
      $Adjclass=file_get_contents($Fname."/AdjClassES.json");
      $Adj_name="AdjectiveES.json";
      $Adjclass_name="AdjClassES.json";
      $Nametable=file_get_contents($Fname."/NameES.json");
      $Nameclass=file_get_contents($Fname."/NameClassES.json");
      $Name_name="NameES.json";
      $Nameclass_name="NameClassES.json";
      break;
    }
    $Boards = file_get_contents($Fname."/Boards.json");
    $Cell = file_get_contents($Fname."/Cell.json");
    $GroupBoards = file_get_contents($Fname."/GroupBoards.json");
    $Images = file_get_contents($Fname."/Images.json");
    $Pictograms = file_get_contents($Fname."/Pictograms.json");
    $PictogramsLanguage = file_get_contents($Fname."/PictogramsLanguage.json");
    $R_BoardCell=file_get_contents($Fname."/R_BoardCell.json");
    $R_S_HistoricPictograms = file_get_contents($Fname."/R_S_HistoricPictograms.json");
    $R_S_SentencePictograms = file_get_contents($Fname."/R_S_SentencePictograms.json");
    $S_Folder = file_get_contents($Fname."/S_Folder.json");
    $S_Historic= file_get_contents($Fname."/S_Historic.json");
    $S_Sentence = file_get_contents($Fname."/S_Sentence.json");
    $SuperUser = file_get_contents($Fname."/SuperUser.json");
    $User = file_get_contents($Fname."/User.json");
    $backup=array(
    $Adjtable,
    $Adjclass,
    $Nametable,
    $Nameclass,
    $Boards,
    $Cell,
    $GroupBoards,
    $Images,
    $Pictograms,
    $PictogramsLanguage,
    $R_BoardCell,
    $R_S_HistoricPictograms,
    $R_S_SentencePictograms,
    $S_Historic,
    $S_Folder,
    $S_Sentence,
    $SuperUser,
    $User);
    $Filenames=array(
    $Adj_name,
    $Adjclass_name,
    $Name_name,
    $Nameclass_name,
    'Boards.json',
    'Cell.json',
    'GroupBoards.json',
    'Images.json',
    'Pictograms.json',
    'PictogramsLanguage.json',
    'R_BoardCell.json',
    'R_S_HistoricPictograms.json',
    'R_S_SentencePictograms.json',
    'S_Historic.json',
    'S_Folder.json',
    'S_Sentence.json',
    'SuperUser.json',
    'User.json');
    for($i=0;$i<count($backup);$i++){
      $this->zip->add_data($Filenames[$i],$backup[$i]);
    }
    $this->zip->add_dir('Images');
    $images=json_decode($Images);
    $count=count($images->ID_Image);
    $path=$images->imgPath;
    for($i=0;$i<$count;$i++){
  $this->zip->add_data('Images/' . $path[$i], file_get_contents($path[$i]));
  }
    $this->zip->archive('/path/to/directory/my_backup.zip');
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      $this->zip->download($name.'.zip');
    } else {
      $this->zip->download($Fname.'.zip');
    }
  // Download the file to your desktop. Name it "my_backup.zip"

  }
}
function uploadBackup_post() {
  $errorText = array();
  $ID_User=$this->session->idusu;
  $target_dir="/xampp/htdocs/Temp/";
  $error = false;
  for ($i = 0; $i < count($_FILES); $i++) {
      $md5Name = $this->Rename_Img(basename($_FILES['file' . $i]['name']));
      if (!($_FILES['file' . $i]['type'] == "application/octet-stream")) {
          $errorProv = ["errorImg1", $_FILES['file' . $i]['type']];
          array_push($errorText, $errorProv);
          $error = true;
          continue;
      }
      $handle = fopen($target_dir . $md5Name, "r");
      if (is_resource($handle)) {
          fclose($handle);
          //MODIF: lanzar error
          $errorProv = ["errorImg2", $_FILES['file' . $i]['name']];
          array_push($errorText, $errorProv);
          $error = true;
          continue;
      }
      //MODIF: poner tamaño a 100 kb y tamaño 150 minimo
  //    if ($_FILES['file' . $i]['size'] > 10000) {
          $success = move_uploaded_file($_FILES['file' . $i]['tmp_name'],$target_dir . basename($_FILES['file' . $i]['name']));
    //  }
      if (!$success) {
          $errorProv = ["errorImadsvg2", $_FILES['file' . $i]['name']];
          $max_upload = ini_get('memory_limit');
          array_push($errorText, $max_upload);
          $error = true;
          continue;
      }
         $dir12=substr(substr($_FILES['file' . $i]['name'],0,-4),9)."-".$ID_User;
         mkdir($target_dir.$dir12);
         $this->unzip->extract('/xampp/htdocs/Temp/'.basename($_FILES['file' . $i]['name']),"/xampp/htdocs/Temp/$dir12");
  }
  $response = [
      'url' => $dir12,
      'errorText' => $errorText,
      'error' => $error
  ];

  $this->response($response, REST_Controller::HTTP_OK);
}
