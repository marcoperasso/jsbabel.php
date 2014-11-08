<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Translator extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Translations_model');
        $this->load->model('Site_model');
    }

    public function test() {
        
    }

    //saves site parameters
    //reads params using POST
    public function save_site_params() {
        if (!$this->is_persistent())
            return;
        $left = $this->input->post('left');
        $top = $this->input->post('top');
        $url = $this->input->post('src');

        $site = $this->Site_model->get_site(parse_url($url, PHP_URL_HOST));
        if (!$site)
            return;

        $site->offset = $left;
        $site->top = $top;
        $site->save();
        $this->output
                ->set_content_type('text/json')
                ->set_output(json_encode(array("success" => true)));
    }

    //returns login page
    public function get_login_page() {
        $view = $this->load->view('login', '', true);
        $view = array_shift(unpack('H*', $view));
        $response = 'jQuery(hexDecode("' . $view . '")).appendTo(document.body);';
        $this->output
                ->set_content_type('text/javascript')
                ->set_output($response);
    }

    //performs login 
    //reads params using GET
    public function do_login() {
        $userid = $this->input->get('1');
        $pwd = $this->input->get('2');

        $this->load->library('BCrypt');
        $bcrypt = new BCrypt(15);
        $this->load->model('User_model');
        $user = $this->User_model->get_user($userid);

        if (!$user) {
            $response = "_jsbMessage('Invalid user ID')";
        } else if (!$bcrypt->verify($pwd, $user->password)) {
            $response = "_jsbMessage('Invalid password')";
        } else {
            set_user($user);
            $response = "location.reload();";
        }

        $this->output
                ->set_content_type('text/javascript')
                ->set_output($response);
    }

    function do_logoff() {
        set_user(null);
        $response = "location.reload();";
        $this->output
                ->set_content_type('text/javascript')
                ->set_output($response);
    }

    //returns translations for a page url
    //reads params using GET
    public function get_translations() {

        $response = '';
        $url = $this->input->get('src');
        $targetLocale = $this->input->get('loc');
        $clientDataVer = $this->input->get('vData');
        $clientStringsVer = $this->input->get('vStrings');
        $site = $this->Site_model->get_site(parse_url($url, PHP_URL_HOST));
        if (!$site)
            return;
        $serverVer = $site->translation_version + CLIENT_CACHE_VERSION;

        $baseLocale = $site->base_locale;
        $translations = "";
        $user = get_user();
        if ($user) {
            $role = $user->get_role($site->id);
            switch ($role) {
                case UserRole::Admin:
                case UserRole::Owner:
                case UserRole::Translator:
                    $response .= "__babel.addTranslatorScripts(" . $role . ", false);";
                    break;
                default:
                    break;
            }
        }
        $send = false;

        //when user is logged, translate mode is active
        if ($user || $serverVer != $clientDataVer) {
            $clientData = new stdClass;
            $clientData->ld = array();
            $clientData->bl = $baseLocale;
            $clientData->a = $site->anchor;
            $clientData->x = $site->offset;
            $clientData->y = $site->top;
            //used language is always the first of the list
            array_push($clientData->ld, $this->getLocaleFlagData($targetLocale));
            if ($baseLocale != $targetLocale) {//lo aggiungo se non l'ho già messo in testa
                array_push($clientData->ld, $this->getLocaleFlagData($baseLocale));
            }
            $this->load->model('Locales_model');
            foreach ($this->Locales_model->get_target_locales($site->id) as $loc) {
                if ($loc != $targetLocale && $loc != $baseLocale) {//add only if not yet added
                    array_push($clientData->ld, $this->getLocaleFlagData($loc));
                }
            }
            $send = true;
        }

        if ($user || $serverVer != $clientStringsVer) {
            if ($baseLocale != $targetLocale) {
                $translations = $this->Translations_model->read_translations($site->id, $targetLocale, parse_url($url, PHP_URL_PATH));

                if (strlen($translations) > 0) {
                    $send = true;
                }
            }
        }
        if ($send) {
            $response .= "__babel.setTranslationData(" .
                    json_encode($clientData) . ',' .
                    $serverVer . ',"' .
                    $translations . '",' .
                    $serverVer . ');';
        }

        $this->output
                ->set_content_type('text/javascript')
                ->set_output($response);
    }

    public function begin_autotranslate() {
        $this->output
                ->set_content_type('text/json')
                ->set_output(json_encode(array("success" => true)));
    }

    //saves translations for a page
    //reads params using POST
    public function save_translations() {

        $url = $this->input->post("src");
        $targetLanguage = $this->input->post("targetLocale");
        $append = "true" == $this->input->post("appendToExisting");
        $site = $this->Site_model->get_site(parse_url($url, PHP_URL_HOST));
        if (!$site)
            return;
        if (!$this->is_persistent())
            return;
        $tt = array();
        $i = 0;
        $base;
        while ($base = $this->input->post("b" . $i)) {
            array_push($tt, new Translations_model(
                    array("baseString" => $base, "targetString" => $this->input->post("t" . $i),
                "pageSpecific" => "true" == $this->input->post("p" . $i),
                "type" => StringType::Text)));
            $i++;
        }
        $i = 0;
        while ($base = $this->input->post("bm" . $i)) {
            array_push($tt, new Translations_model(
                    array("baseString" => $base, "targetString" => $this->input->post("tm" . $i),
                "pageSpecific" => "true" == $this->input->post("pm" . $i),
                "type" => StringType::Text)));
            $i++;
        }

        while ($base = $this->input->post("bi" . $i)) {
            array_push($tt, new Translations_model(
                    array("baseString" => $base, "targetString" => $this->input->post("ti" . $i),
                "pageSpecific" => "true" == $this->input->post("pi" . $i),
                "type" => StringType::Text)));
            $i++;
        }
        $this->Translations_model->save_translations($site->id, $tt, parse_url($url, PHP_URL_PATH), $targetLanguage, $append);
//dh . setTranslations(req . getSession(), tt, url, targetLanguage, append);
        $this->output
                ->set_content_type('text/json')
                ->set_output(json_encode(array("success" => true)));
    }

    public function end_autotranslate() {
        $response = "";
        $url = $this->input->get('src');
        $targetLocale = $this->input->get('loc');
        $t = null;
//            Translations t = (Translations) req.getSession().getAttribute(Helper.getAutomaticTranslationKey(url, targetLocale));
        if ($t != null) {
            $response = "__babel.addAutomaticTranslations('" . $t->serialize() . "');";
        } else {
            $response = "__babel.addAutomaticTranslations('');";
        }
//req.getSession().setAttribute(Helper.getAutomaticTranslationKey(url, targetLocale), null);
        $this->output
                ->set_content_type('text/javascript')
                ->set_output($response);
    }

    private function getLocaleFlagData($locale) {
        $d = new stdClass();
        $country = get_country($locale);
        $d->l = $locale;
        $d->u = "/img/flags/" . $country . ".png";
        $d->t = get_display_name($locale);
        return $d;
    }

    private function is_persistent() {
        return true;
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */