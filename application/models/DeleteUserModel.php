<?php

class DeleteUserModel extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library("session");
    }

    function deleteUserBD() {
       
        $superUserid = $this->session->userdata('idsu');
        $userid = $this->session->userdata('idusu');
        $this->DeleteAll($superUserid,$userid);
    }
    
    function deleteUser($idUsuario){
                $this->db->query("DELETE FROM User WHERE ID_User = ?", $idUsuario);
    }
    
    function deleteSuperUser($idSuperUser){
                $this->db->query("DELETE FROM Superuser WHERE ID_SU = ?", $idSuperUser);
    }
    
    function DeleteAll($superUserid,$userid){
               $this->deleteUser($userid);
               $this->deleteSuperUser($superUserid);  
    }
    
}
