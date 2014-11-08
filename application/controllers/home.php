<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
	$this->load_view('home_not_logged', "Welcome to JSBabel");
	
    }

    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */