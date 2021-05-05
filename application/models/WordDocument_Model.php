<?php
include_once(APPPATH."third_party/PhpWord/Autoloader.php");

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
Autoloader::register();
Settings::loadConfig();
/* Provide a Word Document
 * @rjlopezdev
 */
class WordDocument_Model extends CI_Model {

  //Data folder
  var $folder_name;

  //WordDocument
  var $phpWord;
  var $sentences;

  //WordDocument Styles
  var $imgStyle;
  var $specialImgStyle;
  var $headerStyle;
  var $sentenceStyle;
  var $tableStyle;

  function __construct(){
      parent::__construct(); 
      $this->load->database();
      $this->phpWord = new \PhpOffice\PhpWord\PhpWord();
  }

  public function getWordDocument($folder_data){
      
    $this->sentences = $folder_data;
    $this->setStyles();

    //Adding a Section to the document
    $section = $this->phpWord->addSection();

    //Adding FOLDER TITLE
    $section->addText($this->sentences['folderTitle'], $this->headerStyle);
    $section->addTextBreak();

    //ITERATE preRecSentences
    foreach ($this->sentences['preRecSentences'] as $i) {

      $table = $section->addTable($this->tableStyle);

      $table->addRow();

      /*
       * Append image IF EXISTS
       */
      if($i[0]['image1'] !== null)
        $table->addCell(150)->addImage(
          $i[0]['image1'],
          $this->imgStyle
        );

      if($i[0]['image2'] !== null)
        $table->addCell(150)->addImage(
          $i[0]['image2'],
          $this->imgStyle
        );

      if($i[0]['image3'] !== null)
        $table->addCell(150)->addImage(
          $i[0]['image3'],
          $this->imgStyle
        );

      //Adding associated pictogram text
      $section->addText($i[0]['sentence'], $this->sentenceStyle);
      $section->addTextBreak();
    }

    //ITERATE NOTpreRecSentences
    foreach ($this->sentences['NOTpreRecSentences'] as $i) {
      $count = 0;

      $table = $section->addTable($this->tableStyle);

      for ($pos=0; $pos < count($i); $pos++) {

        if($count++ % 4 == 0) $table->addRow();

        //ADD IMAGE
        
        if ($i[$pos]['imgtemp'] != null && $i[$pos]['imgtemp'] != '') {
            $table->addCell(150)->addImage(
                $i[$pos]['imgtemp'],
                $this->imgStyle
            );
        }
        else {
            $table->addCell(150)->addImage(
                $i[$pos]['imgFolder'] . $i[$pos]['imgPicto'],
                $this->imgStyle
            );
        }

        //CHECK IF isPlural
        if($i[$pos]['isPlural'] == '1'){
          $table->addCell(50)->addImage(
            'img/pictosespeciales/' . 'plural.png',
            $this->specialImgStyle
          );
        }

        //CHECK IF isFem
        if($i[$pos]['isFem'] == '1'){
          $table->addCell(50)->addImage(
            'img/pictosespeciales/' . 'femenino.png',
            $this->specialImgStyle
          );
        }
      }
      //Adding associated pictogram text
      $section->addText($i[0]['sentence'], $this->sentenceStyle);
      $section->addTextBreak();
    }

    //Save WordDocument on tempory folder
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->phpWord, 'Word2007');
    $file_path = './Temp/' . time() . '.docx';
    $objWriter->save($file_path);

    return $file_path;
  }

  /* Preset WordDocument styles
   * @rjlopezdev
   */
  private function setStyles(){
    //Image style
    $this->imgStyle = array(
        'width'         => 100,
        'height'        => 100,
        'marginTop'     => -1,
        'marginLeft'    => -1,
        'wrappingStyle' => 'behind'
    );
    //Plural or Femenine image
    $this->specialImgStyle = array(
        'width'         => 25,
        'height'        => 25,
        'marginTop'     => -1,
        'marginLeft'    => -1,
        'wrappingStyle' => 'behind'
    );
    //Title style
    $this->headerStyle = array(
      'bold'            => true,
      'size'            => 32,
      'color'           => '#000000'
    );
    //Table style
    $this->tableStyle = array(
      'borderColor'     => '#FFFFFF',
      'borderSize'      => 0,
      'cellMargin'      => 50
    );
    //Sentence style
    $this->sentenceStyle = array(
      'bold'            => true,
      'size'            => 16,
      'color'           => '#000000',
      'valign'          => 'center'
    );

  }

}
