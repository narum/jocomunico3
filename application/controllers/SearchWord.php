<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class SearchWord extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('DBwords');
    }

    public function index_get() {
        
    }

    private static function cmp($a, $b) {
            $a = strtolower($a['text']);
            $b = strtolower($b['text']);
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }
    
    public function getDBAll_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr;
        $langid = $request->langid;

        // Controller search all names from all picto table
        $Names = $this->DBwords->getDBNamesLike($startswith, $user, $language, $langid);
        $Verbs = $this->DBwords->getDBVerbsLike($startswith, $user, $language, $langid);
        $Adj = $this->DBwords->getDBAdjLike($startswith, $user, $language, $langid);
        $Exprs = $this->DBwords->getDBExprsLike($startswith, $user, $language, $langid);
        $Advs = $this->DBwords->getDBAdvsLike($startswith, $user, $language, $langid);
        $Modifs = $this->DBwords->getDBModifsLike($startswith, $user, $language, $langid);
        $QuestionPart = $this->DBwords->getDBQuestionPartLike($startswith, $user, $language, $langid);

        // Marge all arrays to one
        $DataArray = array_merge($Names, $Verbs, $Adj, $Exprs, $Advs, $Modifs, $QuestionPart);

        usort($DataArray, array('SearchWord','cmp'));
        
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBNames_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr;
        $langid = $request->langid;
        
        // Controller search all names from all picto table
        $DataArray = $this->DBwords->getDBNamesLike($startswith, $user, $language, $langid);
        usort($DataArray, array('SearchWord','cmp'));
        $response = [
            "data" => $DataArray
        ];
        
        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBVerbs_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr;
        $langid = $request->langid;


        // Controller search all names from all picto table
        $DataArray = $this->DBwords->getDBVerbsLike($startswith, $user, $language, $langid);
        usort($DataArray, array('SearchWord','cmp'));
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBAdj_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr;
        $langid = $request->langid;


        // Controller search all names from all picto table
        $DataArray = $this->DBwords->getDBAdjLike($startswith, $user, $language, $langid);
        usort($DataArray, array('SearchWord','cmp'));
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBExprs_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr;
        $langid = $request->langid;


        // Controller search all names from all picto table
        $DataArray = $this->DBwords->getDBExprsLike($startswith, $user, $language, $langid);
        usort($DataArray, array('SearchWord','cmp'));
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getDBOthers_post() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $startswith = $request->id;
        $user = $request->idusu;
        $language = $request->langabbr;
        $langid = $request->langid;


        // Controller search all names from all picto table
        $Advs = $this->DBwords->getDBAdvsLike($startswith, $user, $language, $langid);
        $Modifs = $this->DBwords->getDBModifsLike($startswith, $user, $language, $langid);
        $QuestionPart = $this->DBwords->getDBQuestionPartLike($startswith, $user, $language, $langid);

        $DataArray = array_merge($Advs, $Modifs, $QuestionPart);
        usort($DataArray, array('SearchWord','cmp'));
        $response = [
            "data" => $DataArray
        ];

        $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    //Unused function, read row by row and make changes on the array
    function create_paths($DataArray) {

        function concat_path($row) {
            $newPath = base_url() . "img/pictos/" . $row["imgPicto"];
            $row["imgPicto"] = $newPath;
            return $row;
        }

        return array_map("concat_path", $DataArray);
    }

}
