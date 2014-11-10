<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MySites extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
	$this->load->view('my_sites.html');
    }

    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */