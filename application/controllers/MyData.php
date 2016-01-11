<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MyData extends MY_Controller {

     public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    
   
    public function index() {
        $user = $this->get_user_or_redirect();
        
        $this->load_view('mydata.html');
    }

  
 /*   public function delete($siteId) {
        $user = $this->get_user();
        if (!$user) {
            $this->send_json_response(MySites::INVALID_USER);
            return;
        }
        if (!$this->verify_permission($user, $siteId))
            return;
        if ($this->Site_model->delete_site($siteId))
            $this->send_json_response();
        else
            $this->send_json_response(MySites::CANNOT_DELETE_SITE);
    }*/

    public function update() {
        $user = $this->get_user();
        if (!$user) {
            $this->send_json_response(MySites::INVALID_USER);
            return;
        }
        $user->name = $this->input->post('name');
        $user->surname = $this->input->post('surname');
        if (!$user->update_object())
            $this->send_json_response(1);//MySites::CANNOT_UPDATE_SITE
        else
            $this->send_json_response();
    }

    

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */