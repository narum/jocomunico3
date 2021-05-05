<?php
/*
El codigo es un 95% parsear los datos de backupselects en json
la ultima funcion es la que coge las imagenes y las guarda en una carpeta.
*/

// It calls BackupSelects Model to get all data from all database tables

class BackupInserts extends CI_Model{
    
    var $isWin = false;
    
  function __construct(){
      parent::__construct();
      $this->load->library('zip');
      $this->load->model("BackupSelects");
      $this->load->helper('url');
      
      $this->isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
  }
  /*Este es el metodo principal, aqui se llama a todos los metodos privados
  pero antes claro crea una carpeta con la fecha y el id de usuario precision segundos
  */
  public function createBackupFolder($idsu, $idusu, $langid){
  $F = date("d_m_Y_H_i_s");
  $Fname = "";
    
  if ($this->isWin) {
      $Fname = "C:\\xampp\\htdocs\\backups\\".$F;
  }
  else {
      $Fname = "./backups/".$F;      
  }
  
  mkdir($Fname, 0777);
  
  $this->generateAdjectivesClassJson($Fname, $idusu, $langid);
  $this->generateAdjectivesJson($Fname, $idusu, $langid);
  $this->generateBoardsJson($Fname, $idusu);
  $this->generateCellJson($Fname, $idusu);
  $this->generateGroupBoardsJson($Fname, $idusu);
  $this->generateImagesJson($Fname, $idusu);
  $this->generateNameJson($Fname, $idusu, $langid);
  $this->generateNameClassJson($Fname, $idusu, $langid);
  $this->generateVerbJson($Fname, $idusu, $langid);
  $this->generateVerbConjugationJson($Fname, $idusu, $langid);
  $this->generatePatternJson($Fname, $idusu, $langid);
  $this->generatePictogramsJson($Fname, $idusu);
  $this->generatePictogramsLanguageJson($Fname, $idusu);
  $this->generateRBoardCellJson($Fname, $idusu);
  $this->generateRSHistoricPictogramsJson($Fname, $idusu);
  $this->generateRSSentencePictogramsJson($Fname, $idusu);
  $this->generateSuperUserJson($Fname, $idsu);
  $this->generateSHistoricJson($Fname, $idusu);
  $this->generateSFolderJson($Fname, $idusu);
  $this->generateSSentenceJson($Fname, $idusu);
  $this->generateUserJson($Fname, $idsu);
  $this->setImagesOnBackup($Fname, $idusu);
  $this->generateVersion($Fname);
  return $F;
}
//Genera json de la tabla AdjectiveClass
  private function generateAdjectivesClassJson($Fname, $idusu, $ID_Language){
    $data=$this->BackupSelects->getAdjectives($idusu, $ID_Language);

    switch($ID_Language){
      case 1:
      $table="AdjClassCA";
      break;
      case 2:
      $table="AdjClassES";
      break;
    }
    $Classdata=array(
      'adjid'=>$data['adjid2'],
      'class'=>$data['class']
    );
    $fp = fopen($Fname.'/'.$table.'.json', 'w');
  fwrite($fp, json_encode($Classdata));
  fclose($fp);
  }
  //Genera json de la tabla Adjectives
  private function generateAdjectivesJson($Fname, $idusu, $ID_Language){
    $data=$this->BackupSelects->getAdjectives($idusu, $ID_Language);
    switch($ID_Language){
      case 1:
      $table="AdjectiveCA";
      break;
      case 2:
      $table="AdjectiveES";
      break;
    }
    $Adjdata=array(
        'adjid'=>$data['adjid'],
        'masc'=>$data['masc'],
        'fem'=>$data['fem'],
        'mascpl'=>$data['mascpl'],
        'fempl'=>$data['fempl'],
        'defaultverb'=>$data['defaultverb'],
        'subjdef'=>$data['subjdef'],
        'pictoid'=>$data['pictoid']
    );
    $fp = fopen($Fname.'/'.$table.'.json', 'w');
  fwrite($fp, json_encode($Adjdata));
  fclose($fp);
  }
  //Genera json de la tabla Boards
  private function generateBoardsJson($Fname, $idusu){
    $data=$this->BackupSelects->getBoards($idusu);
    $fp = fopen($Fname.'/Boards.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  return $data;
  }
  //Genera json de la tabla cell
  private function generateCellJson($Fname, $idusu){
    $data=$this->BackupSelects->getCell($idusu);
    $fp = fopen($Fname.'/Cell.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla GroupBoards
  private function generateGroupBoardsJson($Fname, $idusu){
    $data=$this->BackupSelects->getGroupBoards($idusu);
      $fp = fopen($Fname.'/GroupBoards.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla Images
  private function generateImagesJson($Fname, $idusu){
    $data=$this->BackupSelects->getImages($idusu);
      $fp = fopen($Fname.'/Images.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla Names
  private function generateNameJson($Fname, $idusu, $ID_Language){
    $data=$this->BackupSelects->getNames($idusu, $ID_Language);
    switch($ID_Language){
      case 1:
      $table="NameCA";
      break;
      case 2:
      $table="NameES";
      break;
    }
  $Namedata=array(
    'nomtext'=>$data['nomtext'],
    'mf'=>$data['mf'],
    'singpl'=>$data['singpl'],
    'contabincontab'=>$data['contabincontab'],
    'defaultverb'=>$data['defaultverb'],
    'determinat'=>$data['determinat'],
    'ispropernoun'=>$data['ispropernoun'],
    'plural'=>$data['plural'],
    'femeni'=>$data['femeni'],
    'fempl'=>$data['fempl'],
    'nameid'=>$data['pictoid']
  );
      $fp = fopen($Fname.'/'.$table.'.json', 'w');
  fwrite($fp, json_encode($Namedata));
  fclose($fp);
  }
  //Genera json de la tabla Verb
  private function generateVerbJson($Fname, $idusu, $ID_Language){
        $data=$this->BackupSelects->getVerbs($idusu, $ID_Language);
        switch($ID_Language){
            case 1:
            $table="VerbCA";
            break;
            case 2:
            $table="VerbES";
            break;
        }
        $Verbdata=array(
            'verbid'=>$data['pictoid'],
            'verbtext'=>$data['verbtext'],
            'actiu'=>$data['actiu']
        );
        $fp = fopen($Fname.'/'.$table.'.json', 'w');
        fwrite($fp, json_encode($Verbdata));
        fclose($fp);
  }
  //Genera json de la tabla VerbConjugation
  private function generateVerbConjugationJson($Fname, $idusu, $ID_Language){
        $data=$this->BackupSelects->getVerbs($idusu, $ID_Language);
        switch($ID_Language){
            case 1:
            $table="VerbConjugationCA";
            break;
            case 2:
            $table="VerbConjugationES";
            break;
        }
        $Verbdata=array(
            'verbid'=>$data['verbid1'],
            'tense'=>$data['tense'],
            'pers'=>$data['pers'],
            'singpl'=>$data['singpl'],
            'verbconj'=>$data['verbconj']
        );
        $fp = fopen($Fname.'/'.$table.'.json', 'w');
        fwrite($fp, json_encode($Verbdata));
        fclose($fp);
  }
  //Genera json de la tabla Pattern
  private function generatePatternJson($Fname, $idusu, $ID_Language){
        $data=$this->BackupSelects->getVerbs($idusu, $ID_Language);
        switch($ID_Language){
            case 1:
            $table="PatternCA";
            break;
            case 2:
            $table="PatternES";
            break;
        }
        $Verbdata=array(
            'patternid'=>$data['patternid'],
            'verbid'=>$data['verbid2'],
            'pronominal'=>$data['pronominal'],
            'pseudoimpersonal'=>$data['pseudoimpersonal'],
            'copulatiu'=>$data['copulatiu'],
            'tipusfrase'=>$data['tipusfrase'],
            'defaulttense'=>$data['defaulttense'],
            'verbpeticio'=>$data['verbpeticio'],
            'subj'=>$data['subj'],
            'subjdef'=>$data['subjdef'],
            'theme'=>$data['theme'],
            'themetipus'=>$data['themetipus'],
            'themedef'=>$data['themedef'],
            'themeprep'=>$data['themeprep'],
            'themeart'=>$data['themeart'],
            'receiver'=>$data['receiver'],
            'receiverdef'=>$data['receiverdef'],
            'receiverprep'=>$data['receiverprep'],
            'benef'=>$data['benef'],
            'beneftipus'=>$data['beneftipus'],
            'benefdef'=>$data['benefdef'],
            'benefprep'=>$data['benefprep'],
            'acomp'=>$data['acomp'],
            'acompdef'=>$data['acompdef'],
            'acompprep'=>$data['acompprep'],
            'tool'=>$data['tool'],
            'tooldef'=>$data['tooldef'],
            'toolprep'=>$data['toolprep'],
            'manera'=>$data['manera'],
            'maneradef'=>$data['maneradef'],
            'maneratipus'=>$data['maneratipus'],
            'locto'=>$data['locto'],
            'loctotipus'=>$data['loctotipus'],
            'loctodef'=>$data['loctodef'],
            'loctoprep'=>$data['loctoprep'],
            'locfrom'=>$data['locfrom'],
            'locfromtipus'=>$data['locfromtipus'],
            'locfromdef'=>$data['locfromdef'],
            'locfromprep'=>$data['locfromprep'],
            'locat'=>$data['locat'],
            'locatdef'=>$data['locatdef'],
            'locatprep'=>$data['locatprep'],
            'time'=>$data['time'],
            'expressio'=>$data['expressio'],
            'subverb'=>$data['subverb'],
            'exemple'=>$data['exemple']
        );
        $fp = fopen($Fname.'/'.$table.'.json', 'w');
        fwrite($fp, json_encode($Verbdata));
        fclose($fp);
  }
  //Genera json de la tabla S_Historic
  private function generateSHistoricJson($Fname, $idusu){
    $data=$this->BackupSelects->getHistoric($idusu);
      $fp = fopen($Fname.'/S_Historic.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla NameClass
  private function generateNameClassJson($Fname, $idusu, $ID_Language){
    $data=$this->BackupSelects->getNames($idusu, $ID_Language);
    switch($ID_Language){
      case 1:
      $table="NameClassCA";
      break;
      case 2:
      $table="NameClassES";
      break;
    }
  $Classdata=array(
    'class'=>$data['class'],
    'nameid'=>$data['nameid']
  );
    $fp = fopen($Fname.'/'.$table.'.json', 'w');
  fwrite($fp, json_encode($Classdata));
  fclose($fp);
  }
  //Genera json de la tabla Pictograms
  private function generatePictogramsJson($Fname, $idusu){
    $data=$this->BackupSelects->getPictograms($idusu);
      $fp = fopen($Fname.'/Pictograms.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla PictogramsLanguage
  private function generatePictogramsLanguageJson($Fname, $idusu){
    $data=$this->BackupSelects->getPictogramsLanguage($idusu);
      $fp = fopen($Fname.'/PictogramsLanguage.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla R_BoardCell
  private function generateRBoardCellJson($Fname, $idusu){
    $data=$this->BackupSelects->getRBoardCell($idusu);
      $fp = fopen($Fname.'/R_BoardCell.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla R_S_HistoricPictograms
  private function generateRSHistoricPictogramsJson($Fname, $idusu){
    $data=$this->BackupSelects->getRSHistoricPictograms($idusu);
      $fp = fopen($Fname.'/R_S_HistoricPictograms.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla R_S_SentencePictograms
  private function generateRSSentencePictogramsJson($Fname, $idusu){
    $data=$this->BackupSelects->getRSSentecePictograms($idusu);
      $fp = fopen($Fname.'/R_S_SentencePictograms.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla SuperUser
  private function generateSuperUserJson($Fname, $idsu){
    $data=$this->BackupSelects->getSuperUser($idsu);
    $fp = fopen($Fname.'/SuperUser.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla S_Folder
  private function generateSFolderJson($Fname, $idusu){
    $data=$this->BackupSelects->getSFolder($idusu);
      $fp = fopen($Fname.'/S_Folder.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla S_Sentence
  private function generateSSentenceJson($Fname, $idusu){
    $data=$this->BackupSelects->getSSentence($idusu);
      $fp = fopen($Fname.'/S_Sentence.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla User
  private function generateUserJson($Fname, $idsu){
    $data=$this->BackupSelects->getUser($idsu);
      $fp = fopen($Fname.'/User.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  return $fp;
  }
  //Hace el backup de las imagenes moviendo el contenido de una carpeta a la de backup
  private function setImagesOnBackup($Fname, $idusu){
    mkdir("$Fname/images");
  $data=$this->BackupSelects->getImages($idusu);
  $imgName=$data['imgName'];
  $imgPath=$data['imgPath'];
  for($i=0;$i<count($imgPath);$i++){
      copy($imgPath[$i],$Fname.'/'.'images/'.$imgName[$i]);
}
}

  //Genera json de la tabla AdjectiveClass
  private function generateVersion($Fname){

    $fp = fopen($Fname.'/Version.txt', 'w');
  fwrite($fp, "Jocomunico 3.0");
  fclose($fp);
  }

  }
  ?>
