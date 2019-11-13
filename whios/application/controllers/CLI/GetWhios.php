<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//if (file_exists(__DIR__ . '/vendor/autoload.php')) {
//    require(__DIR__ . '/vendor/autoload.php');
//}

require dirname(__FILE__) . '/../../../vendor/autoload.php';

use phpWhois\Whois;



class GetWhios extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {


//        $IDN = new ToIdn();
// The input string, if input is not UTF-8 or UCS-4, it must be converted before
//        $input = utf8_encode('taoerji.com');
// Encode it to its punycode presentation
//        $output = $IDN->convert($input);
//        var_dump($output);

//        $this->load->library('pwhios');
//        $result = $this->pwhios->query('taoerji.com');
//        var_dump($result);
        $x = new Whois();
////        $x = new idna-convert();
////        idna_convert
////        new idna_con
        $result = $x->lookup('taoerji.com');
        var_dump($result);
        echo "ok";
    }
}
