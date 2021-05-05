<?php
/*
Todo el codigo sigue una estructura intuitiva, las variables se llaman igual que los campos
que hay que coger de la base de datos, los resultados estan guardados en array asociativos
para que el paso a json sea limpio.
La definicion de variables a veces está amontonada en grupos de dos o tres por motivos de espacio
y limpieza.
*/
class BackupSelects extends CI_Model{
  function __construct(){
      parent::__construct();
      $this->load->database();
  }
//coge los registros de la base de datos pertenecientes a AdjectiveCA/ES y AdjectiveClassCA/ES
function getAdjectives($ID_User, $ID_Language){
  $adjid=array(); $masc=array();
  $fem=array(); $mascpl=array();
  $fempl=array(); $defaultverb=array(); $subjdef=array();
  $class=array(); $pictoid=array(); $adjid2=array();

  $maintable="AdjectiveCA";
  $classtable="AdjClassCA";
    
  switch($ID_Language){
    case 1:
    $maintable="AdjectiveCA";
    $classtable="AdjClassCA";
    break;
    case 2:
    $maintable="AdjectiveES";
    $classtable="AdjClassES";
    break;
  }

  $sql="SELECT DISTINCT $maintable.adjid,masc,fem,mascpl,fempl,defaultverb,subjdef,pictoid FROM $maintable,Pictograms WHERE
  Pictograms.ID_PUser=? AND Pictograms.pictoid=$maintable.adjid";
  $query=$this->db->query($sql,$ID_User);

  $sql1="SELECT DISTINCT class, adjid AS adjid2 FROM $classtable,Pictograms WHERE
  Pictograms.ID_PUser=? AND Pictograms.pictoid=$classtable.adjid";
  $query1=$this->db->query($sql1,$ID_User);

  foreach ($query->result() as $row) {
    array_push($adjid,$row->adjid);
    array_push($masc,$row->masc);
    array_push($fem,$row->fem);
    array_push($mascpl,$row->mascpl);
    array_push($fempl,$row->fempl);
    array_push($defaultverb,$row->defaultverb);
    array_push($subjdef,$row->subjdef);
    array_push($pictoid,$row->pictoid);
  }
  foreach ($query1->result() as $row) {
    array_push($class,$row->class);
    array_push($adjid2,$row->adjid2);
  }
  $data=array(
    'adjid'=>$adjid,
    'masc'=>$masc,
    'fem'=>$fem,
    'mascpl'=>$mascpl,
    'fempl'=>$fempl,
    'defaultverb'=>$defaultverb,
    'subjdef'=>$subjdef,
    'class'=>$class,
    'pictoid'=>$pictoid,
    'adjid2'=>$adjid2
  );
  
  return $data;
}

//coge todos los registros de la tabla Boards en funcion del iduser
function getBoards($ID_User){
  $ID_Board=array(); $ID_GBBoard=array();
  $primaryboard=array(); $Bname=array();
  $width=array(); $height=array();
  $autoReturn=array(); $autoReadSentence=array();

  $sql="SELECT DISTINCT ID_Board,ID_GBBoard,primaryboard,Bname,width,height,autoReturn,autoReadSentence FROM Boards,GroupBoards
  WHERE GroupBoards.ID_GBUser=? AND GroupBoards.ID_GB=Boards.ID_GBBoard";
  $query=$this->db->query($sql,$ID_User);
  foreach ($query->result() as $row) {
    array_push($ID_Board,$row->ID_Board);
    array_push($ID_GBBoard,$row->ID_GBBoard);
    array_push($primaryboard,$row->primaryboard);
    array_push($Bname,$row->Bname);
    array_push($width,$row->width);
    array_push($height,$row->height);
    array_push($autoReturn,$row->autoReturn);
    array_push($autoReadSentence,$row->autoReadSentence);
  }

  $data=array(
    'ID_Board'=>$ID_Board,
    'ID_GBBoard'=>$ID_GBBoard,
    'primaryboard'=>$primaryboard,
    'Bname'=>$Bname,
    'width'=>$width,
    'height'=>$height,
    'autoReturn'=>$autoReturn,
    'autoReadSentence'=>$autoReadSentence
  );
  return $data;
}
private function getBoardkey($ID_User){
  $keys=array();
  $sql="SELECT ID_Board FROM Boards,GroupBoards WHERE ID_GBUser=? AND ID_GB=ID_GBBoard";
  $query=$this->db->query($sql,$ID_User);
  foreach ($query->result() as $row) {
    array_push($keys,$row->ID_Board);
  }
  return $keys;
}
//coge todos los registros de la tabla Cell en funcion del iduser
function getCell($ID_User){
  $ID_Cell=array(); $isFixedInGroupBoards=array(); $imgCell=array();
  $ID_CPicto=array(); $ID_CSentence=array(); $sentenceFolder=array();
  $boardLink=array(); $color=array(); $ID_CFunction=array();
  $textInCell=array(); $textInCellTextOnOff=array(); $cellType=array();
  $activeCell=array();
  $boardkey=$this->getBoardkey($ID_User);
  for($i=0;$i<count($boardkey);$i++){
    $sql="SELECT DISTINCT ID_Cell,isFixedInGroupBoards,imgCell,ID_CPicto,ID_CSentence ,sentenceFolder,boardLink,color,
    ID_CFunction, textInCell,textInCellTextOnOff,cellType,activeCell FROM Cell,R_BoardCell WHERE R_BoardCell.ID_RBoard=?
    AND R_BoardCell.ID_RCell=Cell.ID_Cell";
     $query=$this->db->query($sql,$boardkey[$i]);

     foreach ($query->result() as $row) {
       array_push($ID_Cell,$row->ID_Cell);
       array_push($isFixedInGroupBoards,$row->isFixedInGroupBoards);
       array_push($imgCell,$row->imgCell);
       array_push($ID_CPicto,$row->ID_CPicto);
       array_push($ID_CSentence,$row->ID_CSentence);
       array_push($sentenceFolder,$row->sentenceFolder);
       array_push($boardLink,$row->boardLink);
       array_push($color,$row->color);
       array_push($ID_CFunction,$row->ID_CFunction);
       array_push($textInCell,$row->textInCell);
       array_push($textInCellTextOnOff,$row->textInCellTextOnOff);
       array_push($cellType,$row->cellType);
       array_push($activeCell,$row->activeCell);
     }
  }
  $data=array(
    'ID_Cell'=>$ID_Cell,
    'isFixedInGroupBoards'=>$isFixedInGroupBoards,
    'imgCell'=>$imgCell,
    'ID_CPicto'=>$ID_CPicto,
    'ID_CSentence'=>$ID_CSentence,
    'sentenceFolder'=>$sentenceFolder,
    'boardLink'=>$boardLink,
    'color'=>$color,
    'ID_CFunction'=>$ID_CFunction,
    'textInCell'=>$textInCell,
    'textInCellTextOnOff'=>$textInCellTextOnOff,
    'cellType'=>$cellType,
    'activeCell'=>$activeCell
  );
   return $data;
}

//coge todos los registros de la tabla GroupBoards en funcion del iduser
function getGroupBoards($ID_User){
  $ID_GB=array(); $ID_GBUser=array();
  $GBname=array();$primaryGroupBoard=array();
  $defWidth=array(); $defHeight=array();
  $imgGB=array();

  $sql="SELECT ID_GB,ID_GBUser,GBname,primaryGroupBoard,defWidth,defHeight,imgGB
  FROM GroupBoards WHERE ID_GBUser=?";
  $query=$this->db->query($sql,$ID_User);

  foreach ($query->result() as $row) {
    array_push($ID_GB,$row->ID_GB);
    array_push($ID_GBUser,$row->ID_GBUser);
    array_push($GBname,$row->GBname);
    array_push($primaryGroupBoard,$row->primaryGroupBoard);
    array_push($defWidth,$row->defWidth);
    array_push($defHeight,$row->defHeight);
    array_push($imgGB,$row->imgGB);
  }
  $data=array(
    'ID_GB'=>$ID_GB,
    'ID_GBUser'=>$ID_GBUser,
    'GBname'=>$GBname,
    'primaryGroupBoard'=>$primaryGroupBoard,
    'defWidth'=>$defWidth,
    'defHeight'=>$defHeight,
    'imgGB'=>$imgGB
  );
  return $data;
}

//coge todos los registros de la tabla Images en funcion del iduser
function getImages($ID_User){
  $ID_Image=array(); $ID_ISU=array();
  $imgPath=array(); $imgName=array();
  $isArasaac=array();

  $sql="SELECT * FROM Images WHERE ID_ISU=?";
  $query=$this->db->query($sql,$ID_User);

  foreach ($query->result() as $row) {
    array_push($ID_Image,$row->ID_Image);
    array_push($ID_ISU,$row->ID_ISU);
    array_push($imgPath,$row->imgPath);
    array_push($imgName,$row->imgName);
    array_push($isArasaac,$row->isArasaac);
  }
  $data=array(
    'ID_Image'=>$ID_Image,
    'ID_ISU'=>$ID_ISU,
    'imgPath'=>$imgPath,
    'imgName'=>$imgName,
    'isArasaac'=>$isArasaac
  );
  return $data;
}

//coge los registros de la base de datos pertenecientes a NameCA/ES y NameClassCA/ES
function getNames($ID_User, $ID_Language){

  $nomtext=array(); $mf=array(); $singpl=array();
  $contabincontab=array(); $determinat=array(); $ispropernoun=array();
  $class=array(); $pictoid=array(); $defaultverb=array(); $plural=array();
  $femeni=array(); $fempl=array(); $nameid=array();
  
  $maintable="NameCA";
  $classtable="NameClassCA";
  
  switch($ID_Language){
    case 1:
    $maintable="NameCA";
    $classtable="NameClassCA";
    break;
    case 2:
    $maintable="NameES";
    $classtable="NameClassES";
    break;
  }

    $sql="SELECT DISTINCT nomtext,mf,singpl,contabincontab,determinat,ispropernoun,pictoid,
    defaultverb,plural,femeni,fempl FROM $maintable,Pictograms WHERE
    Pictograms.ID_PUser=? AND Pictograms.pictoid=$maintable.nameid";
    $query=$this->db->query($sql,$ID_User);

    $sql1="SELECT DISTINCT class, nameid FROM $classtable,Pictograms WHERE
    Pictograms.ID_PUser=? AND Pictograms.pictoid=$classtable.nameid";
    $query1=$this->db->query($sql1,$ID_User);

  foreach ($query->result() as $row) {
    array_push($nomtext,$row->nomtext);
    array_push($mf,$row->mf);
    array_push($singpl,$row->singpl);
    array_push($contabincontab,$row->contabincontab);
    array_push($defaultverb,$row->defaultverb);
    array_push($determinat,$row->determinat);
    array_push($ispropernoun,$row->ispropernoun);
    array_push($plural,$row->plural);
    array_push($femeni,$row->femeni);
    array_push($fempl,$row->fempl);
    array_push($pictoid,$row->pictoid);
  }
  foreach ($query1->result() as $row) {
    array_push($class,$row->class);
    array_push($nameid,$row->nameid);
  }
  $data=array(
    'nomtext'=>$nomtext,
    'mf'=>$mf,
    'singpl'=>$singpl,
    'contabincontab'=>$contabincontab,
    'defaultverb'=>$defaultverb,
    'class'=>$class,
    'determinat'=>$determinat,
    'ispropernoun'=>$ispropernoun,
    'plural'=>$plural,
    'femeni'=>$femeni,
    'fempl'=>$fempl,
    'pictoid'=>$pictoid,
    'nameid'=>$nameid
  );
  return $data;
}

// Retorna los registros de la base de datos pertenecientes a VerbCA/ES,
// VerConjugationCA/ES y PatternCA/ES
function getVerbs($ID_User, $ID_Language){
        
  $pictoid = array(); $verbtext = array(); $actiu = array();
  $tense = array(); $pers = array(); $singpl = array(); $verbconj = array();
  
  $patternid = array(); $pronominal = array(); $pseudoimpersonal = array();
  $copulatiu = array(); $tipusfrase = array(); $defaulttense = array();
  $verbpeticio = array(); $subj = array(); $subjdef = array(); $theme = array();
  $themetipus = array(); $themedef = array(); $themeprep = array(); $themeart = array();
  $receiver = array(); $receiverdef = array(); $receiverprep = array(); $benef = array();
  $beneftipus = array(); $benefdef = array(); $benefprep = array(); $acomp = array();
  $acompdef = array(); $acompprep = array(); $tool = array(); $tooldef = array(); 
  $toolprep = array(); $manera = array(); $maneradef = array(); $maneratipus = array();
  $locto = array(); $loctotipus = array(); $loctodef = array(); $loctoprep = array();
  $locfrom = array(); $locfromtipus = array(); $locfromdef = array(); $locfromprep = array();
  $locat = array(); $locatdef = array(); $locatprep = array(); $time = array();
  $expressio = array(); $subverb = array(); $exemple = array(); $verbid1 = array(); $verbid2 = array();
  
  $maintable = "VerbCA";
  $conjtable = "VerbConjugationCA";
  $patterntable = "PatternCA";
  
  switch($ID_Language){
    case 1:
        $maintable = "VerbCA";
        $conjtable = "VerbConjugationCA";
        $patterntable = "PatternCA";
        break;
    case 2:
        $maintable = "VerbES";
        $conjtable = "VerbConjugationES";
        $patterntable = "PatternES";
        break;
  }

    $sql="SELECT DISTINCT pictoid, verbtext, actiu FROM $maintable, Pictograms WHERE
    Pictograms.ID_PUser=? AND Pictograms.pictoid=$maintable.verbid";
    $query=$this->db->query($sql,$ID_User);

    $sql1="SELECT DISTINCT tense, pers, singpl, verbconj, verbid AS verbid1 FROM $conjtable, Pictograms WHERE
    Pictograms.ID_PUser=? AND Pictograms.pictoid=$conjtable.verbid";
    $query1=$this->db->query($sql1,$ID_User);
    
    $sql2="SELECT DISTINCT patternid, pronominal, pseudoimpersonal, copulatiu, tipusfrase,"
    . "defaulttense, verbpeticio, subj, subjdef, theme, themetipus, themedef, themeprep,"
    . " themeart, receiver, receiverdef, receiverprep, benef, beneftipus, benefdef, benefprep,"
    . "acomp, acompdef, acompprep, tool, tooldef, toolprep, manera, maneradef, maneratipus, "
    . "locto, loctotipus, loctodef, loctoprep, locfrom, locfromtipus, locfromdef, locfromprep,"
    . "locat, locatdef, locatprep, time, expressio, subverb, exemple, verbid AS verbid2 "
    . "FROM $patterntable, Pictograms WHERE Pictograms.ID_PUser=? AND Pictograms.pictoid=$patterntable.verbid";
    $query2=$this->db->query($sql2,$ID_User);

  foreach ($query->result() as $row) {
    array_push($pictoid, $row->pictoid);
    array_push($verbtext, $row->verbtext);
    array_push($actiu, $row->actiu);
  }
  foreach ($query1->result() as $row) {
    array_push($verbid1, $row->verbid1);
    array_push($tense, $row->tense);
    array_push($pers, $row->pers);
    array_push($singpl, $row->singpl);
    array_push($verbconj, $row->verbconj);
  }
  foreach ($query2->result() as $row) {
    array_push($patternid, $row->patternid);
    array_push($pronominal, $row->pronominal);
    array_push($pseudoimpersonal, $row->pseudoimpersonal);
    array_push($copulatiu, $row->copulatiu);
    array_push($tipusfrase, $row->tipusfrase);
    array_push($defaulttense, $row->defaulttense);
    array_push($verbpeticio, $row->verbpeticio);
    array_push($subj, $row->subj);
    array_push($subjdef, $row->subjdef);
    array_push($theme, $row->theme);
    array_push($themetipus, $row->themetipus);
    array_push($themedef, $row->themedef);
    array_push($themeprep, $row->themeprep);
    array_push($themeart, $row->themeart);
    array_push($receiver, $row->receiver);
    array_push($receiverdef, $row->receiverdef);
    array_push($receiverprep, $row->receiverprep);
    array_push($benef, $row->benef);
    array_push($beneftipus, $row->beneftipus);
    array_push($benefdef, $row->benefdef);
    array_push($benefprep, $row->benefprep);
    array_push($acomp, $row->acomp);
    array_push($acompdef, $row->acompdef);
    array_push($acompprep, $row->acompprep);
    array_push($tool, $row->tool);
    array_push($tooldef, $row->tooldef);
    array_push($toolprep, $row->toolprep);
    array_push($manera, $row->manera);
    array_push($maneradef, $row->maneradef);
    array_push($maneratipus, $row->maneratipus);
    array_push($locto, $row->locto);
    array_push($loctotipus, $row->loctotipus);
    array_push($loctodef, $row->loctodef);
    array_push($loctoprep, $row->loctoprep);
    array_push($locfrom, $row->locfrom);
    array_push($locfromtipus, $row->locfromtipus);
    array_push($locfromdef, $row->locfromdef);
    array_push($locfromprep, $row->locfromprep);
    array_push($locat, $row->locat);
    array_push($locatdef, $row->locatdef);
    array_push($locatprep, $row->locatprep);
    array_push($time, $row->time);
    array_push($expressio, $row->expressio);
    array_push($subverb, $row->subverb);
    array_push($exemple, $row->exemple);
    array_push($verbid2, $row->verbid2);
  }
    $data=array(
        'pictoid'=>$pictoid,
        'verbtext'=>$verbtext,
        'actiu'=>$actiu,
        'tense'=>$tense,
        'pers'=>$pers,
        'singpl'=>$singpl,
        'verbconj'=>$verbconj,
        'patternid'=>$patternid,
        'pronominal'=>$pronominal,
        'pseudoimpersonal'=>$pseudoimpersonal,
        'copulatiu'=>$copulatiu,
        'tipusfrase'=>$tipusfrase,
        'defaulttense'=>$defaulttense,
        'verbpeticio'=>$verbpeticio,
        'subj'=>$subj,
        'subjdef'=>$subjdef,
        'theme'=>$theme,
        'themetipus'=>$themetipus,
        'themedef'=>$themedef,
        'themeprep'=>$themeprep,
        'themeart'=>$themeart,
        'receiver'=>$receiver,
        'receiverdef'=>$receiverdef,
        'receiverprep'=>$receiverprep,
        'benef'=>$benef,
        'beneftipus'=>$beneftipus,
        'benefdef'=>$benefdef,
        'benefprep'=>$benefprep,
        'acomp'=>$acomp,
        'acompdef'=>$acompdef,
        'acompprep'=>$acompprep,
        'tool'=>$tool,
        'tooldef'=>$tooldef,
        'toolprep'=>$toolprep,
        'locto'=>$locto,
        'loctotipus'=>$loctotipus,
        'loctodef'=>$loctodef,
        'loctoprep'=>$loctoprep,
        'locfrom'=>$locfrom,
        'locfromtipus'=>$locfromtipus,
        'locfromdef'=>$locfromdef,
        'locfromprep'=>$locfromprep,
        'locat'=>$locat,
        'locatdef'=>$locatdef,
        'locatprep'=>$locatprep,
        'time'=>$time,
        'expressio'=>$expressio,
        'subverb'=>$subverb,
        'exemple'=>$exemple,
        'verbid1'=>$verbid1,
        'verbid2'=>$verbid2
    );
        
  return $data;
}

//coge todos los registros de la tabla Pictograms en funcion del iduser
function getPictograms($ID_User){
  $pictoid=array();
  $ID_PUser=array();	$pictoType=array();
  $supportsExpansion=array(); $imgPicto=array();
  $imgFolder=array();
  
  $sql="SELECT * FROM Pictograms WHERE ID_PUser=?";
  $query=$this->db->query($sql,$ID_User);

  foreach ($query->result() as $row) {
    array_push($pictoid,$row->pictoid);
    array_push($ID_PUser,$row->ID_PUser);
    array_push($pictoType,$row->pictoType);
    array_push($supportsExpansion,$row->supportsExpansion);
    array_push($imgPicto,$row->imgPicto);
    array_push($imgFolder,$row->imgFolder);
  }
  $data=array(
    'pictoid'=>$pictoid,
    'ID_PUser'=>$ID_PUser,
    'pictoType'=>$pictoType,
    'supportsExpansion'=>$supportsExpansion,
    'imgPicto'=>$imgPicto,
    'imgFolder'=>$imgFolder
  );
  return $data;
}
//coge todos los registros de la tabla PictogramsLanguage en funcion del iduser
function getPictogramsLanguage($ID_User){
    $pictoid=array();
    $languageid=array();	$insertdate=array();
    $pictotext=array(); $pictofreq=array();

    $sql="SELECT Pictograms.pictoid,languageid,insertdate,pictotext,pictofreq FROM PictogramsLanguage,Pictograms WHERE
    Pictograms.ID_PUser=? AND Pictograms.pictoid=PictogramsLanguage.pictoid";
    $query=$this->db->query($sql,$ID_User);

    foreach ($query->result() as $row) {
      array_push($pictoid,$row->pictoid);
      array_push($languageid,$row->languageid);
      array_push($insertdate,$row->insertdate);
      array_push($pictotext,$row->pictotext);
      array_push($pictofreq,$row->pictofreq);
    }
    $data=array(
      'pictoid'=>$pictoid,
      'languageid'=>$languageid,
      'insertdate'=>$insertdate,
      'pictotext'=>$pictotext,
      'pictofreq'=>$pictofreq
    );
    return $data;
}

//coge todos los registros de la tabla R_BoardCell en funcion del iduser
function getRBoardCell($ID_User){
  $ID_RBoard=array(); $ID_RCell=array(); $posInBoard=array();
  $isMenu=array(); $posInMenu=array();
  $customScanBlock1=array(); $customScanBlockText1=array();
  $customScanBlock2=array(); $customScanBlockText2=array();

  $boardkey=$this->getBoardkey($ID_User);
   for($i=0;$i<count($boardkey);$i++){
     $sql="SELECT ID_RBoard,ID_RCell,posInBoard,isMenu,posInMenu,customScanBlock1,
     customScanBlockText1,customScanBlock2,customScanBlockText2 FROM Cell,R_BoardCell WHERE R_BoardCell.ID_RBoard=?
     AND R_BoardCell.ID_RCell=Cell.ID_Cell";
     $query=$this->db->query($sql,$boardkey[$i]);

     foreach ($query->result() as $row) {
       array_push($ID_RBoard,$row->ID_RBoard);
       array_push($ID_RCell,$row->ID_RCell);
       array_push($posInBoard,$row->posInBoard);
       array_push($isMenu,$row->isMenu);
       array_push($customScanBlock1,$row->customScanBlock1);
       array_push($customScanBlock2,$row->customScanBlock2);
       array_push($customScanBlockText1,$row->customScanBlockText1);
       array_push($customScanBlockText2,$row->customScanBlockText2);
     }
   }
   $data=array(
     'ID_RBoard'=>$ID_RBoard,
     'ID_RCell'=>$ID_RCell,
     'posInBoard'=>$posInBoard,
     'isMenu'=>$isMenu,
     'customScanBlock1'=>$customScanBlock1,
     'customScanBlock2'=>$customScanBlock2,
     'customScanBlockText1'=>$customScanBlockText1,
     'customScanBlockText2'=>$customScanBlockText2
   );
   return $data;
}

//coge todos los registros de la tabla R_S_HistoricPictograms en funcion del iduser
function getRSHistoricPictograms($ID_User){
  $ID_RSHPSentencePicto=array(); $ID_RSHPSentence=array(); $pictoid=array();
  $isplural=array(); $isfem=array();
  $coordinated=array(); $ID_RSHPUser=array();
  $imgtemp=array();
  $texttemp=array();

  $sql="SELECT ID_RSHPSentencePicto,ID_RSHPSentence,pictoid,isplural,isfem,coordinated,ID_RSHPUser,imgtemp,texttemp
  FROM R_S_HistoricPictograms,S_Historic WHERE S_Historic.ID_SHUser=? AND R_S_HistoricPictograms.ID_RSHPSentence=S_Historic.ID_SHistoric";
  $query=$this->db->query($sql,$ID_User);

  foreach ($query->result() as $row) {
    array_push($ID_RSHPSentencePicto,$row->ID_RSHPSentencePicto);
    array_push($ID_RSHPSentence,$row->ID_RSHPSentence);
    array_push($pictoid,$row->pictoid);
    array_push($isplural,$row->isplural);
    array_push($isfem,$row->isfem);
    array_push($coordinated,$row->coordinated);
    array_push($ID_RSHPUser,$row->ID_RSHPUser);
    array_push($imgtemp,$row->imgtemp);
    array_push($texttemp,$row->texttemp);
  }
  $data=array(
    'ID_RSHPSentencePicto'=>$ID_RSHPSentencePicto,
    'ID_RSHPSentence'=>$ID_RSHPSentence,
    'pictoid'=>$pictoid,
    'isplural'=>$isplural,
    'isfem'=>$isfem,
    'coordinated'=>$coordinated,
    'ID_RSHPUser'=>$ID_RSHPUser,
    'imgtemp'=>$imgtemp,
    'texttemp'=>$texttemp
  );
  return $data;
}

//coge todos los registros de la tabla R_S_SentencePictograms en funcion del iduser
function getRSSentecePictograms($ID_User){
  $ID_RSHPSentencePicto=array(); $ID_RSHPSentence=array(); $pictoid=array();
  $isplural=array(); $isfem=array();
  $coordinated=array(); $ID_RSHPUser=array();
  $imgtemp=array();
  $texttemp=array();

  $sql="SELECT ID_RSSPSentencePicto,ID_RSSPSentence,pictoid,isplural,isfem,coordinated,ID_RSSPUser,imgtemp,texttemp FROM R_S_SentencePictograms,S_Sentence
  WHERE S_Sentence.ID_SSUser=? AND R_S_SentencePictograms.ID_RSSPSentence=S_Sentence.ID_SSentence";
  $query=$this->db->query($sql,$ID_User);

  foreach ($query->result() as $row) {
    array_push($ID_RSHPSentencePicto,$row->ID_RSSPSentencePicto);
    array_push($ID_RSHPSentence,$row->ID_RSSPSentence);
    array_push($pictoid,$row->pictoid);
    array_push($isplural,$row->isplural);
    array_push($isfem,$row->isfem);
    array_push($coordinated,$row->coordinated);
    array_push($ID_RSHPUser,$row->ID_RSSPUser);
    array_push($imgtemp,$row->imgtemp);
    array_push($texttemp,$row->texttemp);
  }
  $data=array(
    'ID_RSSPSentencePicto'=>$ID_RSHPSentencePicto,
    'ID_RSSPSentence'=>$ID_RSHPSentence,
    'pictoid'=>$pictoid,
    'isplural'=>$isplural,
    'isfem'=>$isfem,
    'coordinated'=>$coordinated,
    'ID_RSSPUser'=>$ID_RSHPUser,
    'imgtemp'=>$imgtemp,
    'texttemp'=>$texttemp
  );
  return $data;
}
function getHistoric($ID_User){
  $ID_SHUser=array(); $sentenceType=array();
  $isNegative=array(); $sentenceTense=array();
  $sentenceDate=array(); $sentenceFinished=array();
  $intendedSentence=array(); $inputWords=array();
  $inputIds=array(); $parseScore=array();
  $parseString=array(); $generatorScore=array();
  $generatorString=array(); $comments=array();
  $userScore=array(); $ID_SHistoric=array();
  $isDeleted=array();

  $sql="SELECT * FROM S_Historic WHERE ID_SHUser=?";
  $query=$this->db->query($sql,$ID_User);

  foreach ($query->result() as $row) {
    array_push($ID_SHistoric,$row->ID_SHistoric);
    array_push($ID_SHUser,$ID_User);
    array_push($sentenceType,$row->sentenceType);
    array_push($isNegative,$row->isNegative);
    array_push($sentenceTense,$row->sentenceTense);
    array_push($sentenceDate,$row->sentenceDate);
    array_push($sentenceFinished,$row->sentenceFinished);
    array_push($intendedSentence,$row->intendedSentence);
    array_push($inputWords,$row->inputWords);
    array_push($inputIds,$row->inputIds);
    array_push($parseScore,$row->parseScore);
    array_push($parseString,$row->parseString);
    array_push($generatorScore,$row->generatorScore);
    array_push($generatorString,$row->generatorString);
    array_push($comments,$row->comments);
    array_push($userScore,$row->userScore);
    array_push($isDeleted,$row->isDeleted);
  }
  $data=array(
    'ID_SHistoric'=>$ID_SHistoric,
    'ID_SHUser'=>$ID_User,
    'sentenceType'=>$sentenceType,
    'isNegative'=>$isNegative,
    'sentenceTense'=>$sentenceTense,
    'sentenceDate'=>$sentenceDate,
    'sentenceFinished'=>$sentenceFinished,
    'intendedSentence'=>$intendedSentence,
    'inputWords'=>$inputWords,
    'inputIds'=>$inputIds,
    'parseScore'=>$parseScore,
    'parseString'=>$parseString,
    'generatorScore'=>$generatorScore,
    'generatorString'=>$generatorString,
    'comments'=>$comments,
    'userScore'=>$userScore,
    'isDeleted'=>$isDeleted
  );
  return $data;
}
//coge todos los registros de la tabla SuperUser en funcion del iduser
function getSuperUser($ID_SUser){
  $sql="SELECT * FROM SuperUser WHERE ID_SU=?";
  $query=$this->db->query($sql,$ID_SUser);
  foreach ($query->result() as $row) {
    $ID_SU=$row->ID_SU;
    $SUname=$row->SUname;
    $pswd=$row->pswd;
    $realname=$row->realname;
    $surnames=$row->surnames;
    $email=$row->email;
    $cfgDefUser=$row->cfgDefUser;
    $cfgIsFem=$row->cfgIsFem;
    $cfgUsageMouseOneCTwoC=$row->cfgUsageMouseOneCTwoC;
    $cfgTimeClick=$row->cfgTimeClick;
    $cfgExpansionOnOff=$row->cfgExpansionOnOff;
    $cfgAutoEraseSentenceBar=$row->cfgAutoEraseSentenceBar;
    $cfgPredOnOff=$row->cfgPredOnOff;
    $cfgPredBarVertHor=$row->cfgPredBarVertHor;
    $cfgPredBarNumPred=$row->cfgPredBarNumPred;
    $cfgScanningOnOff=$row->cfgScanningOnOff;
    $cfgScanningCustomRowCol=$row->cfgScanningCustomRowCol;
    $cfgScanningAutoOnOff=$row->cfgScanningAutoOnOff;
    $cfgCancelScanOnOff=$row->cfgCancelScanOnOff;
    $cfgTimeScanning=$row->cfgTimeScanning;
    $cfgScanStartClick=$row->cfgScanStartClick;
    $cfgScanOrderPred=$row->cfgScanOrderPred;
    $cfgScanOrderMenu=$row->cfgScanOrderMenu;
    $cfgScanOrderPanel=$row->cfgScanOrderPanel;
    $cfgScanColor=$row->cfgScanColor;
    $cfgMenuReadActive=$row->cfgMenuReadActive;
    $cfgMenuHomeActive=$row->cfgMenuHomeActive;
    $cfgMenuDeleteLastActive=$row->cfgMenuDeleteLastActive;
    $cfgMenuDeleteAllActive=$row->cfgMenuDeleteAllActive;
    $cfgSentenceBarUpDown=$row->cfgSentenceBarUpDown;
    $cfgBgColorPanel=$row->cfgBgColorPanel;
    $cfgBgColorPred=$row->cfgBgColorPred;
    $cfgTextInCell=$row->cfgTextInCell;
    $cfgUserExpansionFeedback=$row->cfgUserExpansionFeedback;
    $cfgHistOnOff=$row->cfgHistOnOff;
    $cfgBlackOnWhiteVSWhiteOnBlack=$row->cfgBlackOnWhiteVSWhiteOnBlack;
    $cfgTimeLapseSelectOnOff=$row->cfgTimeLapseSelectOnOff;
    $cfgTimeLapseSelect=$row->cfgTimeLapseSelect;
    $cfgTimeNoRepeatedClickOnOff=$row->cfgTimeNoRepeatedClickOnOff;
    $cfgTimeNoRepeatedClick=$row->cfgTimeNoRepeatedClick;
    $UserValidated=$row->UserValidated;
    $insertDate=$row->insertDate;
    $cfgMenuCopyClipboard=$row->cfgMenuCopyClipboard;
    $cfgMenuCopyTxtImgClipboard=$row->cfgMenuCopyTxtImgClipboard;
    $cfgCellWithBorder=$row->cfgCellWithBorder;
    $cfgTxtRdngBarOnOff=$row->cfgTxtRdngBarOnOff;
    $cfgMenuDeleteSelectedPicto=$row->cfgMenuDeleteSelectedPicto;
    $cfgMenuBlock=$row->cfgMenuBlock;
    $data=array(
      'ID_SU'=>$ID_SU,
      'SUname'=>$SUname,
      'pswd'=>$pswd,
      'realname'=>$realname,
      'surnames'=>$surnames,
      'email'=>$email,
      'cfgDefUser'=>$cfgDefUser,
      'cfgIsFem'=>$cfgIsFem,
      'cfgUsageMouseOneCTwoC'=>$cfgUsageMouseOneCTwoC,
      'cfgTimeClick'=>$cfgTimeClick,
      'cfgExpansionOnOff'=>$cfgExpansionOnOff,
      'cfgAutoEraseSentenceBar'=>$cfgAutoEraseSentenceBar,
      'cfgPredOnOff'=>$cfgPredOnOff,
      'cfgPredBarVertHor'=>$cfgPredBarVertHor,
      'cfgPredBarNumPred'=>$cfgPredBarNumPred,
      'cfgScanningOnOff'=>$cfgScanningOnOff,
      'cfgScanningCustomRowCol'=>$cfgScanningCustomRowCol,
      'cfgScanningAutoOnOff'=>$cfgScanningAutoOnOff,
      'cfgCancelScanOnOff'=>$cfgCancelScanOnOff,
      'cfgTimeScanning'=>$cfgTimeScanning,
      'cfgScanStartClick'=>$cfgScanStartClick,
      'cfgScanOrderPred'=>$cfgScanOrderPred,
      'cfgScanOrderMenu'=>$cfgScanOrderMenu,
      'cfgScanOrderPanel'=>$cfgScanOrderPanel,
      'cfgScanColor'=>$cfgScanColor,
      'cfgMenuReadActive'=>$cfgMenuReadActive,
      'cfgMenuHomeActive'=>$cfgMenuHomeActive,
      'cfgMenuDeleteLastActive'=>$cfgMenuDeleteLastActive,
      'cfgMenuDeleteAllActive'=>$cfgMenuDeleteAllActive,
      'cfgSentenceBarUpDown'=>$cfgSentenceBarUpDown,
      'cfgBgColorPanel'=>$cfgBgColorPanel,
      'cfgBgColorPred'=>$cfgBgColorPred,
      'cfgTextInCell'=>$cfgTextInCell,
      'cfgUserExpansionFeedback'=>$cfgUserExpansionFeedback,
      'cfgHistOnOff'=>$cfgHistOnOff,
      'cfgBlackOnWhiteVSWhiteOnBlack'=>$cfgBlackOnWhiteVSWhiteOnBlack,
      'cfgTimeLapseSelectOnOff'=>$cfgTimeLapseSelectOnOff,
      'cfgTimeLapseSelect'=>$cfgTimeLapseSelect,
      'cfgTimeNoRepeatedClickOnOff'=>$cfgTimeNoRepeatedClickOnOff,
      'cfgTimeNoRepeatedClick'=>$cfgTimeNoRepeatedClick,
      'UserValidated'=>$UserValidated,
      'insertDate'=>$insertDate,
      'cfgMenuCopyClipboard'=>$cfgMenuCopyClipboard,
      'cfgMenuCopyTxtImgClipboard'=>$cfgMenuCopyTxtImgClipboard,
      'cfgCellWithBorder'=>$cfgCellWithBorder,
      'cfgTxtRdngBarOnOff'=>$cfgTxtRdngBarOnOff,
      'cfgMenuDeleteSelectedPicto'=>$cfgMenuDeleteSelectedPicto,
      'cfgMenuBlock'=>$cfgMenuBlock
    );
      return $data;
  }
}

//coge todos los registros de la tabla S_Folder en funcion del iduser
function getSFolder($ID_User){
  $ID_Folder=array(); $ID_SFUser=array(); $folderName=array();
  $folderDescr=array(); $imgSFolder=array();
  $folderColor=array(); $folderOrder=array();

  $sql="SELECT * FROM S_Folder WHERE ID_SFUser=?";
  $query=$this->db->query($sql,$ID_User);

  foreach ($query->result() as $row) {
    array_push($ID_Folder,$row->ID_Folder);
    array_push($ID_SFUser,$row->ID_SFUser);
    array_push($folderName,$row->folderName);
    array_push($folderDescr,$row->folderDescr);
    array_push($imgSFolder,$row->imgSFolder);
    array_push($folderColor,$row->folderColor);
    array_push($folderOrder,$row->folderOrder);
  }
  $data=array(
  'ID_Folder'=>$ID_Folder,
  'ID_SFUser'=>$ID_SFUser,
  'folderName'=>$folderName,
  'imgSFolder'=>$imgSFolder,
  'folderColor'=>$folderColor,
  'folderOrder'=>$folderOrder
  );
  return $data;
}

//coge todos los registros de la tabla S_Sentence en funcion del iduserS
function getSSentence($ID_User){
  $ID_SSentence=array(); $ID_SSUser=array();
  $ID_SFolder=array(); $inputWords=array();
  $posInFolder=array(); $sentenceType=array();
  $isNegative=array(); $sentenceTense=array();
  $sentenceDate=array(); $sentenceFinished=array();
  $intendedSentence=array(); $parseScore=array();
  $parseString=array(); $generatorScore=array();
  $generatorString=array(); $comments=array();
  $userScore=array(); $isPreRec=array();
  $sPreRecText=array(); $sPreRecDate=array();
  $sPreRecImg1=array(); $sPreRecImg2=array();
  $sPreRecImg3=array(); $sPreRecPath=array();
  $inputIds=array(); $isDeleted=array();

  $sql="SELECT * FROM S_Sentence WHERE ID_SSUser=?";
  $query=$this->db->query($sql,$ID_User);

  foreach ($query->result() as $row) {
    array_push($ID_SSentence,$row->ID_SSentence);
    array_push($ID_SSUser,$row->ID_SSUser);
    array_push($ID_SFolder,$row->ID_SFolder);
    array_push($inputWords,$row->inputWords);
    array_push($inputIds,$row->inputIds);
    array_push($posInFolder,$row->posInFolder);
    array_push($sentenceType,$row->sentenceType);
    array_push($isNegative,$row->isNegative);
    array_push($sentenceTense,$row->sentenceTense);
    array_push($sentenceDate,$row->sentenceDate);
    array_push($sentenceFinished,$row->sentenceFinished);
    array_push($intendedSentence,$row->intendedSentence);
    array_push($parseScore,$row->parseScore);
    array_push($parseString,$row->parseString);
    array_push($generatorScore,$row->generatorScore);
    array_push($generatorString,$row->generatorString);
    array_push($comments,$row->comments);
    array_push($userScore,$row->userScore);
    array_push($isPreRec,$row->isPreRec);
    array_push($sPreRecText,$row->sPreRecText);
    array_push($sPreRecDate,$row->sPreRecDate);
    array_push($sPreRecImg1,$row->sPreRecImg1);
    array_push($sPreRecImg2,$row->sPreRecImg2);
    array_push($sPreRecImg3,$row->sPreRecImg3);
    array_push($sPreRecPath,$row->sPreRecPath);
    array_push($isDeleted,$row->isDeleted);
  }
  $data=array(
    'ID_SSentence'=>$ID_SSentence,
    'ID_SSUser'=>$ID_SSUser,
    'ID_SFolder'=>$ID_SFolder,
    'inputWords'=>$inputWords,
    'inputIds'=>$inputIds,
    'posInFolder'=>$posInFolder,
    'sentenceType'=>$sentenceType,
    'isNegative'=>$isNegative,
    'sentenceTense'=>$sentenceTense,
    'sentenceDate'=>$sentenceDate,
    'sentenceFinished'=>$sentenceFinished,
    'intendedSentence'=>$intendedSentence,
    'parseScore'=>$parseScore,
    'parseString'=>$parseString,
    'generatorString'=>$generatorString,
    'generatorScore'=>$generatorScore,
    'comments'=>$comments,
    'userScore'=>$userScore,
    'isPreRec'=>$isPreRec,
    'sPreRecText'=>$sPreRecText,
    'sPreRecDate'=>$sPreRecDate,
    'sPreRecImg1'=>$sPreRecImg1,
    'sPreRecImg2'=>$sPreRecImg2,
    'sPreRecImg3'=>$sPreRecImg3,
    'sPreRecPath'=>$sPreRecPath,
    'isDeleted'=>$isDeleted
  );
  return $data;
}

//coge todos los registros de la tabla User en funcion del iduser
function getUser($ID_SUser){
  $ID_User=array(); $ID_USU=array();
  $ID_ULanguage=array(); $ID_UOrg=array();
  $cfgExpansionVoiceOnline=array(); $cfgExpansionVoiceOnlineType=array();
  $cfgExpansionVoiceOffline=array(); $cfgInterfaceVoiceOnOff=array();
  $cfgInterfaceVoiceMascFem=array(); $cfgInterfaceVoiceOnline=array();
  $cfgInterfaceVoiceOffline=array(); $cfgVoiceOfflineRate=array();
  $cfgExpansionLanguage=array(); $errorTemp=array();

  $sql="SELECT * FROM User WHERE ID_USU=?";
  $query=$this->db->query($sql,$ID_SUser);

  foreach ($query->result() as $row) {
    array_push($ID_USU,$row->ID_USU);
    array_push($ID_User,$row->ID_User);
    array_push($ID_ULanguage,$row->ID_ULanguage);
    array_push($ID_UOrg,$row->ID_UOrg);
    array_push($cfgExpansionVoiceOnline,$row->cfgExpansionVoiceOnline);
    array_push($cfgExpansionVoiceOnlineType,$row->cfgExpansionVoiceOnlineType);
    array_push($cfgExpansionVoiceOffline,$row->cfgExpansionVoiceOffline);
    array_push($cfgInterfaceVoiceOnOff,$row->cfgInterfaceVoiceOnOff);
    array_push($cfgInterfaceVoiceMascFem,$row->cfgInterfaceVoiceMascFem);
    array_push($cfgInterfaceVoiceOnline,$row->cfgInterfaceVoiceOnline);
    array_push($cfgInterfaceVoiceOffline,$row->cfgInterfaceVoiceOffline);
    array_push($cfgVoiceOfflineRate,$row->cfgVoiceOfflineRate);
    array_push($cfgExpansionLanguage,$row->cfgExpansionLanguage);
    array_push($errorTemp,$row->errorTemp);
  }
  $data=array(
    'ID_USU'=>$ID_USU,
    'ID_User'=>$ID_User,
    'ID_ULanguage'=>$ID_ULanguage,
    'ID_UOrg'=>$ID_UOrg,
    'cfgExpansionVoiceOnline'=>$cfgExpansionVoiceOnline,
    'cfgExpansionVoiceOnlineType'=>$cfgExpansionVoiceOnlineType,
    'cfgExpansionVoiceOffline'=>$cfgExpansionVoiceOffline,
    'cfgInterfaceVoiceOnOff'=>$cfgInterfaceVoiceOnOff,
    'cfgInterfaceVoiceMascFem'=>$cfgInterfaceVoiceMascFem,
    'cfgInterfaceVoiceOnline'=>$cfgInterfaceVoiceOnline,
    'cfgInterfaceVoiceOffline'=>$cfgInterfaceVoiceOffline,
    'cfgVoiceOfflineRate'=>$cfgVoiceOfflineRate,
    'cfgExpansionLanguage'=>$cfgExpansionLanguage,
    'errorTemp'=>$errorTemp
  );
  return $data;
}
}

 ?>
