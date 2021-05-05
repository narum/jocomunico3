<?php

class Email_model extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
    }
    
    public function getListValidEmails()
    {
        $aux = array();
        
        $this->db->where_in('UserValidated', array('1', '2'));
        $this->db->join('User', 'User.ID_USU = SuperUser.cfgDefUser', 'left');
        $this->db->group_by('SuperUser.email');
        $this->db->select('email, ID_ULanguage as lang, SUname as name');
        $query = $this->db->get('SuperUser');

        if ($query->num_rows() > 0) {
            $aux = $query->result();
        }
        
        return $aux; 
    }
        
}
?>