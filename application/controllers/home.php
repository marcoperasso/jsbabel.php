<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->view('home_not_logged.html');
    }
   
    function data() {
        $data = new stdClass();
        $data->user = $this->get_user();
        if ($data->user) {
            $data->user = $data->user->get_public_clone();
        }
        $data->state = $this->get_state();
        $this->output
                ->set_content_type('text/json')
                ->set_output(json_encode($data));
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */