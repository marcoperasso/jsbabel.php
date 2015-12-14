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
        
        $this->load->view('mydata.html');
    }

  
    public function delete($siteId) {
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
    }

    public function update() {
        $user = $this->get_user();
        if (!$user) {
            $this->send_json_response(MySites::INVALID_USER);
            return;
        }

        $siteId = $this->input->post('siteid');
        if (!$this->verify_permission($user, $siteId))
            return;
        $site = $this->Site_model->get_site_by_id($siteId);
        $site->base_locale = $this->input->post('baseLanguage');
        $site->target_locales = $this->input->post('targetLanguage');
        $site->anchor = $this->input->post('anchor');
        if (!$site->update())
            $this->send_json_response(MySites::CANNOT_UPDATE_SITE);
        else
            $this->send_json_response();
    }

    public function add() {
        //must be logged!
        $user = $this->get_user();
        if (!$user) {
            $this->send_json_response(MySites::INVALID_USER);
            return;
        }
        //read url from request
        $url = $this->input->post("host");
        $scheme = parse_url($url, PHP_URL_SCHEME);
        //add scheme if not existing
        if (!$scheme)
            $url = "http://" . $url;

        //verify site exists!
        $file_headers = @get_headers($url);
        if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $this->send_json_response(MySites::INVALID_URL);
            return;
        }

        $host = parse_url($url, PHP_URL_HOST);
        $site = $this->Site_model->get_site($host);
        //verify site is not yet registered
        if ($site) {
            $this->send_json_response(MySites::SITE_ALREADY_EXISTING);
            return;
        }
        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $lang = substr($lang, 0, strpos($lang, ','));
        //finally create site
        $site = new Site_model();
        $site->host = $host;
        $site->anchor = 'L';
        $site->base_locale = $lang;
        $site->offset = 0;
        $site->top = 0;
        $site->translation_version = 0;

        if ($site->insert($user->id)) {
            $user->sites = NULL;
            $this->set_user($user);
            $this->send_json_response();
        } else {
            $this->send_json_response(MySites::CANNOT_CREATE_SITE);
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */