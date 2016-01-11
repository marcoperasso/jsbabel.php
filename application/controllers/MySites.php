<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MySites extends MY_Controller {

    const INVALID_USER = 1;
    const INVALID_URL = 2;
    const CANNOT_DELETE_SITE = 3;
    const CANNOT_CREATE_SITE = 4;
    const CANNOT_UPDATE_SITE = 5;
    const SITE_ALREADY_EXISTING = 6;
    const USER_HAS_NO_RIGHTS = 7;
    public function __construct() {
        parent::__construct();
        $this->load->model('Site_model');
    }

    public function get_flag($loc) {
        $path = realpath(APPPATH) . "/asset/img/flags/" . get_country($loc) . ".png";
        $seconds_to_cache = 22896000;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        $this->output
                ->set_header('Expires: ' . $ts)
                ->set_header('Pragma: cache')
                ->set_header('Cache-Control: max-age=' . $seconds_to_cache)
                ->set_content_type('image/png')
                ->set_output(file_get_contents($path));
    }

    public function site_list() {
        $sites = NULL;
        $locales = NULL;
        $user = $this->get_user();
        if ($user) {
            $sites = $user->get_sites();
            foreach ($sites as $s)
            {
                $s->get_target_locales(); //force locales loading
            }
            $locales = array();
            foreach (get_locales() as $key => $value) {
                $obj = new stdClass;
                $obj->code = $key;
                $obj->displayName = $value;
                array_push($locales, $obj);
            }
        }
        $this->output
        ->set_content_type('text/json')
        ->set_output(json_encode(array("sites" => $sites, "locales" => $locales)));
    }

    public function index() {
        $user = $this->get_user_or_redirect();
        if (!$user)
            return;
        $this->load_view('mysites.html');
    }

    private function verify_permission($user, $siteId) {
        foreach ($user->get_sites() as $site) {
            if ($site->id == $siteId) {

                if (!UserRole::Is($site->role, UserRole::Admin) && !UserRole::Is($site->role, UserRole::Owner)) {
                    $this->send_json_response(MySites::USER_HAS_NO_RIGHTS);
                    return FALSE;
                }
                return TRUE;
            }
        }
        $this->send_json_response(MySites::USER_HAS_NO_RIGHTS);
        return FALSE;
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
        if (!$site->update_object())
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