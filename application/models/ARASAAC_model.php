<?php

class ARASAAC_model extends CI_Model {
    
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 
     * @param bool/int $id if set to false, all voices are returned,
     * else, the voice with the set $id is returned
     * @return array $output a row for each returned voice with all the fields
     * from the database
     */
    public function getServerImages($name, $type, $userlanguage) 
    {
        $lang = "ca";
        switch($userlanguage){
            case 1:
                $lang="ca";
                break;
            case 2:
                $lang="es";
                break;
        }
        
        $images = array();
        
        if ($name == "" || $name == " ") {
            return $images;
        }
        
        else {
            $service_url = "https://api.arasaac.org/api/pictograms/".$lang."/search/".$name;
            $curl = curl_init($service_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $curl_response = curl_exec($curl);
            curl_close($curl);

            $decoded = json_decode($curl_response);

            for($i=0; $i<count($decoded); $i++){

                $id = $decoded[$i]->_id;
                $text = $decoded[$i]->keywords[0]->keyword;
                $path = "";

                switch($type){
                    case "color":
                        $path = "https://static.arasaac.org/pictograms/".$id."/".$id."_500.png";
                        break;
                    case "bw":
                        $path = "https://static.arasaac.org/pictograms/".$id."/".$id."_nocolor_500.png";
                        break;
                }

                $aux = array(
                    "imagePNGURL" => $path,
                    "name" => $text
                );

                array_push($images, $aux);

                //echo "ID: ".$id."; Name: ".$name."; Path: ".$path." - ";
            }

            return $images;
        }
    }
    
    public function downloadServerImage($idusu, $url, $name)
    {
        $status = "error";
        
        $imgname = substr($url,-13);
        $imgnameclean = str_replace('/','',$imgname);
        $path = "img/users/".$imgnameclean;
        
        $this->db->where('imgPath', $path);
        $this->db->where('imgName', $name);
        $this->db->where('isARASAAC', '1');
        $query = $this->db->get('Images');
        
        // it means that the image is already in the database,
        // but we have to check if it's from the same user
        if ($query->num_rows() > 0) {
            $aux = $query->result();
            // if it's not from the same user, we add it to the user without reuploading the image
            if ($idusu != $aux[0]->ID_ISU) {
                $this->addImageInTable($idusu, $name, $path);
            }
            $status = "ok";
        }
        else {
            // download image from ARASAAC server
            $ch = curl_init($url);
            $fp = fopen($path, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $aux = curl_exec($ch);
            curl_close($ch);
            $aux2 = fclose($fp);
            
            $this->addImageInTable($idusu, $name, $path);
            if ($aux && $aux2) $status = "ok";
        }
        
        return $status;
    }
    
    private function addImageInTable($idusu, $name, $path) {
        $data = array(
            'ID_ISU' => $idusu,
            'imgName' => $name,
            'imgPath' => $path,
            'isArasaac' => '1'
        );

        $this->db->insert('Images', $data);
    }
    
    public function getUserImages($idusu, $name) {
        $output = array();

        $this->db->limit(8);
        $this->db->where('ID_ISU', $idusu);
        $this->db->like('imgName', $name, 'after');
        $this->db->order_by('imgName', 'asc');
        $query = $this->db->get('Images');

        if ($query->num_rows() > 0) {
            $output = $query->result_array();
        }

        return $output;
    }
    
    public function deleteImage($imgID, $path)
    {
        $status = "error";
        $aux = true;
        
        if (!(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')) {
            // if the image is only being used by that user, delete it from the folder
            $this->db->where('imgPath', $path);
            $query = $this->db->get('Images');

            if ($query->num_rows() <= 1) {
                $aux = unlink($path);
            }
        }
                        
        $this->db->where('ID_Image', $imgID);
        $this->db->delete('Images');
        
        if ($aux) $status = "ok";
        
        return $status;
    }
    
}
?>