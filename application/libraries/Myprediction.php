<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Myprediction {

    var $idusu;
    var $languageid;
    var $langabbr;
    var $expansion;
    var $predbarnum;
    
    public function __construct() {}
    
    public function initialise($idusu, $languageid, $langabbr, $expansion, $predbarnum) 
    {
        $this->idusu = $idusu;
        $this->languageid = $languageid;
        $this->langabbr = $langabbr;
        $this->expansion = $expansion;
        $this->predbarnum = $predbarnum;
    }
    
    function getPrediction($kbword) {
        
        $numpar = $this->getcountElem();
        
        if ($kbword != "") {
            
            if ($numpar == 0) {
                return $this->getKBRecommenderX1($kbword);
            }
            else {
                return $this->getKBRecommenderX2($kbword);
            }
        }
        
        else {
            if ($numpar == 0) {
                return $this->getRecommenderX1();
            }
            else if ($numpar == 1) {
                return $this->getRecommenderX2();
            }
            else {
                return $this->getRecommenderX3();     
            }
        }
    }      
    
    function getRecommenderX1() {  
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $CI->Recommender->initialise($this->idusu, $this->languageid, $this->langabbr, $this->expansion, $this->predbarnum);
        $output = $CI->Recommender->getRecommenderX1();
        return $output;                  
    }
    
    function getRecommenderX2() {
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $CI->Recommender->initialise($this->idusu, $this->languageid, $this->langabbr, $this->expansion, $this->predbarnum);
        $output = $CI->Recommender->getRecommenderX2();
        return $output;                 
    }
    
    function getRecommenderX3() {
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $CI->Recommender->initialise($this->idusu, $this->languageid, $this->langabbr, $this->expansion, $this->predbarnum);
        $output = $CI->Recommender->getRecommenderX3();
        return $output;      
    }
    
    function getKBRecommenderX1($kbword) {  
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $CI->Recommender->initialise($this->idusu, $this->languageid, $this->langabbr, $this->expansion, $this->predbarnum);
        $output = $CI->Recommender->getKeyboardRecommenderX1($kbword);
        return $output;                  
    }
    
    function getKBRecommenderX2($kbword) {
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $CI->Recommender->initialise($this->idusu, $this->languageid, $this->langabbr, $this->expansion, $this->predbarnum);
        $output = $CI->Recommender->getKeyboardRecommenderX2($kbword);
        return $output;                 
    }
    
    
    function getcountElem(){
        $CI = &get_instance();
        $CI->load->model('Recommender');
        $CI->Recommender->initialise($this->idusu, $this->languageid, $this->langabbr, $this->expansion, $this->predbarnum);
        $output = $CI->Recommender->getcountElem();
        return $output;  
    }       
}

/* End of file Myprediction.php */