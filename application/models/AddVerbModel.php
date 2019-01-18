<?php

class AddVerbModel extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library("session");
    }

     function conjugateVerb($verb) {
        $verb = str_replace(' ', '', $verb);
        $language = $this->session->userdata('ulangabbr');//CA or ES
        //$aux = $this->getInfinitive($language, $verb);
        //$infinitive = $aux[0]->verb;
        return $this->getConjugations($language, $verb);
    }

     function getInfinitive($language, $verb){
        if ($language == 'CA'){
            $language = 'cat';
        }else if ($language == 'ES'){
            $language = 'spa';
        }
         return $this->getInfinitiveVerb($verb, $language);
    }

     function getInfinitiveVerb($verb, $language){
         $url = 'http://api.verbix.com/finder/json/eba16c29-e22e-11e5-be88-00089be4dcbc/v2/'.$language.'/'.$verb;
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_URL, $url);
         $result = curl_exec($ch);
         curl_close($ch);
         $obj = json_decode($result);
         echo $obj->access_token;
         return $obj;
     }

     function getConjugations($language, $infinitive){
         $verb = new Verb();
         if ($language == 'CA'){
             $url = 'http://api.verbix.com/conjugator/html?language=cat&tableurl=http://tools.verbix.com/webverbix/personal/template.htm&verb='.$infinitive;
         }else if ($language == 'ES'){
             $url = 'http://api.verbix.com/conjugator/html?language=spa&tableurl=http://tools.verbix.com/webverbix/personal/template.htm&verb='.$infinitive;
         }
         $xpath = $this->getXpathConjugations($url);
         $this->getPresent($verb, $xpath);
         $this->getImperfecto($verb, $xpath);
         $this->getPasado($verb, $xpath);
         $this->getFuturo($verb, $xpath);
         $this->getPrsubj($verb, $xpath);
         $this->getImpsubj($verb, $xpath);
         $this->getImperativo($verb, $xpath);
         $this->getFormasNoPersonales($verb, $xpath);
         return $verb;
     }

    function getXpathConjugations($url){
        $filepath = "";
        $Fname = "conj-".$this->session->userdata('idusu').".html";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filepath = "C:\\xampp\\htdocs\\Temp\\".$Fname;
        }
        else {
            $filepath = "./Temp/".$Fname;
        }
                
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
        $html = curl_exec($curl); curl_close($curl);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->substituteEntities = TRUE;
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        file_put_contents($filepath, $dom->saveHTML($dom->documentElement));
        $xpath = new DOMXPath($dom);
        return $xpath;
    }

    function getPresent($verb, $xpath){
        $verb->presente->ps1 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[1]/td[1]/table/tr[1]/td[2]')->item(0)->nodeValue;
        $verb->presente->ps2 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[1]/td[1]/table/tr[2]/td[2]')->item(0)->nodeValue;
        $verb->presente->ps3 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[1]/td[1]/table/tr[3]/td[2]')->item(0)->nodeValue;
        $verb->presente->pp1 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[1]/td[1]/table/tr[4]/td[2]')->item(0)->nodeValue;
        $verb->presente->pp2 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[1]/td[1]/table/tr[5]/td[2]')->item(0)->nodeValue;
        $verb->presente->pp3 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[1]/td[1]/table/tr[6]/td[2]')->item(0)->nodeValue;
    }

    function getImperfecto($verb, $xpath){
        $verb->imperfecto->ps1 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[2]/td[1]/table/tr[1]/td[2]')->item(0)->nodeValue;
        $verb->imperfecto->ps2 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[2]/td[1]/table/tr[2]/td[2]')->item(0)->nodeValue;
        $verb->imperfecto->ps3 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[2]/td[1]/table/tr[3]/td[2]')->item(0)->nodeValue;
        $verb->imperfecto->pp1 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[2]/td[1]/table/tr[4]/td[2]')->item(0)->nodeValue;
        $verb->imperfecto->pp2 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[2]/td[1]/table/tr[5]/td[2]')->item(0)->nodeValue;
        $verb->imperfecto->pp3 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[2]/td[1]/table/tr[6]/td[2]')->item(0)->nodeValue;
    }

    function getPasado($verb, $xpath){
        $verb->pasado->ps1 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[3]/td[1]/table/tr[1]/td[2]')->item(0)->nodeValue;
        $verb->pasado->ps2 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[3]/td[1]/table/tr[2]/td[2]')->item(0)->nodeValue;
        $verb->pasado->ps3 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[3]/td[1]/table/tr[3]/td[2]')->item(0)->nodeValue;
        $verb->pasado->pp1 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[3]/td[1]/table/tr[4]/td[2]')->item(0)->nodeValue;
        $verb->pasado->pp2 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[3]/td[1]/table/tr[5]/td[2]')->item(0)->nodeValue;
        $verb->pasado->pp3 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[3]/td[1]/table/tr[6]/td[2]')->item(0)->nodeValue;
    }

    function getFuturo($verb, $xpath){
        $verb->futuro->ps1 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[4]/td[1]/table/tr[1]/td[2]')->item(0)->nodeValue;
        $verb->futuro->ps2 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[4]/td[1]/table/tr[2]/td[2]')->item(0)->nodeValue;
        $verb->futuro->ps3 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[4]/td[1]/table/tr[3]/td[2]')->item(0)->nodeValue;
        $verb->futuro->pp1 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[4]/td[1]/table/tr[4]/td[2]')->item(0)->nodeValue;
        $verb->futuro->pp2 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[4]/td[1]/table/tr[5]/td[2]')->item(0)->nodeValue;
        $verb->futuro->pp3 = $xpath->query('/html/body/table[2]/tr[3]/td[1]/table/tr[4]/td[1]/table/tr[6]/td[2]')->item(0)->nodeValue;
    }

    function getPrsubj($verb, $xpath){
        $verb->prsubj->ps1 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[1]/td[1]/table/tr[1]/td[2]')->item(0)->nodeValue;
        $verb->prsubj->ps2 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[1]/td[1]/table/tr[2]/td[2]')->item(0)->nodeValue;
        $verb->prsubj->ps3 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[1]/td[1]/table/tr[3]/td[2]')->item(0)->nodeValue;
        $verb->prsubj->pp1 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[1]/td[1]/table/tr[4]/td[2]')->item(0)->nodeValue;
        $verb->prsubj->pp2 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[1]/td[1]/table/tr[5]/td[2]')->item(0)->nodeValue;
        $verb->prsubj->pp3 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[1]/td[1]/table/tr[6]/td[2]')->item(0)->nodeValue;
    }

    function getImpsubj($verb, $xpath){
        $verb->impsubj->ps1 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[2]/td[1]/table[1]/tr[1]/td[2]')->item(0)->nodeValue;
        $verb->impsubj->ps2 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[2]/td[1]/table[1]/tr[2]/td[2]')->item(0)->nodeValue;
        $verb->impsubj->ps3 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[2]/td[1]/table[1]/tr[3]/td[2]')->item(0)->nodeValue;
        $verb->impsubj->pp1 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[2]/td[1]/table[1]/tr[4]/td[2]')->item(0)->nodeValue;
        $verb->impsubj->pp2 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[2]/td[1]/table[1]/tr[5]/td[2]')->item(0)->nodeValue;
        $verb->impsubj->pp3 = $xpath->query('/html/body/table[2]/tr[3]/td[2]/table/tr[2]/td[1]/table[1]/tr[6]/td[2]')->item(0)->nodeValue;
    }

    function getImperativo($verb, $xpath){
        $verb->imperativo->ps2 = $xpath->query('/html/body/table[2]/tr[5]/td[2]/table/tr[1]/td[2]')->item(0)->nodeValue;
        $verb->imperativo->ps3 = $xpath->query('/html/body/table[2]/tr[5]/td[2]/table/tr[2]/td[2]')->item(0)->nodeValue;
        $verb->imperativo->pp1 = $xpath->query('/html/body/table[2]/tr[5]/td[2]/table/tr[3]/td[2]')->item(0)->nodeValue;
        $verb->imperativo->pp2 = $xpath->query('/html/body/table[2]/tr[5]/td[2]/table/tr[4]/td[2]')->item(0)->nodeValue;
        $verb->imperativo->pp3 = $xpath->query('/html/body/table[2]/tr[5]/td[2]/table/tr[5]/td[2]')->item(0)->nodeValue;
    }

    function getFormasNoPersonales($verb, $xpath){
        $verb->infinitivo = $xpath->query('/html/body/table[2]/tr[1]/td[1]/span[1]')->item(0)->nodeValue;
        $verb->participio = $xpath->query('/html/body/table[2]/tr[1]/td[1]/span[2]')->item(0)->nodeValue;
        $verb->gerundio = $xpath->query('/html/body/table[2]/tr[1]/td[1]/span[3]')->item(0)->nodeValue;
    }

    //Check If the verb is already on the DB.
    function verbExist($verb){
        $verb = str_replace(' ', '', $verb);
        $language = $this->session->userdata('ulangabbr');//CA or ES
        $userid = $this->session->userdata('idusu');
        if ($language == 'CA'){
            $verbTable = 'VerbCA';
        }else if ($language == 'ES'){
            $verbTable = 'VerbES';
        }
        $sql = "SELECT verbid FROM ".$verbTable." JOIN Pictograms ON verbid = pictoid WHERE ID_PUser = ? AND verbtext ='".$verb."';";
        $query = $this->db->query($sql, $userid);
        $res = $query->result();
        if(count($res) < 1){
            return false;
        }else {
            return true;
        }
    }

    function insertData($request){
        $imgPicto = $request->img;
        $imgFolder = $request->folder;
        $pictotext = $request->verb;
        $isEdit = $request->isEdit;
        if($isEdit == false){
            $pictoid = $this->insertPictogram($imgPicto, $imgFolder);
        }else if($isEdit == true){
            $pictoid = $request->verbID;
            $this->updatePictogram($pictoid, $imgPicto, $imgFolder);
        }
        $this->insertPictogramsLanguage($pictoid, $pictotext, $isEdit);
        $this->insertVerb($pictoid, $pictotext, $isEdit);
        $this->insertVerbConjugation($pictoid, $request->conjugations, $isEdit);
        return $this->insertVerbPatterns($pictoid, $request->patterns, $request->pronominal, $isEdit);
    }

    function insertPictogram($imgPicto, $imgFolder){
        $userid = $this->session->userdata('idusu');
        $data = array(
            'ID_PUser' => $userid,
            'pictoType' => 'verb',
            'supportsExpansion' => '1',
            'imgPicto' => $imgPicto,
            'imgFolder' => $imgFolder
        );
        $this->db->insert('Pictograms', $data);
        $pictoid = $this->db->insert_id();
        return $pictoid;
    }

    function updatePictogram($pictoid, $imgPicto, $imgFolder){
        $userid = $this->session->userdata('idusu');
        $data = array(
            'ID_PUser' => $userid,
            'pictoType' => 'verb',
            'supportsExpansion' => '1',
            'imgPicto' => $imgPicto,
            'imgFolder' => $imgFolder
        );
        $this->db->where('pictoid', $pictoid);
        $this->db->update('Pictograms', $data);
    }

    function insertPictogramsLanguage($pictoid, $pictotext, $isEdit){
        $languageid = ($this->session->userdata('ulangabbr') == 'CA' ? 1:  2);
        $data = array(
            'pictoid' => $pictoid,
            'languageid' => $languageid,
            'insertdate' => mdate("%Y/%m/%d", time()),
            'pictotext' => $pictotext,
            'pictofreq' => 10.000
        );
        if($isEdit == false){
            $this->db->insert('PictogramsLanguage', $data);
        } else if($isEdit == true){
            $this->db->where('pictoid', $pictoid);
            $this->db->update('PictogramsLanguage', $data);
        }
    }

    function insertVerb($verbid, $verbtext, $isEdit){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'VerbCA': 'VerbES');
        $data = array(
            'verbid' => $verbid,
            'verbtext' => $verbtext,
            'actiu' => '1'
        );
        if($isEdit == false){
            $this->db->insert($table, $data);
        } else if($isEdit == true){
            $this->db->where('verbid', $verbid);
            $this->db->update($table, $data);
        }
    }

    function insertVerbConjugation($verbid, $conjugations, $isEdit){
        $language = $this->session->userdata('ulangabbr');
        $this->insertConjugations($verbid, $conjugations->presente, $isEdit);
        $this->insertConjugations($verbid, $conjugations->imperfecto, $isEdit);
        if($language == 'ES'){$this->insertConjugations($verbid, $conjugations->pasado, $isEdit);}
        $this->insertConjugations($verbid, $conjugations->futuro, $isEdit);
        $this->insertConjugations($verbid, $conjugations->prsubj, $isEdit);
        $this->insertConjugations($verbid, $conjugations->impsubj, $isEdit);
        $this->insertImperatiuConjugations($verbid, $conjugations->imperativo, $isEdit);
        $this->insertFormasNoPersonales($verbid, $conjugations->formasNoPersonales, $isEdit);
    }
    function insertConjugations($verbid, $conj, $isEdit){
        $this->insertTemp($verbid, $conj->name, 1, 'sing', $conj->persona->ps1, $isEdit);
        $this->insertTemp($verbid, $conj->name, 2, 'sing', $conj->persona->ps2, $isEdit);
        $this->insertTemp($verbid, $conj->name, 3, 'sing', $conj->persona->ps3, $isEdit);
        $this->insertTemp($verbid, $conj->name, 1, 'pl', $conj->persona->pp1, $isEdit);
        $this->insertTemp($verbid, $conj->name, 2, 'pl', $conj->persona->pp2, $isEdit);
        $this->insertTemp($verbid, $conj->name, 3, 'pl', $conj->persona->pp3, $isEdit);
    }
    function insertImperatiuConjugations($verbid, $conj, $isEdit){
        $this->insertTemp($verbid, $conj->name, 2, 'sing', $conj->persona->ps2, $isEdit);
        $this->insertTemp($verbid, $conj->name, 3, 'sing', $conj->persona->ps3, $isEdit);
        $this->insertTemp($verbid, $conj->name, 1, 'pl', $conj->persona->pp1, $isEdit);
        $this->insertTemp($verbid, $conj->name, 2, 'pl', $conj->persona->pp2, $isEdit);
        $this->insertTemp($verbid, $conj->name, 3, 'pl', $conj->persona->pp3, $isEdit);
    }
    function insertTemp($verbid, $tense, $pers, $singpl, $verbConj, $isEdit){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'VerbConjugationCA': 'VerbConjugationES');
        $data = array(
            'verbid' => $verbid,
            'tense' => $tense,
            'pers' => $pers,
            'singpl' => $singpl,
            'verbconj' => $verbConj
        );
        if($isEdit == false){
            $this->db->insert($table, $data);
        } else if($isEdit == true){
            $this->db->where('verbid', $verbid);
            $this->db->where('tense', $tense);
            $this->db->where('pers', $pers);
            $this->db->where('singpl', $singpl);
            $this->db->update($table, $data);
        }
    }
    function insertFormasNoPersonales($verbid, $conj, $isEdit){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'VerbConjugationCA': 'VerbConjugationES');
        $data = array(
            'verbid' => $verbid,
            'tense' => 'infinitiu',
            'pers' => 0,
            'singpl' => 'sing',
            'verbconj' => $conj->infinitivo
        );
        if($isEdit == false){
            $this->db->insert($table, $data);
        } else if($isEdit == true){
            $this->db->where('verbid', $verbid);
            $this->db->where('tense', 'infinitiu');
            $this->db->where('pers', 0);
            $this->db->where('singpl', 'sing');
            $this->db->update($table, $data);
        }
        $data = array(
            'verbid' => $verbid,
            'tense' => 'gerundi',
            'pers' => 0,
            'singpl' => 'sing',
            'verbconj' => $conj->gerundio
        );
        if($isEdit == false){
            $this->db->insert($table, $data);
        } else if($isEdit == true){
            $this->db->where('verbid', $verbid);
            $this->db->where('tense', 'gerundi');
            $this->db->where('pers', 0);
            $this->db->where('singpl', 'sing');
            $this->db->update($table, $data);
        }
        $data = array(
            'verbid' => $verbid,
            'tense' => 'participi',
            'pers' => 0,
            'singpl' => 'sing',
            'verbconj' => $conj->participio
        );
        if($isEdit == false){
            $this->db->insert($table, $data);
        } else if($isEdit == true){
            $this->db->where('verbid', $verbid);
            $this->db->where('tense', 'participi');
            $this->db->where('pers', 0);
            $this->db->where('singpl', 'sing');
            $this->db->update($table, $data);
        }
    }

    function insertVerbPatterns($verbid, $patterns, $pronominal, $isEdit){
        if($isEdit == true){
            $this->deletePatterns($verbid);
        }
        foreach ($patterns as $pattern){
            $this->insertPattern($verbid, $pattern, $pronominal);
        }
    }

    function deletePatterns($verbid){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'PatternCA': 'PatternES');
        $this->db->query("DELETE FROM ".$table." WHERE verbid = ?", $verbid);
    }
    function insertPattern($verbid, $pattern, $pronominal){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'PatternCA': 'PatternES');
        $pronominal = ($pronominal == 0 ? '0': '1');
        $subverb = $this->subVerb($pattern);
        $data = array(
            'verbid' => $verbid,
            'pronominal' => $pronominal,
            'pseudoimpersonal' => '0',
            'copulatiu' => '0',
            'tipusfrase' => 'enunciativa',
            'defaulttense' => $pattern->Patron->defaulttense,
            'subj' => $pattern->Patron->subj,
            'subjdef' => $pattern->Patron->subjdef,
            'theme' => $pattern->CD->priority,
            'themetipus' => $pattern->CD->type,
            'themedef' => $pattern->CD->themedef,
            'themeprep' => $pattern->CD->preposition,
            'receiver' => $pattern->Receiver->priority,
            'receiverdef' => $pattern->Receiver->receiverdef,
            'receiverprep' => $pattern->Receiver->preposition,
            'benef' => $pattern->Beneficiary->priority,
            'beneftipus' => $pattern->Beneficiary->type,
            'benefprep' => $pattern->Beneficiary->preposition,
            'acomp' => $pattern->Acomp->priority,
            'acompprep' => $pattern->Acomp->preposition,
            'tool' => $pattern->Tool->priority,
            'toolprep' => $pattern->Tool->preposition,
            'manera' => $pattern->Modo->priority,
            'maneratipus' => $pattern->Modo->type,
            'locto' => $pattern->Locto->priority,
            'loctotipus' => $pattern->Locto->type,
            'loctoprep' => $pattern->Locto->preposition,
            'subverb' => $subverb,
            'exemple' => $pattern->Patron->exemple,
        );
        $this->db->insert($table, $data);
    }

    function subVerb($pattern){
        if ($pattern->CD->type == 'verb' || $pattern->Beneficiary->type == 'verb' || $pattern->Locto->type == 'verb'){
            return '1';
        }else{
            return '0';
        }
    }

    function getAllData($verbid){
        $result = new stdClass();
        $result->verbText = $this->getVerbText($verbid);
        $result->imgPicto = $this->getPictoImg($verbid);
        $result->imgFolder = $this->getPictoImgFolder($verbid);
        $result->pronominal = $this->isPronominal($verbid);
        $result->conjugations = $this->getConjugationsBD($verbid);
        //get Patterns
        $result->patterns = $this->getPatterns($verbid);
        return $result;
    }
    function getVerbText($verbid){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'VerbCA': 'VerbES');
        return $this->db->query("SELECT verbtext FROM ".$table." WHERE verbid = ?", $verbid)->row('verbtext');
    }
    function getPictoImg($verbid){
        return $this->db->query("SELECT imgPicto FROM Pictograms WHERE pictoid = ?", $verbid)->row('imgPicto');
    }
    function getPictoImgFolder($verbid){
        return $this->db->query("SELECT imgFolder FROM Pictograms WHERE pictoid = ?", $verbid)->row('imgFolder');
    }
    function isPronominal($verbid){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'PatternCA': 'PatternES');
        return $this->db->query("SELECT pronominal FROM ".$table." WHERE verbid = ?", $verbid)->row('pronominal');
    }
    function getConjugationsBD($verbid){
        $verb = new Verb();
        $this->getTimeBD($verb->presente, $verbid, 'present');
        $this->getTimeBD($verb->imperfecto, $verbid, 'imperfecte');
        $this->getTimeBD($verb->pasado, $verbid, 'passat');
        $this->getTimeBD($verb->futuro, $verbid, 'futur');
        $this->getTimeBD($verb->prsubj, $verbid, 'prsubj');
        $this->getTimeBD($verb->impsubj, $verbid, 'impsubj');
        $this->getImperativoBD($verb->imperativo, $verbid);
        $this->getFormasNoPersonalesBD($verb, $verbid);
        return $verb;
    }

    function getTimeBD($verbTime, $verbid, $tense){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'VerbConjugationCA': 'VerbConjugationES');
        $verbTime->ps1 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = '".$tense."' AND pers = 1 AND singpl = 'sing'", $verbid)->row('verbconj');
        $verbTime->ps2 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = '".$tense."' AND pers = 2 AND singpl = 'sing'", $verbid)->row('verbconj');
        $verbTime->ps3 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = '".$tense."' AND pers = 3 AND singpl = 'sing'", $verbid)->row('verbconj');
        $verbTime->pp1 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = '".$tense."' AND pers = 1 AND singpl = 'pl'", $verbid)->row('verbconj');
        $verbTime->pp2 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = '".$tense."' AND pers = 2 AND singpl = 'pl'", $verbid)->row('verbconj');
        $verbTime->pp3 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = '".$tense."' AND pers = 3 AND singpl = 'pl'", $verbid)->row('verbconj');
    }
    function getImperativoBD($verbTime, $verbid){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'VerbConjugationCA': 'VerbConjugationES');
        $verbTime->ps2 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = 'imperatiu' AND pers = 2 AND singpl = 'sing'", $verbid)->row('verbconj');
        $verbTime->ps3 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = 'imperatiu' AND pers = 3 AND singpl = 'sing'", $verbid)->row('verbconj');
        $verbTime->pp1 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = 'imperatiu' AND pers = 1 AND singpl = 'pl'", $verbid)->row('verbconj');
        $verbTime->pp2 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = 'imperatiu' AND pers = 2 AND singpl = 'pl'", $verbid)->row('verbconj');
        $verbTime->pp3 = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = 'imperatiu' AND pers = 3 AND singpl = 'pl'", $verbid)->row('verbconj');
    }
    function getFormasNoPersonalesBD($verb, $verbid){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'VerbConjugationCA': 'VerbConjugationES');
        $verb->infinitivo = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = 'infinitiu'", $verbid)->row('verbconj');
        $verb->gerundio = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = 'gerundi'", $verbid)->row('verbconj');
        $verb->participio = $this->db->query("SELECT verbconj FROM ".$table." WHERE verbid = ? AND tense = 'participi'", $verbid)->row('verbconj');
    }

    function getPatterns($verbid){
        $table = ($this->session->userdata('ulangabbr') == 'CA' ? 'PatternCA': 'PatternES');
        $sql = "SELECT verbid, pronominal, defaulttense, subj, subjdef, theme, themetipus, themedef, themeprep, receiver, 
        receiverdef, receiverprep, benef, beneftipus, benefprep, acomp, acompprep, tool, toolprep, manera, maneratipus,
        locto, loctotipus, loctoprep, exemple FROM ".$table." WHERE verbid = ?";
        return $this->db->query($sql, $verbid)->result_array();
    }
}
class Verb {
    public $presente;
    public $imperfecto;
    public $pasado;
    public $futuro;
    public $prsubj;
    public $impsubj;
    public $imperativo;
    public $infinitivo;
    public $gerundio;
    public $participio;

    function __construct(){
        $this->presente = new Persona();
        $this->imperfecto = new Persona();
        $this->pasado = new Persona();
        $this->futuro = new Persona();
        $this->prsubj = new Persona();
        $this->impsubj = new Persona();
        $this->imperativo = new Persona();
    }
}
class Persona {
    public $ps1;
    public $ps2;
    public $ps3;
    public $pp1;
    public $pp2;
    public $pp3;

    function __construct(){
    }
}