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
        $site->update();
        $this->output
                ->set_content_type('text/json')
                ->set_output(json_encode(array("success" => true)));
    }

    //returns login page
    public function get_login_page() {
        $view = $this->load->view('login.html', '', true);
        $view = str_replace('{{BASE_URL}}', BASE_URL, $view);
        $view = hexEncode($view);
        $response = 'jQuery(_jsbHexDecode("' . $view . '")).appendTo(document.body);';
        $this->output
                ->set_content_type('text/javascript')
                ->set_output($response);
    }

    //performs login 
    //reads params using GET
    public function do_login() {
        $userid = $this->input->get('1');
        $pwd = $this->input->get('2');

        $h = password_hash($pwd, PASSWORD_BCRYPT);
        $this->load->model('User_model');
        $user = $this->User_model->get_user($userid);

        if (!$user) {
            $response = "_jsbMessage('Invalid user ID')";
        } else if (!password_verify($pwd, $user->password)) {
            $response = "_jsbMessage('Invalid password')";
        } else {
            $this->set_user($user);
            $response = "location.reload();";
        }

        $this->output
                ->set_content_type('text/javascript')
                ->set_output($response);
    }

    function do_logoff() {
        $this->set_user(NULL);
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
        $clientData = new stdClass;
        $clientData->ld = array();
        $host = parse_url($url, PHP_URL_HOST);
        $user = $this->get_user();
        $site = $this->Site_model->get_site($host);
        //site not yet registered: apply default data
        if (!$site) {
            if ($user) {//user logged: open site manager
                $adminUrl = BASE_URL . "/mysites";
                //avoid recursion!
                if (parse_url($url, PHP_URL_PATH) != parse_url($adminUrl, PHP_URL_PATH)) {
                    $adminUrl .= "?host=" . encode_URI_Component($host);
                    $this->output
                            ->set_content_type('text/javascript')
                            ->set_output("window.open('" . $adminUrl . "', 'JSBABEL');");
                }
            } else {//user not logged: add login button
                $clientData->bl = "";
                $clientData->a = "C";
                $clientData->x = 0;
                $clientData->y = 0;

                $this->output
                        ->set_content_type('text/javascript')
                        ->set_output("__babel.setTranslationData(" . json_encode($clientData) . ", 0, '', 0);");
            }
            $this->output->set_content_type('text/javascript');
            return;
        }
        $serverVer = $site->translation_version + CLIENT_CACHE_VERSION;

        $baseLocale = $site->base_locale;
        $translations = "";
        if ($user) {
            $role = $user->get_role($site->id);
            if (UserRole::Is($role, UserRole::Translator)) {
                $response .= "__babel.addTranslatorScripts(" . $role . ", false);";
            }
        }

        //when user is logged, translate mode is active
        if ($user || $serverVer != $clientDataVer) {
            $clientData->bl = $baseLocale;
            $clientData->a = $site->anchor;
            $clientData->x = $site->offset;
            $clientData->y = $site->top;

            array_push($clientData->ld, $this->getLocaleFlagData($baseLocale));

            foreach ($site->get_target_locales() as $loc) {
                if ($loc == $targetLocale) {
                    array_unshift($clientData->ld, $this->getLocaleFlagData($loc)); //used language is always the first of the list
                } else {
                    array_push($clientData->ld, $this->getLocaleFlagData($loc));
                }
            }
        }
        if ($serverVer != $clientStringsVer) {
            if ($baseLocale != $targetLocale) {
                $translations = $this->Translations_model->read_translations($site->id, $targetLocale, parse_url($url, PHP_URL_PATH));
            }
        }


        $response .= "__babel.setTranslationData(" .
                json_encode($clientData) . ',' .
                $serverVer . ',"' .
                $translations . '",' .
                $serverVer . ');';
        $this->output
                ->set_content_type('text/javascript')
                ->set_output($response);
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
            array_push($tt, $this->Translations_model->create($base, $this->input->post("t" . $i), StringType::Text, "true" == $this->input->post("p" . $i)));
            $i++;
        }
        $i = 0;
        while ($base = $this->input->post("bm" . $i)) {
            array_push($tt, $this->Translations_model->create($base, $this->input->post("tm" . $i), StringType::Move, "true" == $this->input->post("pm" . $i)));
            $i++;
        }

        while ($base = $this->input->post("bi" . $i)) {
            array_push($tt, $this->Translations_model->create($base, $this->input->post("ti" . $i), StringType::Ignore, "true" == $this->input->post("pi" . $i)));
            $i++;
        }
        $this->Translations_model->save_translations($site->id, $tt, parse_url($url, PHP_URL_PATH), $targetLanguage, $append);
        $site->translation_version++;
        $site->update();
        $this->output
                ->set_content_type('text/json')
                ->set_output(json_encode(array("success" => true)));
    }

    public function begin_autotranslate() {
        $this->output
                ->set_content_type('text/json')
                ->set_output(json_encode(array("success" => true)));

        if (!isset($_SESSION))
            return;
        $url = $this->input->post('src');
        $targetLocale = $this->input->post('targetLocale');
        $strings = $this->input->post('strings');

        if ($strings === NULL)
            return;

        $site = $this->Site_model->get_site(parse_url($url, PHP_URL_HOST));
        if (!$site)
            return;

        $tt = $this->Translations_model->parse($strings);
        $this->Translations_model->auto_translate($site->id, $targetLocale, $tt);
        $key = $this->get_automatic_translation_key($url, $targetLocale);
        $_SESSION[$key] = $this->Translations_model->serialize($tt);
    }

    public function end_autotranslate() {
        $response = "";
        if (isset($_SESSION)) {
            $url = $this->input->get('src');
            $targetLocale = $this->input->get('loc');
            $t = $_SESSION[$this->get_automatic_translation_key($url, $targetLocale)];
            if ($t) {
                $response = "__babel.addAutomaticTranslations('" . str_replace("'", "\\'", $t) . "');";
            } else {
                $response = "__babel.addAutomaticTranslations('');";
            }
        }
        $_SESSION[$this->get_automatic_translation_key($url, $targetLocale)] = NULL;
        $this->output
                ->set_content_type('text/javascript')
                ->set_output($response);
    }

    public function translate() {
        $json = NULL;
        try {
            $callback = $this->input->get("jsoncallback");
            $inputStr = $this->input->get("text");
            $from = $this->input->get("from");
            $to = $this->input->get("to");
            $translated = $this->microsoft_translate($inputStr, $from, $to, FALSE);
            if (!$translated)
                $translated = $this->microsoft_translate($inputStr, $from, $to, TRUE);
            $json = array("success" => true, "value" => $translated);
        } catch (Exception $ex) {
            $json = array("success" => false, "error" => $ex->getMessage());
        }
        if ($callback) {
            $output = $callback . "(" . json_encode($json) . ");";
            $this->output
                    ->set_content_type('text/javascript')
                    ->set_output($output);
        } else {
            $this->output
                    ->set_content_type('text/json')
                    ->set_output(json_encode($json));
        }
    }

    private function microsoft_translate($inputStr, $from, $to, $forceGetToken) {
        //Get the Access token.
        //Il token dure dieci minuti, lo metto in sessione e lo riuso finché posso
        if (!isset($_SESSION)) {
            session_start();
        }
        $accessToken = $_SESSION["microsoft_token"];

        if (!$accessToken || $forceGetToken) {
            $accessToken = $this->getTokens();
            if (isset($_SESSION))
                $_SESSION["microsoft_token"] = $accessToken;
        }

        //Create the authorization Header string.
        $authHeader = "Authorization: Bearer " . $accessToken;
        $uri = "http://api.microsofttranslator.com/v2/Http.svc/Translate?text=" . urlencode($inputStr) . "&from=" . $from . "&to=" . $to;
        //Call the curlRequest.
        $strResponse = $this->curlRequest($uri, $authHeader);
        //Interprets a string of XML into an object.
        $xmlObj = simplexml_load_string($strResponse);
        $ar = (array) $xmlObj[0];
        $translated = $ar[0];
        return $translated;
    }

    private function getTokens() {

        $clientID = "jsbabel";
        $clientSecret = MICROSOFT_TRANSLATOR_SECRET;
        $authUrl = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
        $scopeUrl = "http://api.microsofttranslator.com";
        $grantType = "client_credentials";

        //Initialize the Curl Session.
        $ch = curl_init();
        //Create the request Array.
        $paramArr = array(
            'grant_type' => $grantType,
            'scope' => $scopeUrl,
            'client_id' => $clientID,
            'client_secret' => $clientSecret
        );
        //Create an Http Query.//
        $paramArr = http_build_query($paramArr);
        //Set the Curl URL.
        curl_setopt($ch, CURLOPT_URL, $authUrl);
        //Set HTTP POST Request.
        curl_setopt($ch, CURLOPT_POST, TRUE);
        //Set data to POST in HTTP "POST" Operation.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArr);
        //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Execute the  cURL session.
        $strResponse = curl_exec($ch);
        //Get the Error Code returned by Curl.
        $curlErrno = curl_errno($ch);
        if ($curlErrno) {
            $curlError = curl_error($ch);
            throw new Exception($curlError);
        }
        //Close the Curl Session.
        curl_close($ch);
        //Decode the returned JSON string.
        $objResponse = json_decode($strResponse);

        return $objResponse->access_token;
    }

    private function curlRequest($url, $authHeader, $postData = '') {
        //Initialize the Curl Session.
        $ch = curl_init();
        //Set the Curl url.
        curl_setopt($ch, CURLOPT_URL, $url);
        //Set the HTTP HEADER Fields.
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader, "Content-Type: text/xml"));
        //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, False);
        if ($postData) {
            //Set HTTP POST Request.
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //Set data to POST in HTTP "POST" Operation.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        //Execute the  cURL session.
        $curlResponse = curl_exec($ch);
        //Get the Error Code returned by Curl.
        $curlErrno = curl_errno($ch);
        if ($curlErrno) {
            $curlError = curl_error($ch);
            throw new Exception($curlError);
        }
        //Close a cURL session.
        curl_close($ch);
        return $curlResponse;
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

    private function get_automatic_translation_key($url, $targetLocale) {
        return hexEncode($url . $targetLocale);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */