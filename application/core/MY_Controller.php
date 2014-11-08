<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    var $user;

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION)) {
            session_start();
        }
        /* $lang = isset($_SESSION["CURRENT_LANGUAGE"]) ? $_SESSION["CURRENT_LANGUAGE"] : NULL;
          if ($lang == NULL)
          $lang = $this->config['language'];
          if (!$this->lang->load("messages", $lang)) */
        $this->lang->load("messages", "it-IT");
        //$this->load->model('User_model');
        //$this->user = (isset($_SESSION) && isset($_SESSION['user'])) ? unserialize($_SESSION["user"]) : NULL;
    }

    protected function send_mail($to, $subject, $message) {
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => MAIL_HOST,
            'smtp_port' => MAIL_PORT,
            'smtp_user' => MAIL_USER,
            'smtp_pass' => MAIL_PASSWORD,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'mailtype' => 'html'
        );

        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->set_crlf("\r\n");
        $this->email->from(MAIL_USER, 'Smart Pointer');

        $this->email->to($to);

        $this->email->subject($subject);
        $this->email->message($message);

        $this->email->send();
    }

    protected function validate_login() {
        if ($this->user == NULL)
        {
            $this->load_view(
                    "loginneeded",
                    "Richiesta login", 
                    array('request' => $_SERVER['REQUEST_URI'])
                    );
            return FALSE;
        }
        return TRUE;
    }

    protected function load_view($view, $title = "", $data = array(), $return = FALSE) {
        $data["view_name"] = $view;
        if (empty($title)) {
            $title = lang("welcome");
        }
        $data["page_title"] = $title;
        $data["user"] = $this->user;
        return $this->load->view("templates/masterpage", $data, $return);
    }

}
