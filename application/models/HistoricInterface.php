<?php

class HistoricInterface extends CI_Model {

    public function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    public function getSFolders($idusu) {
        $this->db->order_by('folderOrder', 'asc');
        $this->db->where('ID_SFUser', $idusu);
        $query = $this->db->get('S_Folder');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }
    public function getHistoric($idusu, $day){

        if($this->getHistorialState() == '0')
            return null;
        else{
            $date = date('Y-m-d', strtotime("-".$day." day"));
        
            $this->db->where('sentenceDate >', $date);
            $this->db->where('isDeleted', 0);
            $this->db->where('ID_SHUser', $idusu);
            $this->db->where('generatorString IS NOT NULL', null, false);
            $this->db->order_by('sentenceDate', 'desc');
            $this->db->order_by('ID_SHistoric', 'desc');
            $query = $this->db->get('S_Historic');

            if ($query->num_rows() > 0) {
                $output = $query->result();
            } else
                $output = null;

            return $output;
        }

    }
    
    public function getPictosHistoric($IDHistoric){
        $this->db->where_in('Pictograms.ID_PUser', array('1', $this->session->userdata('idusu')));
        $this->db->where('ID_SHistoric', $IDHistoric);
        $this->db->join('R_S_HistoricPictograms', 'S_Historic.ID_SHistoric = R_S_HistoricPictograms.ID_RSHPSentence', 'left');
        $this->db->join('Pictograms', 'R_S_HistoricPictograms.pictoid = Pictograms.pictoid', 'left');
        $query = $this->db->get('S_Historic');
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }
    
    public function getCountHistoric($idusu, $day){
        $date = date('Y-m-d', strtotime("-".$day." day"));
        $this->db->where('sentenceDate >', $date);
        $this->db->where('ID_SHUser', $idusu);
        $this->db->where('generatorString IS NOT NULL', null, false);
        $query = $this->db->get('S_Historic');

        return $query->num_rows();
    }
    
    public function getSentenceFolder($idusu, $folder){
        $this->db->where('ID_SSUser', $idusu);
        $this->db->where('ID_SFolder', $folder);
        $this->db->order_by('posInFolder', 'asc');
        $query = $this->db->get('S_Sentence');

        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }
    
    public function getCountSentenceFolder($idusu, $folder){
        $this->db->where('ID_SSUser', $idusu);
        $this->db->where('ID_SFolder', $folder);
        $query = $this->db->get('S_Sentence');

        return $query->num_rows();
    }
    
    public function getPictosFolder($IDSentence){
        $this->db->where_in('Pictograms.ID_PUser', array('1', $this->session->userdata('idusu')));
        $this->db->where('ID_SSentence', $IDSentence);
        $this->db->join('R_S_SentencePictograms', 'S_Sentence.ID_SSentence = R_S_SentencePictograms.ID_RSSPSentence', 'left');
        $this->db->join('Pictograms', 'R_S_SentencePictograms.pictoid = Pictograms.pictoid', 'left');
        $query = $this->db->get('S_Sentence');
        
        if ($query->num_rows() > 0) {
            $output = $query->result();
        } else
            $output = null;

        return $output;
    }

    //Get if Historial is enable or disable
    public function getHistorialState(){
        $idusu = $this->session->userdata('idusu');
        $this->db->where('ID_User', $idusu);
        $query = $this->db->get('User');
        
        $result = $query->result();
        $state = $result[0]->cfgHistorialState;
        
        return $state;
    }

    //Execute update [cfgHistorialState] and new new latest date [cfgLatestHistrorialActivated]
    public function changeHistorialState($newState) {
        //Change enable or disable
        $data = array(
            'cfgHistorialState' => $newState
        );
        $this->db->where('ID_User', $this->session->userdata('idusu'));
        $this->db->update('User', $data);
    }

    public function deleteHistoric(){
        //Delete Historial
        //Change enable or disable
        $data = array(
            'isDeleted' => '1'
        );
        $this->db->where('ID_SHUser', $this->session->userdata('idusu'));
        $this->db->update('S_Historic', $data);
    }

}
