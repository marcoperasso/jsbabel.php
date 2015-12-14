<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Register extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Validation_key_model');
         $this->load->model('User_model');
    }

    public function privacy() {
        $this->load->view('register/privacy');
    }

    public function activate() {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $key = $this->input->post('userkey');
        $resetpwd = $this->input->post('resetpwd');
        $data['key'] = $key;
        if ($this->form_validation->run() === TRUE) {
            if ($this->User_model->get_user_by_key($key)) {
                $this->user = $this->User_model;
                $this->db->trans_begin();
                $this->User_model->activate_user();
                $this->Validation_key_model->delete_key($key);
                $this->db->trans_commit();
                set_user($this->User_model);
                if ($resetpwd) {
                    $this->load_view("register/passwordresetted", "Password impostata", $data);
                } else {
                    $this->load_view("register/useractivated", "Registrazione attivata", $data);
                }
            } else {
                if ($resetpwd) {
                    $this->load_view("register/invalidkeyforresetpwd", "Chiave di attivazione non valida", $data);
                } else {
                    $this->load_view("register/invalidkey", "Chiave di attivazione non valida", $data);
                }
            }
        }
    }

    public function preactivate() {
        $key = $this->input->get("userkey");
        $resetpwd = $this->input->get('resetpwd');
        $data['resetpwd'] = isset($resetpwd) ? $resetpwd : FALSE;
        $data['key'] = $key;
        if ($this->User_model->get_user_by_key($key)) {
            $this->load->helper('form');
            $this->load->library('form_validation');
            $data["user_draft"] = $this->User_model;
            if ($resetpwd) {
                $this->load_view("register/activate", "Ripristino password", $data);
            } else {
                $this->load_view("register/activate", "Attiva la registrazione", $data);
            }
        } else {
            if ($resetpwd) {
                $this->load_view("register/invalidkeyforresetpwd", "Chiave di attivazione non valida", $data);
            } else {
                $this->load_view("register/invalidkey", "Chiave di attivazione non valida", $data);
            }
        }
    }

    public function reset_pwd() {
        $mail = $this->input->post('email');
        if ($mail && $this->User_model->get_user($mail)) {
            $this->db->trans_begin();
            $this->Validation_key_model->create_key($this->User_model->id, VALIDATION_KEY_REASON_RESET_PWD);
            $this->db->trans_commit();
            $data["user_draft"] = $this->User_model;
            $data["validationkey"] = $this->Validation_key_model->validationkey;
            $view = $this->load->view('mail/resetpwdmailcontent', $data, TRUE);
            $this->send_mail($this->User_model->mail, lang("reset_pwd_submitted"), $view);
            $this->load_view('register/resettedpwd', "Ripristino password", $data);
        } else {
            $data["mail"] = $mail;
            $this->load_view('register/invalidmail', "Email non valida", $data);
        }
    }

    public function create_user() {
        $this->db->trans_begin();
        $this->User_model->create_user();
        $this->Validation_key_model->create_key($this->User_model->id, VALIDATION_KEY_REASON_REGISTER);
        $this->db->trans_commit();
        $data["user_draft"] = $this->User_model;
        $data["validationkey"] = $this->Validation_key_model->validationkey;
        $view = $this->load->view('mail/registermailcontent', $data, TRUE);
        $this->send_mail($this->User_model->mail, lang("registration_submitted"), $view);
        $this->send_mail("info@ecommuters.com", lang("registration_submitted"), $view);
        $this->load_view('register/userregistered', "Utente registrato", $data);
    }

    public function index() {
        $this->load->view('register/register.html');
    }

}
