<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EmailUpdates extends CI_Controller {

  public function __construct(){
      parent::__construct();
      $this->load->model('Email_model'); 
  }
  
  public function index(){
        $bodyCA = '<p><b>Jocomunico 2.0</b> ja està disponible. La versió online s\'ha actualitzat
            automàticament. Si utilitzeu també la versió descarregada per a Windows o per a Mac, us heu de
            descarrgar el paquet amb l\'actualització. Aneu a
            <a href="https://jocomunico.com"> jocomunico.com</a> per a més informació.</p>
            <p>Podeu trobar el llistat de novetats dins la secció Actualització 1.0 a 2.0 del següent
            <a href="https://jocomunico.com/#/updates"> <b>enllaç</b></a>. I les instruccions per
            dur a terme la instal·lació al botó Actualitzacions del menú 
            <a href="https://jocomunico.com/#/download"> <b>Baixades</b></a>.</p>
            <p>Gràcies pel vostre interès en Jocomunico.</p>
            <p>Fins aviat!</p>
            <p>L\'Equip de Jocomunico</p>
         ';
        
        $bodyES = '<p><b>Jocomunico 2.0</b> ya está disponible. La versión online se ha actualizado
            automáticamente. Si utilizáis también la versión descargada para Windows o para Mac, tenéis que
            descargaros el paquete con la actualización. Id a
            <a href="https://jocomunico.com"> jocomunico.com</a> para más información.</p>
            <p>Podéis encontrar el listado de novedades dentro de la sección Actualización 1.0 a 2.0 del siguiente
            <a href="https://jocomunico.com/#/updates"> <b>enlace</b></a>. Y las instrucciones para
            realizar la instalación en el botón Actualizaciones del menú 
            <a href="https://jocomunico.com/#/download"> <b>Descargas</b></a>.</p>
            <p>Gracias por el interés mostrado en Jocomunico.</p>
            <p>¡Hasta pronto!</p>
            <p>El Equipo de Jocomunico</p>
         ';

//        $list = $this->Email_model->getListValidEmails();
//        
//        //Cargamos la libreria de codeigniter
//        $this->load->library('email');
//        
//        $config = array(
//            //Indicamos el protocolo a utilizar
//            'protocol' => 'sendmail',
//            //El servidor de correo que utilizaremos
//            'smtp_host' => '',
//            //Nuestro usuario
//            'smtp_user' => '',
//            //Nuestra contraseña
//            'smtp_pass' => '',
//            //El email debe ser valido
//            'mailtype' => 'html'
//        );
//        
//        for ($i=0; $i<count($list); $i++) 
//        {
//            //Establecemos esta configuración
//            $this->email->initialize($config);
//            //Ponemos la dirección de correo que enviará el email y un nombre
//            $this->email->from('info@jocomunico.com', 'Jocomunico');
//
//            //Destinatario
//            $this->email->to($list[$i]->email, $list[$i]->name);
//            
//            //Definimos el asunto del mensaje
//            $this->email->subject('Jocomunico 2.0 disponible');
//            
//            if ($list[$i]->lang == '1') {
//                //Definimos el asunto del mensaje
//                $this->email->message($bodyCA);
//            } else {
//                $this->email->message($bodyES);
//            }
//            //Enviamos el email y comprovamos el envio
//            $this->email->send();
//        }        
    }

}
