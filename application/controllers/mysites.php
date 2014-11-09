<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MySites extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
	$this->load_view('my_sites', "My sites");
    }

    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */