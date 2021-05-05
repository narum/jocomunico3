<?php
class BackupClean extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->database();

    }
    //lanza todas las funciones de borrado en un determinado orden(NO TOCAR EL ORDEN)
    function LaunchClean($idusu, $langid){
      $this->cleanAdjectives($idusu, $langid);
      $this->cleanNames($idusu, $langid);
      $this->cleanVerbs($idusu, $langid);
      $this->cleanPictogramLanguage($idusu);
      $this->cleanImages($idusu);
      $this->cleanCells($idusu);
      $this->cleanRBoardCell($idusu);
      $this->cleanBoards($idusu);
      $this->cleanGroupBoards($idusu);
      $this->cleanRSHistoricPictograms($idusu);
      $this->cleanSHistoric($idusu);
      $this->cleanRSSentencePictograms($idusu);
      $this->cleanSFolder($idusu);
      $this->cleanStats($idusu);
      // $this->cleanSSentence($idusu); ALREADY DELETED IN SFOLDER
      $this->cleanPictograms($idusu);
    }
    function LaunchParcialClean_images($idusu){
      $this->cleanImages($idusu);
    }
    function LaunchParcialClean_Pictograms($idusu, $langid){
      $this->cleanAdjectives($idusu, $langid);
      $this->cleanNames($idusu, $langid);
      $this->cleanVerbs($idusu, $langid);
      $this->cleanPictogramLanguage($idusu);
      $this->cleanPictograms($idusu);
    }
      //llama a la recuperacion parcial de la carpetas tematicas
    function LaunchParcialClean_Folder($idusu){
      $this->CleanRSHistoricPictograms($idusu);
      $this->cleanRSSentencePictograms($idusu);
      $this->cleanSHistoric($idusu);
      $this->cleanSFolder($idusu);
    }
      //llama a la recuperacion parcial de paneles
    function LaunchParcialClean_panels($idusu){
      $this->cleanCells($idusu);
      $this->cleanRBoardCell($idusu);
      $this->cleanBoards($idusu);
      $this->cleanGroupBoards($idusu);
    }
    //borrado de adjetives y adjectiveClass
    private function cleanAdjectives($ID_User, $ID_Language){

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
      $sql="DELETE $maintable,$classtable FROM
      $maintable INNER JOIN Pictograms ON $maintable.adjid = Pictograms.pictoid INNER JOIN
      $classtable ON $maintable.adjid = $classtable.adjid AND Pictograms.ID_PUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de names y nameClass
    private function cleanNames($ID_User, $ID_Language){

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
      $sql="DELETE $maintable,$classtable FROM
      $maintable INNER JOIN Pictograms ON $maintable.nameid = Pictograms.pictoid INNER JOIN
      $classtable ON $maintable.nameid = $classtable.nameid AND Pictograms.ID_PUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de verb, verbconjugation y pattern
    private function cleanVerbs($ID_User, $ID_Language){

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
      $sql="DELETE $maintable, $conjtable, $patterntable FROM
      $maintable INNER JOIN Pictograms ON $maintable.verbid = Pictograms.pictoid INNER JOIN
      $conjtable ON $maintable.verbid = $conjtable.verbid INNER JOIN
      $patterntable ON $maintable.verbid = $patterntable.verbid  AND Pictograms.ID_PUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla Boards
    private function cleanBoards($ID_User){
      $sql="DELETE Boards FROM Boards INNER JOIN GroupBoards ON GroupBoards.ID_GB = Boards.ID_GBBoard AND GroupBoards.ID_GBUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla GroupBoards
    private function cleanGroupBoards($ID_User){
      $sql="DELETE FROM GroupBoards WHERE ID_GBUser=?";
      $this->db->query($sql,$ID_User);
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
    //borrado de la tabla cells
    private function cleanCells($ID_User){
      $boardkey=$this->getBoardkey($ID_User);
      for($i=0;$i<count($boardkey);$i++){
      $sql="DELETE Cell FROM Cell INNER JOIN R_BoardCell ON
      R_BoardCell.ID_RCell = Cell.ID_Cell AND R_BoardCell.ID_RBoard=?";
      $this->db->query($sql,$boardkey[$i]);
     }
    }
    //borrado de la tabla Images
    private function cleanImages($idUser){
        
        $imgPaths = $this->db->query(
        "SELECT imgPath FROM Images WHERE ID_ISU = ?", $idUser); 
        
        if (!(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')) {
            foreach($imgPaths->result() as $row){
                // if the image is only being used by that user, delete it from the folder
                $this->db->where('imgPath', $row->imgPath);
                $query2 = $this->db->get('Images');

                if ($query2->num_rows() <= 1) {
                    unlink($row->imgPath);
                }                    
            }
        }
        $this->db->query("DELETE FROM Images WHERE ID_ISU = ?", $idUser); 
    }
    //borrado de la tabla Pictograms
    private function cleanPictograms($ID_User){
      $sql="DELETE FROM Pictograms WHERE ID_PUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla PictogramsLanguage
    private function cleanPictogramLanguage($ID_User){
      $sql="DELETE PictogramsLanguage FROM PictogramsLanguage INNER JOIN Pictograms
       ON PictogramsLanguage.pictoid = Pictograms.pictoid AND Pictograms.ID_PUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla R_BoardCell
    private function cleanRBoardCell($ID_User){
      $boardkey=$this->getBoardkey($ID_User);
      for($i=0;$i<count($boardkey);$i++){
        $sql="DELETE R_BoardCell FROM R_BoardCell INNER JOIN Cell ON
        R_BoardCell.ID_RCell = Cell.ID_Cell AND R_BoardCell.ID_RBoard=?";
        $this->db->query($sql,$boardkey[$i]);
      }
    }
    //borrado de la tabla R_S_HistoricPictograms
    private function cleanRSHistoricPictograms($ID_User){
      $sql="DELETE R_S_HistoricPictograms FROM R_S_HistoricPictograms INNER JOIN S_Historic ON
      R_S_HistoricPictograms.ID_RSHPSentence = S_Historic.ID_SHistoric AND S_Historic.ID_SHUser=?";
      $this->db->query($sql,$ID_User);
    }
    private function cleanSHistoric($ID_User){
      $sql="DELETE S_Historic FROM S_Historic WHERE ID_SHUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla R_S_SentencePictograms
    private function cleanRSSentencePictograms($ID_User){
      $sql="DELETE R_S_SentencePictograms FROM R_S_SentencePictograms INNER JOIN S_Sentence ON
      R_S_SentencePictograms.ID_RSSPSentence = S_Sentence.ID_SSentence AND S_Sentence.ID_SSUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla S_Folder
    private function cleanSFolder($ID_User){
      $sql="DELETE FROM S_Folder WHERE ID_SFUser=?";
      $this->db->query($sql,$ID_User);
      $sql1="DELETE FROM S_Sentence WHERE ID_SSUser=?";
      $this->db->query($sql1,$ID_User);
    }
    //borrado de la tabla S_Sentence
    private function cleanSSentence($ID_User){
      $sql="DELETE FROM S_Sentence WHERE ID_SSUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de las estadísticas para la predicción
    private function cleanStats($ID_User){
      $sql="DELETE FROM P_StatsUserPicto WHERE ID_PSUPUser=?";
      $this->db->query($sql,$ID_User);
      $sql1="DELETE FROM P_StatsUserPictox2 WHERE ID_PSUP2User=?";
      $this->db->query($sql1,$ID_User);
      $sql2="DELETE FROM P_StatsUserPictox3 WHERE ID_PSUP3User=?";
      $this->db->query($sql2,$ID_User);
      $sql3="DELETE FROM KeyboardStatsx1 WHERE ID_KSUser=?";
      $this->db->query($sql3,$ID_User);
      $sql4="DELETE FROM KeyboardStatsx2 WHERE ID_KS2User=?";
      $this->db->query($sql4,$ID_User);
    }
  }
?>
