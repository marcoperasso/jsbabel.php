<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MySites extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function site_list() {
        $sites = NULL;
        $user = $this->get_user();
        if ($user) {
            $sites = $user->get_sites();
        }
        $locales = array();
        foreach (get_locales() as $key => $value) {
            $obj = new stdClass;
            $obj->code = $key;
            $obj->displayName = $value;
            array_push($locales, $obj);
        }
        $this->output
                ->set_content_type('text/json')
                ->set_output(json_encode(array("sites" => $sites, "locales" => $locales)));
    }

    public function index() {
        $user = $this->get_user_or_redirect();
        if (!$user)
            return;
        $host = $this->input->get("host");

        $this->load->model('Site_model');
        $site = $this->Site_model->get_site($host);
        if (!$site) {
            $site = new Site_model();
            $site->host = $host;
            $site->anchor = 'L';
            $site->base_locale = '';
            $site->offset = 0;
            $site->top = 0;
            $site->translation_version = 0;
            $site->save();
        }
        $this->load->view('mysites.html');
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */