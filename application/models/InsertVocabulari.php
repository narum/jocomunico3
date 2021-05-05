<?php

class InsertVocabulari extends CI_Model {
 
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }        

    private function insertAdjClass($new, $adjid, $class, $langabbr) {
        if ($new) {
            $this->insertIntoAdjClass($adjid, $class, $langabbr);
        }
        else {
            $this->db->where('adjid', $adjid);
            $this->db->delete('AdjClass'.$langabbr);
            $this->insertIntoAdjClass($adjid, $class, $langabbr);
        }
    }
    
    private function insertIntoAdjClass($adjid, $class, $langabbr) {
        for ($i = 0; $i < sizeof($class); $i++) {
            $data = array(
                'adjid' => $adjid,
                'class' => $class[$i]->classType
            );
            $this->db->insert('AdjClass'.$langabbr, $data);
        }
    }        
    
    private function insertAdjective($new, $adjid, $fem, $masc, $mascpl, $fempl, $defaultverb, $subjdef, $langabbr) {
        $data = array(
            'adjid' => $adjid,
            'fem' => $fem,
            'masc' => $masc,
            'mascpl' => $mascpl,
            'fempl' => $fempl,
            'defaultverb' => $defaultverb,
            'subjdef' => $subjdef
        );
        if ($new) $this->db->insert('Adjective'.$langabbr, $data);
        else {
            $this->db->where('adjid', $adjid);
            $this->db->update('Adjective'.$langabbr, $data); 
        }
    }
    
    // NOT IN USE
    private function insertAdverb($new, $advid, $advtext, $langabbr) {
        $data = array(
            'advid' => $advid,
            'class' => $advtext
        );
        if ($new) $this->db->insert('Adverb'.$langabbr, $data);
        else {
            $this->db->where('advid', $advid);
            $this->db->update('Adverb'.$langabbr, $data);
        }
    }
    
    // NOT IN USE
    private function insertAdvType($new, $advid, $type, $langabbr) {
        $data = array(
            'advid' => $advid,
            'type' => $type
        );
        if ($new) $this->db->insert('AdvType'.$langabbr, $data);
        else {
            $this->db->where('advid', $advid);
            $this->db->update('AdvType'.$langabbr, $data);
        }
    }
    
    // NOT IN USE
    private function insertModifier($new, $modid, $masc, $fem, $mascpl, $fempl, $negatiu, $type, $scope, $langabbr) {
        $data = array(
            'modid' => $modid,
            'masc' => $masc,
            'fem' => $fem,
            'mascpl' => $mascpl,
            'fempl' => $fempl,
            'negatiu' => $negatiu,
            'type' => $type,
            'scope' => $scope
        );
        if ($new) $this->db->insert('Modifier'.$langabbr, $data);
        else {
            $this->db->where('modid', $modid);
            $this->db->update('Modifier'.$langabbr, $data);
        }
    }
    
    private function insertName($new, $nameid, $nomtext, $mf, $singpl, $contabincontab, $determinat, $ispropernoun, $defaultverb, $plural, $femeni, $fempl, $langabbr) {
        $data = array(
            'nameid' => $nameid,
            'nomtext' => $nomtext,
            'mf' => $mf,
            'singpl' => $singpl,
            'contabincontab' => $contabincontab,
            'determinat' => $determinat,
            'ispropernoun' => $ispropernoun,
            'defaultverb' => $defaultverb,
            'plural' => $plural,
            'femeni' => $femeni,
            'fempl' => $fempl
        );
        if ($new) $this->db->insert('Name'.$langabbr, $data);
        else {
            $this->db->where('nameid', $nameid);
            $this->db->update('Name'.$langabbr, $data);
        }
    }
    
    private function insertNameClass($new, $nameid, $class, $langabbr) {
        if ($new) {
            $this->insertIntoNameClass($nameid, $class, $langabbr);
        }
        else {
            $this->db->where('nameid', $nameid);
            $this->db->delete('NameClass'.$langabbr);
            $this->insertIntoNameClass($nameid, $class, $langabbr);
        }
    }
    
    private function insertIntoNameClass($nameid, $class, $langabbr) {
        for ($i = 0; $i < sizeof($class); $i++) {
            $data = array(
                'nameid' => $nameid,
                'class' => $class[$i]->classType
            );
            $this->db->insert('NameClass'.$langabbr, $data);
        }
    }
    
    // NOT IN USE
    private function insertPattern($new, $patternid, $verbid, $langabbr) { // falta param pattern
        $data = array(
            'patternid' => $patternid,
            'verbid' => $verbid
        );
        if ($new) $this->db->insert('Pattern'.$langabbr, $data);
        else {
            $this->db->where('patternid', $patternid);
            $this->db->update('Pattern'.$langabbr, $data);
        }
    }
    
    // NOT IN USE
    private function insertVerb($new, $verbid, $verbtext, $actiu, $langabbr) {
        $data = array(
            'verbid' => $verbid,
            'verbtext' => $verbtext,
            'actiu' => $actiu
        );
        if ($new) $this->db->insert('Verb'.$langabbr, $data);
        else {
            $this->db->where('verbid', $verbid);
            $this->db->update('Verb'.$langabbr, $data);
        }
    }
    
    // NOT IN USE
    private function insertVerbConjugation($new, $verbid, $tense, $pers, $singpl, $verbconj, $langabbr) {
        $data = array(
            'verbid' => $verbid,
            'tense' => $tense,
            'pers' => $pers,
            'singpl' => $singpl,
            'verbconj' => $verbconj
        );
        if ($new) $this->db->insert('VerbConjugation'.$langabbr, $data);
        else {
            $this->db->where('verbid', $verbid);
            $this->db->update('VerbConjugation'.$langabbr, $data);
        }
    }
    
    // NOT IN USE
    private function insertVerbPattern($new, $verbid, $patternid, $langabbr) {
        $data = array(
            'verbid' => $verbid,
            'patternid' => $patternid
        );
        if ($new) $this->db->insert('VerbPattern'.$langabbr, $data);
        else {
            $this->db->where('verbid', $verbid);
            $this->db->update('VerbPattern'.$langabbr, $data);
        }
    }

    private function insertP_StatsUserPicto($ID_PSUPUser, $pictoid) {
        $data = array(
            'ID_PSUPUser' => $ID_PSUPUser,
            'pictoid' => $pictoid,
            'countx1' => 1,
            'lastdate' => mdate("%Y/%m/%d", time())
        );
        $this->db->insert('P_StatsUserPicto', $data);
    }
    
    private function insertPictograms($new, $pictoid, $ID_PUSer, $pictoType, $supportsExpansion, $imgPicto, $imgFolder) {
        if($new) {
            $data = array(
                'ID_PUser' => $ID_PUSer,
                'pictoType' => $pictoType,
                'supportsExpansion' => $supportsExpansion,
                'imgPicto' => $imgPicto,
                'imgFolder' => $imgFolder
            );
            $this->db->insert('Pictograms', $data);
            $pictoid = $this->db->insert_id();
        }
        else {
            $data = array(
                'ID_PUser' => $ID_PUSer,
                'pictoType' => $pictoType,
                'supportsExpansion' => $supportsExpansion,
                'imgPicto' => $imgPicto,
                'imgFolder' => $imgFolder
            );
            $this->db->where('pictoid', $pictoid);
            $this->db->update('Pictograms', $data);
        }
        return $pictoid;
    }
    
    private function insertPictogramsLanguage($new, $pictoid, $languageid, $pictotext, $pictofreq ) {
        if ($new){
            $data = array(
            'pictoid' => $pictoid,
            'languageid' => $languageid,
            'insertdate' => mdate("%Y/%m/%d", time()),
            'pictotext' => $pictotext,
            'pictofreq' => $pictofreq
            );
            $this->db->insert('PictogramsLanguage', $data);
        }
        else {
            $data = array(
            'insertdate' => mdate("%Y/%m/%d", time()),
            'pictotext' => $pictotext,
            'pictofreq' => $pictofreq
            );
            $this->db->where('pictoid', $pictoid);
            $this->db->update('PictogramsLanguage', $data);
        }
    }

    public function insertPicto($objAdd, $ID_PUSer, $langabbr) {
        
        $languageid = ($langabbr == 'CA' ? 1:  2);
        $pictoid = $this->insertPictograms($objAdd->new, $objAdd->pictoid, $ID_PUSer, $objAdd->type, $objAdd->supExp, $objAdd->imgPicto, $objAdd->imgFolder);
        var_dump($pictoid);        
         //pictofreq a modificar
        
        if ($objAdd->type == 'name') {
            $this->insertPictogramsLanguage($objAdd->new, $pictoid, $languageid, $objAdd->nomtext, $pictofreq = 10.000);
            $this->insertName($objAdd->new, $pictoid, $objAdd->nomtext, $objAdd->mf, $objAdd->singpl, $objAdd->contabincontab, $objAdd->determinat, $objAdd->ispropernoun, $objAdd->defaultverb, $objAdd->plural, $objAdd->femeni, $objAdd->fempl, $langabbr);
            $this->insertNameClass($objAdd->new, $pictoid, $objAdd->class, $langabbr);            
        }
        else if ($objAdd->type == 'adj') {
            $this->insertPictogramsLanguage($objAdd->new, $pictoid, $languageid, $objAdd->masc, $pictofreq = 10.000);
            $this->insertAdjective($objAdd->new, $pictoid, $objAdd->fem, $objAdd->masc, $objAdd->mascpl, $objAdd->fempl, $objAdd->defaultverb, $objAdd->subjdef, $langabbr);
            $this->insertAdjClass($objAdd->new, $pictoid, $objAdd->class, $langabbr);
        }
    }

    public function deletePictogram($pictoid, $type, $idusu, $langid) {
        switch ($type) {
            case "adj":
                $this->deleteAdj($pictoid, $langid);
                break;
            case "name":
                $this->deleteName($pictoid, $langid);
                break;
            case "verb":
                $this->deleteVerb($pictoid, $langid);
                break;
            default:
                break;
        }
        
        $this->db->where('ID_PUser', $idusu);                             
        $this->db->where('pictoid', $pictoid);                             
        $this->db->delete('Pictograms');
        
        $this->db->where('pictoid', $pictoid);                             
        $this->db->delete('PictogramsLanguage');
    }
    
    private function deleteAdj($pictoid, $ID_Language) 
    {
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
        $classtable ON $maintable.adjid = $classtable.adjid AND Pictograms.pictoid=?";
        $this->db->query($sql, $pictoid);
    }
    
    private function deleteName($pictoid, $ID_Language) 
    {
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
        $classtable ON $maintable.nameid = $classtable.nameid AND Pictograms.pictoid=?";
        $this->db->query($sql, $pictoid);
    }
    
    private function deleteVerb($pictoid, $ID_Language) 
    {
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
        $patterntable ON $maintable.verbid = $patterntable.verbid  AND Pictograms.pictoid=?";
        $this->db->query($sql, $pictoid);
    }
}
?>