<?php
class BackupClean extends CI_Model {
    public function __construct(){
        parent::__construct();
        $this->load->library('session');
        $this->load->database();

    }
    //lanza todas las funciones de borrado en un determinado orden(NO TOCAR EL ORDEN)
    function LaunchClean(){
      $this->cleanAdjectives();
      $this->cleanNames();
      $this->cleanVerbs();
      $this->cleanPictogramLanguage();
      $this->cleanSFolder();
      $this->cleanImages();
      $this->cleanCells();
      $this->cleanRBoardCell();
      $this->cleanBoards();
      $this->cleanGroupBoards();
      $this->cleanRSSentecePictograms();
      $this->cleanRSHistoricPictograms();
      $this->cleanSHistoric();
      $this->cleanPictograms();
    //  $this->cleanSSentence();
    }
    function LaunchParcialClean_images(){
      $this->cleanImages();
    }
    function LaunchParcialClean_Pictograms(){
      $this->cleanAdjectives();
      $this->cleanNames();
      $this->cleanVerbs();
      $this->cleanPictogramLanguage();
      $this->cleanPictograms();
    }
      //llama a la recuperacion parcial de la carpetas tematicas
    function LaunchParcialClean_Folder(){
      $this->CleanRSHistoricPictograms();
      $this->cleanRSSentecePictograms();
      $this->cleanSHistoric();
      $this->cleanSFolder();
    }
      //llama a la recuperacion parcial de paneles
    function LaunchParcialClean_panels(){
      $this->cleanCells();
      $this->cleanRBoardCell();
      $this->cleanBoards();
      $this->cleanGroupBoards();
    }
    //borrado de adjetives y adjectiveClass
    private function cleanAdjectives(){
      $ID_Language=$this->session->uinterfacelangauge;
      $ID_User=$this->session->idusu;
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
    private function cleanNames(){
      $ID_Language=$this->session->uinterfacelangauge;
      $ID_User=$this->session->idusu;
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
    private function cleanVerbs(){
      $ID_Language=$this->session->uinterfacelangauge;
      $ID_User=$this->session->idusu;
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
    private function cleanBoards(){
      $ID_User=$this->session->idusu;
      $sql="DELETE Boards FROM Boards INNER JOIN GroupBoards ON GroupBoards.ID_GB = Boards.ID_GBBoard AND GroupBoards.ID_GBUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla GroupBoards
    private function cleanGroupBoards(){
      $ID_User=$this->session->idusu;
      $sql="DELETE FROM GroupBoards WHERE ID_GBUser=?";
      $this->db->query($sql,$ID_User);
    }
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
    //borrado de la tabla cells
    private function cleanCells(){
      $ID_User=$this->session->idusu;
      $boardkey=$this->getBoardkey();
      for($i=0;$i<count($boardkey);$i++){
      $sql="DELETE Cell FROM Cell INNER JOIN R_BoardCell ON
      R_BoardCell.ID_RCell = Cell.ID_Cell AND R_BoardCell.ID_RBoard=?";
      $this->db->query($sql,$boardkey[$i]);
     }
    }
    //borrado de la tabla Images
    private function cleanImages(){
        $idSuperUser = $this->session->idsu;
        
        $imgPaths = $this->db->query(
        "SELECT imgPath FROM Images WHERE ID_ISU = ?", $idSuperUser); 
        
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
        $this->db->query("DELETE FROM Images WHERE ID_ISU = ?", $idSuperUser); 
    }
    //borrado de la tabla Pictograms
    private function cleanPictograms(){
      $ID_User=$this->session->idusu;
      $sql="DELETE FROM Pictograms WHERE ID_PUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla PictogramsLanguage
    private function cleanPictogramLanguage(){
      $ID_User=$this->session->idusu;
      $sql="DELETE PictogramsLanguage FROM PictogramsLanguage INNER JOIN Pictograms
       ON PictogramsLanguage.pictoid = Pictograms.pictoid AND Pictograms.ID_PUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla R_BoardCell
    private function cleanRBoardCell(){
      $ID_User=$this->session->idusu;
      $boardkey=$this->getBoardkey();
      for($i=0;$i<count($boardkey);$i++){
        $sql="DELETE R_BoardCell FROM R_BoardCell INNER JOIN Cell ON
        R_BoardCell.ID_RCell = Cell.ID_Cell AND R_BoardCell.ID_RBoard=?";
        $this->db->query($sql,$boardkey[$i]);
      }
    }
    //borrado de la tabla R_S_HistoricPictograms
    private function cleanRSHistoricPictograms(){
      $ID_User=$this->session->idusu;
      $sql="DELETE R_S_HistoricPictograms FROM R_S_HistoricPictograms INNER JOIN S_Historic ON
      R_S_HistoricPictograms.ID_RSHPSentence = S_Historic.ID_SHistoric AND S_Historic.ID_SHUser=?";
      $this->db->query($sql,$ID_User);
    }
    private function cleanSHistoric(){
      $ID_User=$this->session->idusu;
      $sql="DELETE S_Historic FROM S_Historic WHERE ID_SHUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla R_S_SentencePictograms
    private function cleanRSSentecePictograms(){
      $ID_User=$this->session->idusu;
      $sql="DELETE R_S_SentencePictograms FROM R_S_SentencePictograms INNER JOIN S_Sentence ON
      R_S_SentencePictograms.ID_RSSPSentence = S_Sentence.ID_SSentence AND S_Sentence.ID_SSUser=?";
      $this->db->query($sql,$ID_User);
    }
    //borrado de la tabla S_Folder
    private function cleanSFolder(){
      $ID_User=$this->session->idusu;
      $sql="DELETE FROM S_Folder WHERE ID_SFUser=?";
      $this->db->query($sql,$ID_User);
      $sql1="DELETE FROM S_Sentence WHERE ID_SSUser=?";
      $this->db->query($sql1,$ID_User);
    }
    //borrado de la tabla S_Sentence
    private function cleanSSentence(){
      $ID_User=$this->session->idusu;
      $sql="DELETE FROM S_Sentence WHERE ID_SSUser=?";
      $this->db->query($sql,$ID_User);
    }
  }
?>
