<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Creates ZIP file from json file contents and downloads the backup for Unix systems

class DownloadBackup extends CI_Controller {

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
      $Verbtable=file_get_contents($Fname."/VerbCA.json");
      $Verbconjugation=file_get_contents($Fname."/VerbConjugationCA.json");
      $Pattern=file_get_contents($Fname."/PatternCA.json");
      $Verb_verb="VerbCA.json";
      $Verbconjugation_verb="VerbConjugationCA.json";
      $Pattern_verb="PatternCA.json";
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
      $Verbtable=file_get_contents($Fname."/VerbES.json");
      $Verbconjugation=file_get_contents($Fname."/VerbConjugationES.json");
      $Pattern=file_get_contents($Fname."/PatternES.json");
      $Verb_verb="VerbES.json";
      $Verbconjugation_verb="VerbConjugationES.json";
      $Pattern_verb="PatternES.json";
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
    $Version = file_get_contents($Fname."/Version.txt");

    $backup=array(
    $Adjtable,
    $Adjclass,
    $Nametable,
    $Nameclass,
    $Verbtable,
    $Verbconjugation,
    $Pattern,
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
    $User,
    $Version);

    $Filenames=array(
    $Adj_name,
    $Adjclass_name,
    $Name_name,
    $Nameclass_name,
    $Verb_verb,
    $Verbconjugation_verb,
    $Pattern_verb,
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
    'User.json',
    'Version.txt');

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

  // Download the file to your desktop. Name it "my_backup.zip"
  $this->zip->download($Fname.'.zip');
  }
}
