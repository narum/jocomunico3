<?php
class InterfaceModel extends CI_Model{

    public function _construct(){
        parent::__construct();
        $this->load->database();
    }


    //Function to get the value of the ID language of the user.
    private function getLanguageUserId(){

          //Choose the option where the user id has the value 1, this is the default value.
          $sql = "SELECT ID_ULanguage FROM user WHERE ID_User=3";
          $query= $this->db->query($sql);
          foreach ($query->result() as $row){
              return $row->ID_ULanguage;
          }

          /* Query Builder*/

          /*$this->db->select('ID_ULanguage, ID_USU');

          $idlanguage = '2';
          $this->db->where("ID_USU=", $idlanguage);
          $query= $this->db->get('user');
          $row = $query->row_array();
          return $row['ID_ULanguage'];*/

          /* End Query Builder*/
      }


    //Function to see the elements which the beginning starts with M.
    public function seeElementsWithBeginningStartsInM(){
        $arrayNames = array();
        $language = $this->getLanguageUserId();

        if($language == '1'){
            $tablename = 'nameca';
        }

        else{
            $tablename = 'namees';
        }

        $sql = "SELECT DISTINCT nomtext FROM $tablename WHERE nomtext LIKE 'm%'";
        $query= $this->db->query($sql);
        foreach($query->result() as $row){
            array_push($arrayNames, $row->nomtext);
        }

        /* Query Builder */

        /*$this->db->select('nomtext');
        $this->db->distinct(TRUE);
        $this->db->like('nomtext', 'm', 'after');

        if($language === '1'){
            $query =$this->db->get('nameca');
        }
        else{
            $query =$this->db->get('namees');
        }

        foreach ($query->result_array() as $row){

            $nomtext= $row['nomtext'];
            array_push($arrayNames, $nomtext);

        }*/

        /* End Query Builder*/

        return $arrayNames;


    }

    //Get info of the pictogram. We need to pass the text that it's related with the pictogram.
    private function getInfoPictogram($pictotext){

        $languageid = $this->getLanguageUserId();
        $sql = "SELECT pictoid,languageid,pictotext FROM pictogramslanguage WHERE pictotext=? AND languageid=?";
        $query=$this->db->query($sql,array($pictotext,$languageid));
        foreach ($query->result() as $row) {
          $data=array('id_picto'=>$row->pictoid,
          'id_language'=>$this->$languageid,
          'pictotext'=>$row->pictotext,);
        }


       return $data;
    }

    //Get all the data of Pictogram.
    public function getDataPictogram($pictotext){

        $infopictogram = $this->getInfoPictogram($pictotext);
        $sql='SELECT imgPicto FROM pictograms WHERE pictoid=?';
        $query=$this->db->query($sql,$infopictogram['id_picto']);
        foreach($query->result() as $row){
          $pictoimage=$row->imgPicto;
        }

        $data = array('idpicto'=>$infopictogram['id_picto'],
          'idlanguage'=>$infopictogram['id_language'],
          'pictotext'=>$infopictogram['pictotext'],
          'pictoimage'=>$pictoimage);

        return $data;

    }


    // SQL Sentence to create the table in the database:
    // CREATE TABLE Historial (ID_Hist int NOT NULL AUTO_INCREMENT, ID_User int NOT NULL, pictoid int NOT NULL, ID_Language int NOT NULL, PRIMARY KEY (ID_Hist), FOREIGN KEY (ID_User) REFERENCES User(ID_User), FOREIGN KEY (pictoid) REFERENCES Pictograms(pictoid), FOREIGN KEY (ID_Language) REFERENCES Languages(ID_Language))

    //Check if the value is inside the database table.
    public function checkPictogramInsideTable($idpicto){
      $sql= 'SELECT pictoid FROM historial WHERE pictoid=?';
      $query= $this->db->query($sql,array($idpicto));
      return $query->num_rows();
    }

    //Insert the pictogram info in the table Historial
    public function insertPictogramInfo($data){
      $id_picto=$data['idpicto'];
      $id_user=3;
      $id_language= $this->getLanguageUserId();
      $numrows = $this->checkPictogramInsideTable($id_picto);
      if($numrows <= 0){
        $sql='INSERT INTO historial(ID_User,pictoid,ID_Language) VALUES(?,?,?)';
        $this->db->query($sql,array($id_user,$id_picto,$id_language));
      }
    }










}
