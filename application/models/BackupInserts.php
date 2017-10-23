<?php
/*
El codigo es un 95% parsear los datos de backupselects en json
la ultima funcion es la que coge las imagenes y las guarda en una carpeta.
*/

class BackupInserts extends CI_Model{
    
    var $isWin = false;
    
  function __construct(){
      parent::__construct();
      $this->load->library('session');
      $this->load->library('zip');
      $this->load->model("BackupSelects");
      $this->load->helper('url');
      
      $this->isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
  }
  /*Este es el metodo principal, aqui se llama a todos los metodos privados
  pero antes claro crea una carpeta con la fecha y el id de usuario precision segundos
  */
  public function createBackupFolder(){
  $F = date("d_m_Y_H_i_s");
  $Fname = "";
    
  if ($this->isWin) {
      $Fname = "C:\\xampp\\htdocs\\backups\\".$F;
  }
  else {
      $Fname = "./backups/".$F;      
  }
  
  mkdir($Fname, 0777);
  
  $this->generateAdjectivesClassJson($Fname);
  $this->generateAdjectivesJson($Fname);
  $this->generateBoardsJson($Fname);
  $this->generateCellJson($Fname);
  $this->generateGroupBoardsJson($Fname);
  $this->generateImagesJson($Fname);
  $this->generateNameJson($Fname);
  $this->generateNameClassJson($Fname);
  $this->generateVerbJson($Fname);
  $this->generateVerbConjugationJson($Fname);
  $this->generatePatternJson($Fname);
  $this->generatePictogramsJson($Fname);
  $this->generatePictogramsLanguageJson($Fname);
  $this->generateRBoardCellJson($Fname);
  $this->generateRSHistoricPictogramsJson($Fname);
  $this->generateRSSentencePictogramsJson($Fname);
  $this->generateSuperUserJson($Fname);
  $this->generateSHistoricJson($Fname);
  $this->generateSFolderJson($Fname);
  $this->generateSSentenceJson($Fname);
  $this->generateUserJson($Fname);
  $this->setImagesOnBackup($Fname);
  return $F;
}
//Genera json de la tabla AdjectiveClass
  private function generateAdjectivesClassJson($Fname){
    $data=$this->BackupSelects->getAdjectives();
    $ID_Language=$this->session->uinterfacelangauge;
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
  private function generateAdjectivesJson($Fname){
    $data=$this->BackupSelects->getAdjectives();
    $ID_Language=$this->session->uinterfacelangauge;
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
  private function generateBoardsJson($Fname){
    $data=$this->BackupSelects->getBoards();
    $fp = fopen($Fname.'/Boards.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  return $data;
  }
  //Genera json de la tabla cell
  private function generateCellJson($Fname){
    $data=$this->BackupSelects->getCell();
    $fp = fopen($Fname.'/Cell.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla GroupBoards
  private function generateGroupBoardsJson($Fname){
    $data=$this->BackupSelects->getGroupBoards();
      $fp = fopen($Fname.'/GroupBoards.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla Images
  private function generateImagesJson($Fname){
    $data=$this->BackupSelects->getImages();
      $fp = fopen($Fname.'/Images.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla Names
  private function generateNameJson($Fname){
    $data=$this->BackupSelects->getNames();
    $ID_Language=$this->session->uinterfacelangauge;
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
  private function generateVerbJson($Fname){
        $data=$this->BackupSelects->getVerbs();
        $ID_Language=$this->session->uinterfacelangauge;
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
  private function generateVerbConjugationJson($Fname){
        $data=$this->BackupSelects->getVerbs();
        $ID_Language=$this->session->uinterfacelangauge;
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
  private function generatePatternJson($Fname){
        $data=$this->BackupSelects->getVerbs();
        $ID_Language=$this->session->uinterfacelangauge;
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
  private function generateSHistoricJson($Fname){
    $data=$this->BackupSelects->getHistoric();
      $fp = fopen($Fname.'/S_Historic.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla NameClass
  private function generateNameClassJson($Fname){
    $data=$this->BackupSelects->getNames();
    $ID_Language=$this->session->uinterfacelangauge;
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
  private function generatePictogramsJson($Fname){
    $data=$this->BackupSelects->getPictograms();
      $fp = fopen($Fname.'/Pictograms.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla PictogramsLanguage
  private function generatePictogramsLanguageJson($Fname){
    $data=$this->BackupSelects->getPictogramsLanguage();
      $fp = fopen($Fname.'/PictogramsLanguage.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla R_BoardCell
  private function generateRBoardCellJson($Fname){
    $data=$this->BackupSelects->getRBoardCell();
      $fp = fopen($Fname.'/R_BoardCell.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla R_S_HistoricPictograms
  private function generateRSHistoricPictogramsJson($Fname){
    $data=$this->BackupSelects->getRSHistoricPictograms();
      $fp = fopen($Fname.'/R_S_HistoricPictograms.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla R_S_SentencePictograms
  private function generateRSSentencePictogramsJson($Fname){
    $data=$this->BackupSelects->getRSSentecePictograms();
      $fp = fopen($Fname.'/R_S_SentencePictograms.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla SuperUser
  private function generateSuperUserJson($Fname){
    $data=$this->BackupSelects->getSuperUser();
    $fp = fopen($Fname.'/SuperUser.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla S_Folder
  private function generateSFolderJson($Fname){
    $data=$this->BackupSelects->getSFolder();
      $fp = fopen($Fname.'/S_Folder.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla S_Sentence
  private function generateSSentenceJson($Fname){
    $data=$this->BackupSelects->getSSentence();
      $fp = fopen($Fname.'/S_Sentence.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  }
  //Genera json de la tabla User
  private function generateUserJson($Fname){
    $data=$this->BackupSelects->getUser();
      $fp = fopen($Fname.'/User.json', 'w');
  fwrite($fp, json_encode($data));
  fclose($fp);
  return $fp;
  }
  //Hace el backup de las imagenes moviendo el contenido de una carpeta a la de backup
  private function setImagesOnBackup($Fname){
    mkdir("$Fname/images");
  $data=$this->BackupSelects->getImages();
  $imgName=$data['imgName'];
  $imgPath=$data['imgPath'];
  for($i=0;$i<count($imgPath);$i++){
      copy($imgPath[$i],$Fname.'/'.'images/'.$imgName[$i]);
}
}
  }
  ?>
