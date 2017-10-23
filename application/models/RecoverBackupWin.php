<?php
/*Aqui se produce la recuperacion del backup, hay varias funciones que comprueban si los backups existen,
tambien varias funciones auxiliares al final del fichero las cuales cogen las claves recien insertadas para
evitar colisiones entre claves, en la funcion LaunchTotalRecover es muy importante el orden en el que se ejecutan las
funciones*/
class RecoverBackupWin extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
    }
    function checklang(){
      $Fname=$this->getLastGlobalBackup();
      $ID_Language=$this->session->uinterfacelangauge;
      $file = file_get_contents($Folder."/User.json");
      $us=json_decode($file);
      if($ID_Language==$us->ID_ULanguage)
       return true;
       else return false;
    }
    //llama a la recuperacion parcial de imagenes
    function LaunchParcialRecover_images($Fname){
      $Fname="/xampp/htdocs/Temp/$Fname";
      $a=$this->InsertImages($Fname);
      return $a;
    }
    function LaunchParcialRecover_Pictograms($Fname, $overwrite){
      $Fname="Temp/".$Fname;
      $pcont=count($this->getPictokeys());
      $this->InsertPictograms($Fname);
      $this->InsertAdjectives($Fname,$pcont,$overwrite);
      $this->InsertNames($Fname,$pcont, $overwrite);
      return "sdfasdf";
    }
      //llama a la recuperacion parcial de imagenes
    function LaunchParcialRecover_vocabulary($Fname){
      if(!$this->checkifPictogramsexists()){
      $this->LaunchParcialRecover_Pictograms($Fname);
      }
      $Fname="/xampp/htdocs/Temp/$Fname";
      $this->InsertAdjectives($Fname);
      $a=$this->InsertNames($Fname);
      return $a;
    }
      //llama a la recuperacion parcial de la carpetas tematicas
    function LaunchParcialRecover_Folder($Fname){
      $Fname="/xampp/htdocs/Temp/$Fname";
      $cfold=count($this->getfolderkey());
      $sscont=count($this->getSentencekey());
      $hcont=count($this->getHistorickey());
      $this->InsertSFolder($Fname);
      $this->InsertSSentence($Fname,$cfold);
      $this->InsertSHistoric($Fname);
      $this->InsertRSHistoricPictograms($Fname,$hcont);
      $bla=$this->InsertRSSentencePictograms($Fname,$sscont);
      return $bla;
    }
      //llama a la recuperacion parcial de configuracion
    function LaunchParcialRecover_cfg($ow,$Fname){
      $Fname="/xampp/htdocs/Temp/$Fname";
      $this->UpdateSuperUser($Fname,$ow);
      $this->UpdateUser($Fname);
      return $Fname;
    }
    function checkifPictogramsexists(){
     $exists=true;
       if(count($this->getPictokeys())===0) $exists=false;
     return $exists;
   }

      //llama a la recuperacion parcial de paneles
    function LaunchParcialRecover_panels($mainGboard,$Fname){

      if(!$this->checkifPictogramsexists()){
      $this->LaunchParcialRecover_Pictograms($Fname);
      }
      $Fname="/xampp/htdocs/Temp/$Fname";
      $gbcont=count($this->getGBkeys());
      $bcont=count($this->getBoardkey());
      $pcont=count($this->getPictokeys());
      $this->InsertGroupBoards($Fname,$mainGboard);
      $this->InsertBoards($Fname,$gbcont);
      $this->InsertCells($Fname,$bcont,$scont,$fcont,$pcont);
      return $Fname;
    }
    //Inserta en la base de datos los registros correspondientes a adjectiveClass
private function InsertAdjectivesClass($Folder, $pcont, $overwrite, $backupLanguage){
  $tableBackup = "";
  $tableInsert = "";
  
  switch($backupLanguage){
      case 1:
      $tableBackup="AdjClassCA";
      break;
      case 2:
      $tableBackup="AdjClassES";
      break;
  }
      
$ID_Language=$this->session->ulanguage;

switch($ID_Language){
      case 1:
      $tableInsert="AdjClassCA";
      break;
      case 2:
      $tableInsert="AdjClassES";
      break;
}
  
  $pictokeys=$this->getPictokeys();

$file = file_get_contents($Folder."/".$tableBackup.".json");
$pics=file_get_contents($Folder."/Pictograms.json");
$pic=json_decode($pics);
$adjclass=json_decode($file);
if (!$overwrite) $pictokeys= array_slice($pictokeys, $pcont);
$count=count($adjclass->class);
for($i=0;$i<$count;$i++){
   $posp=array_search($adjclass->pictoid[$i],$pic->pictoid);
$sql="INSERT INTO $tableInsert(adjid, class) VALUES (?, ?)";
$this->db->query($sql,array(
  $pictokeys[$posp],
  $adjclass->class[$i]
));
}
return $pictokey;
}
//Inserta en la base de datos los registros correspondientes a adjectives
private function InsertAdjectives($Folder, $pcont, $overwrite){
$tableBackup = "";
$tableInsert = "";
  
  $fileU = file_get_contents($Folder."/User.json");
  $us=json_decode($fileU);
  $backupLanguage = $us->ID_ULanguage[0];
          
  switch($backupLanguage){
      case 1:
      $tableBackup="AdjectiveCA";
      break;
      case 2:
      $tableBackup="AdjectiveES";
      break;
  }
      
$ID_Language=$this->session->ulanguage;

switch($ID_Language){
      case 1:
      $tableInsert="AdjectiveCA";
      break;
      case 2:
      $tableInsert="AdjectiveES";
      break;
}
  
$pictokeys=$this->getPictokeys();

$file = file_get_contents($Folder."/".$tableBackup.".json");
$pics=file_get_contents($Folder."/Pictograms.json");
$pic=json_decode($pics);
$adj=json_decode($file);
if (!$overwrite) $pictokeys= array_slice($pictokeys, $pcont);
$count=count($adj->adjid);
for($i=0;$i<$count;$i++){
$posp=array_search($adj->pictoid[$i],$pic->pictoid);
$sql="INSERT INTO $tableInsert(adjid,masc,fem,mascpl,fempl,defaultverb,subjdef) VALUES (?,?,?,?,?,?,?)";
$this->db->query($sql,array(
  $pictokeys[$posp],
  $adj->masc[$i],
  $adj->fem[$i],
  $adj->mascpl[$i],
  $adj->fempl[$i],
  $adj->defaultverb[$i],
  $adj->subjdef[$i]
));
}
$this->InsertAdjectivesClass($Folder, $pcont, $overwrite, $backupLanguage);
}
//Inserta en la base de datos los registros correspondientes a boards
private function InsertBoards($Folder,$gbcont, $overwrite){
$gbkeys=$this->getGBkeys();
$file = file_get_contents($Folder."/Boards.json");
$fileGB = file_get_contents($Folder."/GroupBoards.json");
$boards=json_decode($file);
$GB=json_decode($fileGB);
$count=count($boards->ID_Board);
if (!$overwrite) $gbkeys=array_slice($gbkeys,$gbcont);
for($i=0;$i<$count;$i++){
 if(!(is_null($boards->ID_GBBoard[$i]))){
     $posc=array_search($boards->ID_GBBoard[$i],$GB->ID_GB);
 }else{
     $posc=null;
 }
$sql="INSERT INTO Boards(ID_GBBoard,primaryboard,Bname,width,height,autoReturn,autoReadSentence)
 VALUES (?,?,?,?,?,?,?)";
$this->db->query($sql,array(
  $gbkeys[$posc],
  $boards->primaryboard[$i],
  $boards->Bname[$i],
  $boards->width[$i],
  $boards->height[$i],
  $boards->autoReturn[$i],
  $boards->autoReadSentence[$i]
));
}
return count($gbkeys);
}
private function InsertSHistoric($Folder){
$ID_User=$this->session->idusu;
$file = file_get_contents($Folder."/S_Historic.json");
$hs=json_decode($file);
$count=count($hs->isNegative);
for($i=0;$i<$count;$i++){
  $sql="INSERT INTO `S_Historic`(`ID_SHUser`, `sentenceType`, `isNegative`, `sentenceTense`, `sentenceDate`,
    `sentenceFinished`, `intendedSentence`, `inputWords`, `inputIds`, `parseScore`, `parseString`, `generatorScore`, `generatorString`,
    `comments`, `userScore`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
  $this->db->query($sql,array(
    $ID_User,
    $hs->sentenceType[$i],
    $hs->isNegative[$i],
    $hs->sentenceTense[$i],
    $hs->sentenceDate[$i],
    $hs->sentenceFinished[$i],
    $hs->intendedSentence[$i],
    $hs->inputWords[$i],
    $hs->inputIds[$i],
    $hs->parseScore[$i],
    $hs->parseString[$i],
    $hs->generatorScore[$i],
    $hs->generatorString[$i],
    $hs->comments[$i],
    $hs->userScore[$i]
  ));
}
return $count;
}
//Inserta en la base de datos los registros correspondientes a cells
private function InsertCells($Folder,$bcont,$scont,$fcont,$pcont, $overwrite){
$picts = null;
$ID_Cell=array();
$a=array();
$sentencekeys=$this->getSentencekey();
$folderkeys=$this->getfolderkey();
$boardkeys=$this->getBoardkey();
$pictokeys=$this->getPictokeys();

echo "Num. pictos originals: ".$pcont;
print_r($pictokeys);

$file = file_get_contents($Folder."/Cell.json");
$files = file_get_contents($Folder."/Boards.json");
$filesent = file_get_contents($Folder."/S_sentence.json");
$filefol= file_get_contents($Folder."/S_Folder.json");
$picto=file_get_contents($Folder."/Pictograms.json");

$cells=json_decode($file);
$boards=json_decode($files);
$sentences=json_decode($filesent);
$pic=json_decode($picto);
$sfolder=json_decode($filefol);

print_r($pic);

if (!$overwrite) $boardkeys=array_slice($boardkeys,$bcont);
if (!$overwrite) $sentencekeys=array_slice($sentencekeys,$scont);
if (!$overwrite) $folderkeys=array_slice($folderkeys,$fcont);
if (!$overwrite) $pictokeys=array_slice($pictokeys,$pcont);

print_r($pictokeys);

$count=count($cells->ID_Cell);
for($i=0;$i<$count;$i++){
 if(!(is_null($cells->boardLink[$i]))){
     $posc=array_search($cells->boardLink[$i],$boards->ID_Board);
 }else{
     $posc=null;
 }
 if(!(is_null($cells->ID_CSentence[$i]))){
     
     echo "ID SENTENCE CELL JSON: ".$cells->ID_CSentence[$i]." - ";
     
     $poscs=array_search($cells->ID_CSentence[$i],$sentences->ID_SSentence);
     
     echo $poscs;
     
  }else{
     $poscs=null;
  }
 if(!(is_null($cells->sentenceFolder[$i]))){
     $posf=array_search($cells->sentenceFolder[$i],$sfolder->ID_Folder);
 }else{
     $posf=null;
 }
 if($cells->ID_CPicto[$i]>2019){
 echo "IN?";
        $posp=array_search($cells->ID_CPicto[$i],$pic->pictoid);
        $picts=$pictokeys[$posp];
 }else{
   $picts=$cells->ID_CPicto[$i];
 }
    
 $newcell = array(
  $cells->isFixedInGroupBoards[$i],
  $cells->imgCell[$i],
  $picts,
  $sentencekeys[$poscs],
  $folderkeys[$posf],
  $boardkeys[$posc],
  $cells->color[$i],
  $cells->ID_CFunction[$i],
  $cells->textInCell[$i],
  $cells->textInCellTextOnOff[$i],
  $cells->cellType[$i],
  $cells->activeCell[$i]
);
 
print_r($newcell);
 
  $sql="INSERT INTO Cell(isFixedInGroupBoards,imgCell,ID_CPicto,ID_CSentence,sentenceFolder,boardLink,color,
  ID_CFunction,textInCell,textInCellTextOnOff,cellType,activeCell)VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
  $this->db->query($sql,$newcell);
  $query=$this->db->query("SELECT LAST_INSERT_ID() as s2");
  $res=$query->result();
  array_push($ID_Cell,$res[0]->s2);
}
$this->InsertRBoardCell($Folder,$ID_Cell,$bcont, $overwrite);
return $sentencekeys;
}
//Inserta en la base de datos los registros correspondientes a groupboards
private function InsertGroupBoards($Folder){
$ID_User=$this->session->idusu;
$file = file_get_contents($Folder."/GroupBoards.json");
$gboards=json_decode($file);
$count=count($gboards->ID_GBUser);
 for($i=0;$i<$count;$i++){
   if($i==0 && !$this->existsmain()) $mainGboard="1"; else $mainGboard="0";
  $sql="INSERT INTO GroupBoards(ID_GBUser,GBname,primaryGroupBoard,defWidth,defHeight,imgGB)VALUES (?,?,?,?,?,?)";
  $this->db->query($sql,
   array(
    $ID_User,
    $gboards->GBname[$i],
    $mainGboard,
    $gboards->defWidth[$i],
    $gboards->defHeight[$i],
    $gboards->imgGB[$i]
  ));
}
}
//Inserta en la base de datos los registros correspondientes a images
private function InsertImages($Folder){
$ID_User=$this->session->idsu;
$file = file_get_contents($Folder."/Images.json");
$images=json_decode($file);
$count=count($images->ID_Image);
for($i=0;$i<$count;$i++){
$sql="INSERT INTO Images(ID_ISU,imgPath,imgName)VALUES (?,?,?)";
$this->db->query($sql,array(
  $ID_User,
  $images->imgPath[$i],
  $images->imgName[$i]
));
 $this->moveImages($images->imgPath[$i], $Folder);
}
}
//Inserta en la base de datos los registros correspondientes a nameclass
private function InsertNameClass($Folder,$pcont,$overwrite, $backupLanguage){
  $tableBackup = "";
  $tableInsert = "";
  
  switch($backupLanguage){
      case 1:
      $tableBackup="NameClassCA";
      break;
      case 2:
      $tableBackup="NameClassES";
      break;
  }
      
$ID_Language=$this->session->ulanguage;

switch($ID_Language){
      case 1:
      $tableInsert="NameClassCA";
      break;
      case 2:
      $tableInsert="NameClassES";
      break;
}
  
$pictokeys=$this->getPictokeys();

$file = file_get_contents($Folder."/".$tableBackup.".json");
$nclass=json_decode($file);
$count=count($nclass->nameid);
$pics=file_get_contents($Folder."/Pictograms.json");
$pic=json_decode($pics);
if (!$overwrite) $pictokeys= array_slice($pictokeys, $pcont);
for($i=0;$i<$count;$i++){
   $posp=array_search($nclass->nameid[$i],$pic->pictoid);
$sql="INSERT INTO $tableInsert(nameid,class)VALUES (?,?)";
$this->db->query($sql,array(
  $pictokeys[$posp],
  $nclass->class[$i]
));
}
}
//Inserta en la base de datos los registros correspondientes a names
private function InsertNames($Folder,$pcont, $overwrite){
  $a=array();
  $tableBackup = "";
  $tableInsert = "";
  
  $fileU = file_get_contents($Folder."/User.json");
    $us=json_decode($fileU);
    $backupLanguage = $us->ID_ULanguage[0];
    
  switch($backupLanguage){
      case 1:
      $tableBackup="NameCA";
      break;
      case 2:
      $tableBackup="NameES";
      break;
  }
  
$ID_Language=$this->session->ulanguage;
switch($ID_Language){
      case 1:
      $tableInsert="NameCA";
      break;
      case 2:
      $tableInsert="NameES";
      break;
}
$pictokeys=$this->getPictokeys();

$file = file_get_contents($Folder."/".$tableBackup.".json");
$name=json_decode($file);
$pics=file_get_contents($Folder."/Pictograms.json");
$pic=json_decode($pics);
$count=count($name->nameid);
if (!$overwrite) $pictokeys= array_slice($pictokeys, $pcont);
for($i=0;$i<$count;$i++){
 $posp=array_search($name->nameid[$i],$pic->pictoid);
 array_push($a, $posp);
$sql="INSERT INTO $tableInsert(nameid,nomtext,mf,singpl,contabincontab,determinat,ispropernoun,defaultverb,plural,femeni,fempl)
VALUES (?,?,?,?,?,?,?,?,?,?,?)";
$this->db->query($sql,array(
  $pictokeys[$posp],
  $name->nomtext[$i],
  $name->mf[$i],
  $name->singpl[$i],
  $name->contabincontab[$i],
  $name->determinat[$i],
  $name->ispropernoun[$i],
  $name->defaultverb[$i],
  $name->plural[$i],
  $name->femeni[$i],
  $name->fempl[$i]
));
}
$this->InsertNameClass($Folder,$pcont, $overwrite, $backupLanguage);
return $name->nameid;
}
//Inserta en la base de datos los registros correspondientes a pictograms
private function InsertPictograms($Folder){
  $pictosid=array();
$ID_User=$this->session->idusu;
$file = file_get_contents($Folder."/Pictograms.json");
$pictos=json_decode($file);
$count=count($pictos->pictoid);
for($i=0;$i<$count;$i++){
$sql="INSERT INTO Pictograms(ID_PUser,pictoType,supportsExpansion,imgPicto)
VALUES (?,?,?,?)";
$this->db->query($sql,array(
  $ID_User,
  $pictos->pictoType[$i],
  $pictos->supportsExpansion[$i],
  $pictos->imgPicto[$i]
));
$query=$this->db->query("SELECT LAST_INSERT_ID() as s2");
$res=$query->result();
array_push($pictosid,$res[0]->s2);
}

$this->InsertPictogramsLanguage($Folder, $pictosid);
}
//Inserta en la base de datos los registros correspondientes a pcitogramslanguage
private function InsertPictogramsLanguage($Folder,$pictosid){
$ID_Language=$this->session->ulanguage;
        
$file = file_get_contents($Folder."/PictogramsLanguage.json");
$plang=json_decode($file);
$count=count($plang->pictoid);
for($i=0;$i<$count;$i++){
$sql="INSERT INTO PictogramsLanguage(pictoid,languageid,insertdate,pictotext,pictofreq)
VALUES (?,?,?,?,?)";
$this->db->query($sql,array(
$pictosid[$i],
$ID_Language,
$plang->insertdate[$i],
$plang->pictotext[$i],
$plang->pictofreq[$i]
));
}
}
//Inserta en la base de datos los registros correspondientes a R_BoardCell
private function InsertRBoardCell($Folder,$ID_Cell,$bcont, $overwrite){
 $boardkeys=$this->getBoardkey();
 $file = file_get_contents($Folder."/R_BoardCell.json");
 $fileB=file_get_contents($Folder."/Boards.json");
 $rbcell=json_decode($file);
 $boards=json_decode($fileB);
 $count=count($rbcell->ID_RBoard);
 if (!$overwrite) $boardkeys=array_slice($boardkeys,$bcont);
 for($i=0;$i<$count;$i++){
   if(!(is_null($rbcell->ID_RBoard[$i]))){
       $posc=array_search($rbcell->ID_RBoard[$i],$boards->ID_Board);
   }else{
       $posc=null;
   }
  $sql="INSERT INTO R_BoardCell(ID_RBoard,ID_RCell,posInBoard,isMenu,posInMenu,customScanBlock1,customScanBlockText1,customScanBlock2,
    customScanBlockText2)VALUES (?,?,?,?,?,?,?,?,?)";
  $this->db->query($sql,array(
    $boardkeys[$posc],
    $ID_Cell[$i],
    $rbcell->posInBoard[$i],
    $rbcell->isMenu[$i],
    $rbcell->posInMenu[$i],
    $rbcell->customScanBlock1[$i],
    $rbcell->customScanBlockText1[$i],
    $rbcell->customScanBlock2[$i],
    $rbcell->customScanBlockText2[$i]
  ));
}
}
//Inserta en la base de datos los registros correspondientes a R_S_HistoricPictograms
private function InsertRSHistoricPictograms($Folder,$scont, $overwrite){
$a=array();
$histokeys=$this->getHistorickey();
$ID_User=$this->session->idusu;
$file = file_get_contents($Folder."/R_S_HistoricPictograms.json");
$his=file_get_contents($Folder."/S_Historic.json");
$hist=json_decode($his);
$rshp=json_decode($file);
if (!$overwrite) $histokeys=array_slice($histokeys,$scont);
$count=count($rshp->ID_RSHPSentencePicto);
for($i=0;$i<$count;$i++){
 if(!(is_null($rshp->ID_RSHPSentence[$i]))){
     $posh=array_search($rshp->ID_RSHPSentence[$i],$hist->ID_SHistoric);
 }else{
     $posh=null;
 }
 array_push($a,$posh);
$sql="INSERT INTO R_S_HistoricPictograms(ID_RSHPSentence,pictoid,isplural,isfem,coordinated,ID_RSHPUser,imgtemp)
VALUES (?,?,?,?,?,?,?)";
$this->db->query($sql,array(
  $histokeys[$posh],
  $rshp->pictoid[$i],
  $rshp->isplural[$i],
  $rshp->isfem[$i],
  $rshp->coordinated[$i],
  $ID_User,
  $rshp->imgtemp[$i]
));
}
return $rshp;
}
//Inserta en la base de datos los registros correspondientes a R_S_SentencePictograms
private function InsertRSSentencePictograms($Folder,$scont, $overwrite){
$a=array();
$sentkeys=$this->getSSentencekey();
$ID_User=$this->session->idusu;
$file = file_get_contents($Folder."/R_S_SentencePictograms.json");
$sent = file_get_contents($Folder."/S_Sentence.json");
$rssp=json_decode($file);
$sen=json_decode($sent);
if (!$overwrite) $sentkeys=array_slice($sentkeys,$scont);
$count=count($rssp->ID_RSSPSentencePicto);
for($i=0;$i<$count;$i++){
    if(!(is_null($rssp->ID_RSSPSentence[$i]))){
        $posh=array_search($rssp->ID_RSSPSentence[$i],$sen->ID_SSentence);
    }else{
        $posh=null;
    }
    array_push($a,$posh);
$sql="INSERT INTO `R_S_SentencePictograms` (`ID_RSSPSentence`, `pictoid`, `isplural`, `isfem`, `coordinated`, `ID_RSSPUser`, `imgtemp`)
VALUES (?,?,?,?,?,?,?);";
$this->db->query($sql,array(
  $sentkeys[$posh],
  $rssp->pictoid[$i],
  $rssp->isplural[$i],
  $rssp->isfem[$i],
  $rssp->coordinated[$i],
  $ID_User,
  $rssp->imgtemp[$i]
));
}
return $sentkey;
}
//Inserta en la base de datos los registros correspondientes a S_Folder
private function InsertSFolder($Folder){
$ID_User=$this->session->idusu;
$file = file_get_contents($Folder."/S_Folder.json");
$sf=json_decode($file);
$count=count($sf->ID_Folder);
for($i=0;$i<$count;$i++){
$sql="INSERT INTO S_Folder(ID_SFUser,folderName,folderDescr,imgSFolder,folderColor,folderOrder)
VALUES (?,?,?,?,?,?)";
$this->db->query($sql,array(
  $ID_User,
  $sf->folderName[$i],
  $sf->folderDescr[$i],
  $sf->imgSFolder[$i],
  $sf->folderColor[$i],
  $sf->folderOrder[$i]
));
}
return $sf;
}
//Inserta en la base de datos los registros correspondientes a S_Sentence
private function InsertSSentence($Folder, $folds, $overwrite){
$ID_User=$this->session->idusu;
$folderkeys=$this->getfolderkey();
$file = file_get_contents($Folder."/S_Sentence.json");
$filefol= file_get_contents($Folder."/S_Folder.json");
$ss=json_decode($file);
$sfolder=json_decode($filefol);
$count=count($ss->ID_SSentence);

if (!$overwrite) $folderkeys=array_slice($folderkeys,$folds);

for($i=0;$i<$count;$i++){
   if(!(is_null($ss->ID_SFolder[$i]))){
       $posf=array_search($ss->ID_SFolder[$i],$sfolder->ID_Folder);
   }else{
       $posf=null;
   }
  $sql="INSERT INTO S_Sentence(ID_SSUser,ID_SFolder,posInFolder,sentenceType,isNegative,sentenceTense,sentenceDate,
  sentenceFinished,intendedSentence,inputWords,inputIds,parseScore,parseString,generatorScore,generatorString,comments,userScore,
  isPreRec,sPreRecText,sPreRecDate,sPreRecImg1,sPreRecImg2,sPreRecImg3,sPreRecPath)
  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
  $this->db->query($sql,array(
  $ID_User,
  $folderkeys[$posf],
  $ss->posInFolder[$i],
  $ss->sentenceType[$i],
  $ss->isNegative[$i],
  $ss->sentenceTense[$i],
  $ss->sentenceDate[$i],
  $ss->sentenceFinished[$i],
  $ss->intendedSentence[$i],
  $ss->inputWords[$i],
  $ss->inputIds[$i],
  $ss->parseScore[$i],
  $ss->parseString[$i],
  $ss->generatorScore[$i],
  $ss->generatorString[$i],
  $ss->comments[$i],
  $ss->userScore[$i],
  $ss->isPreRec[$i],
  $ss->sPreRecText[$i],
  $ss->sPreRecDate[$i],
  $ss->sPreRecImg1[$i],
  $ss->sPreRecImg2[$i],
  $ss->sPreRecImg3[$i],
  $ss->sPreRecPath[$i]
));
}
}
//sobreescribe en la base de datos los registros correspondientes a SuperUser
private function UpdateSuperUser($Folder,$ow){
$ID_SU=$this->session->idsu;
$ID_User=$this->session->idusu;
$file = file_get_contents($Folder."/SuperUser.json");
$su=json_decode($file);
if($ow){
 $sql="UPDATE SuperUser SET cfgDefUser=?, cfgIsFem=?, cfgUsageMouseOneCTwoC=?,
  cfgTimeClick=?, cfgExpansionOnOff=?, cfgAutoEraseSentenceBar=?, cfgPredOnOff=?,
  cfgPredBarVertHor=?, cfgPredBarNumPred=?, cfgScanningOnOff=?, cfgScanningCustomRowCol=?,
  cfgScanningAutoOnOff=?, cfgCancelScanOnOff=?, cfgTimeScanning=?, cfgScanStartClick=?,
  cfgScanOrderPred=?, cfgScanOrderMenu=?, cfgScanOrderPanel=?, cfgScanColor=?,
  cfgMenuReadActive=?, cfgMenuHomeActive=?, cfgMenuDeleteLastActive=?,
  cfgMenuDeleteAllActive=?, cfgSentenceBarUpDown=?, cfgBgColorPanel=?, cfgBgColorPred=?,
  cfgTextInCell=?, cfgUserExpansionFeedback=?, cfgHistOnOff=?, cfgBlackOnWhiteVSWhiteOnBlack=?,
  cfgTimeLapseSelectOnOff=?, cfgTimeLapseSelect=?, cfgTimeNoRepeatedClickOnOff=?,
  cfgTimeNoRepeatedClick=?, UserValidated=? WHERE ID_SU=?";
  $this->db->query($sql,
  array(
  $ID_User,
  $su->cfgIsFem,
  $su->cfgUsageMouseOneCTwoC,
  $su->cfgTimeClick,
  $su->cfgExpansionOnOff,
  $su->cfgAutoEraseSentenceBar,
  $su->cfgPredOnOff,
  $su->cfgPredBarVertHor,
  $su->cfgPredBarNumPred,
  $su->cfgScanningOnOff,
  $su->cfgScanningCustomRowCol,
  $su->cfgScanningAutoOnOff,
  $su->cfgCancelScanOnOff,
  $su->cfgTimeScanning,
  $su->cfgScanStartClick,
  $su->cfgScanOrderPred,
  $su->cfgScanOrderMenu,
  $su->cfgScanOrderPanel,
  $su->cfgScanColor,
  $su->cfgMenuReadActive,
  $su->cfgMenuHomeActive,
  $su->cfgMenuDeleteLastActive,
  $su->cfgMenuDeleteAllActive,
  $su->cfgSentenceBarUpDown,
  $su->cfgBgColorPanel,
  $su->cfgBgColorPred,
  $su->cfgTextInCell,
  $su->cfgUserExpansionFeedback,
  $su->cfgHistOnOff,
  $su->cfgBlackOnWhiteVSWhiteOnBlack,
  $su->cfgTimeLapseSelectOnOff,
  $su->cfgTimeLapseSelect,
  $su->cfgTimeNoRepeatedClickOnOff,
  $su->cfgTimeNoRepeatedClick,
  $su->UserValidated,
  $ID_SU
));
}
}
//sobreescribe en la base de datos los registros correspondientes a User
private function UpdateUser($Folder){
$ID_User=$this->session->idusu;
$ID_SU=$this->session->idsu;
$file = file_get_contents($Folder."/User.json");
$us=json_decode($file);
$count=count($us->ID_User);
for($i=0;$i<$count;$i++){
$sql="UPDATE User SET ID_USU=?,ID_ULanguage=?, ID_UOrg=?, cfgExpansionLanguage=?,
errorTemp=? WHERE ID_User=?";
$this->db->query($sql,array(
  $ID_SU,
  $us->ID_ULanguage[$i],
  $us->ID_UOrg[$i],
  $us->cfgExpansionLanguage[$i],
  $us->errorTemp[$i],
  $ID_User
));
}
}
//coge las claves de la tabla boards insertadas anteriormente
private function getBoardkey(){
$keys=array();
$ID_User=$this->session->idusu;
$sql="SELECT ID_Board FROM Boards,GroupBoards WHERE ID_GBUser=? AND ID_GB=ID_GBBoard";
$query=$this->db->query($sql,$ID_User);
foreach ($query->result() as $row) {
  array_push($keys,$row->ID_Board);
}
return $keys;
}
//coge las claves de la tabla Pictograms insertadas anteriormente
private function getPictokeys(){
$keys=array();
$ID_User=$this->session->idusu;
$sql="SELECT pictoid FROM Pictograms WHERE ID_PUser=?";
$query=$this->db->query($sql,$ID_User);
foreach ($query->result() as $row) {
  array_push($keys,$row->pictoid);
}
return $keys;
}
private function getPictokeysType($type){
$keys=array();
$ID_User=$this->session->idusu;
$sql="SELECT pictoid FROM Pictograms WHERE ID_PUser=? AND pictoType=?";
$query=$this->db->query($sql,array($ID_User,$type));
foreach ($query->result() as $row) {
  array_push($keys,$row->pictoid);
}
return $keys;
}
//coge las claves de la tabla Groupboards insertadas anteriormente
private function getGBkeys(){
$keys=array();
$ID_User=$this->session->idusu;
$sql="SELECT ID_GB FROM GroupBoards WHERE ID_GBUser=?";
$query=$this->db->query($sql,$ID_User);
foreach ($query->result() as $row) {
  array_push($keys,$row->ID_GB);
}
return $keys;
}
//coge las claves de la tabla S_Folder insertadas anteriormente
private function getfolderkey(){
$keys=array();
$ID_User=$this->session->idusu;
$sql="SELECT ID_Folder FROM S_Folder WHERE ID_SFUser=?";
$query=$this->db->query($sql,$ID_User);
foreach ($query->result() as $row) {
  array_push($keys,$row->ID_Folder);
}
return $keys;
}
//coge las claves de la tabla S_Sentence insertadas anteriormente
private function getSentencekey(){
$keys=array();
$ID_User=$this->session->idusu;
$sql="SELECT ID_SSentence FROM S_Sentence WHERE ID_SSUser=?";
$query=$this->db->query($sql,$ID_User);
foreach ($query->result() as $row) {
  array_push($keys,$row->ID_SSentence);
}
return $keys;
}
private function getSSentencekey(){
$keys=array();
$ID_User=$this->session->idusu;
$sql="SELECT ID_SSentence FROM S_Sentence WHERE ID_SSUser=?";
$query=$this->db->query($sql,$ID_User);
foreach ($query->result() as $row) {
  array_push($keys,$row->ID_SSentence);
}
return $keys;
}
private function existsmain(){
$exists=false;
$ID_User=$this->session->idusu;
$sql="SELECT primaryGroupBoard FROM GroupBoards WHERE ID_GBUser=? AND primaryGroupBoard='1'";
$query=$this->db->query($sql,$ID_User);
if($query->num_rows()>0) $exists=true;
return $exists;
}
private function getHistorickey(){
$keys=array();
$ID_User=$this->session->idusu;
$sql="SELECT ID_SHistoric FROM S_Historic WHERE ID_SHUser=?";
$query=$this->db->query($sql,$ID_User);
foreach ($query->result() as $row) {
  array_push($keys,$row->ID_SHistoric);
}
return $keys;
}
    //mueve las imagenes del backup al servidor para que la aplicacion pueda usarlas
    private function moveImages($imgPath,$imgName){
        if(substr($imgPath,4,6)=='pictos'){
          copy('/xampp/htdocs/Temp/'.$Fname.'/'.'img/pictos/'.$imgName , $imgPath);
        }else{
          copy('xampp/htdocs/Temp/'.$Fname.'/'.'img/users/'.$imgName , $imgPath);
        }
        return $imgPath;
    }
    }
    ?>
