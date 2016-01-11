<?php

require_once("user_sites_model.php");

class User_model extends MY_Model {

    public $id;
    public $userId;
    public $mail;
    public $name;
    public $surname;
    public $active;
    public $password;
    public $sites;

    public function __construct() {
        parent::__construct();
        $this->t = "USERS";
        $this->c = array(
            'id' => 'ID',
            'userId' => 'USERID',
            'mail' => 'MAIL',
            'name' => 'NAME',
            'surname' => 'SURNAME',
            'active' => 'ACTIVE',
            'password' => 'PASSWORD'
        );
    }

    public function to_string() {
        return $this->name . ' ' . $this->surname;
    }

    public function get_public_clone() {
        $clone = new stdClass();
        $clone->userid = $this->userId;
        $clone->mail = $this->mail;
        $clone->name = $this->name;
        $clone->surname = $this->surname;
        return $clone;
    }

    public function get_user($userId) {
        $query = $this->db->get_where($this->t, array($this->c['userId'] => $userId));
        $row = $query->row();
        if (!$row)
            return FALSE;
        $this->row_to_object($row, $this);
        return $this;
    }

    public function get_role($siteId) {
        $role = UserRole::None;
        if ($this->userId == "admin") {
            $role = $role | UserRole::Admin;
        }
        foreach ($this->get_sites() as $site) {
            if ($siteId == $site->siteId) {
                $role = $role | $site->role;
                break;
            }
        }
        return $role;
    }

    public function get_sites() {
        if (!$this->sites) {
            $this->load->model('User_sites_model');
            $this->sites = $this->User_sites_model->get_user_sites($this->id);
        }
        return $this->sites;
    }

    /*    public function get_user($mail) {
      $query = $this->db->get_where($this->t, array('mail' => $mail));
      if ($query->num_rows() === 1) {
      $this->assign($query->row());
      return TRUE;
      }
      return FALSE;
      }

      public function activate_user() {
      $this->active = TRUE;
      $this->activationdate = date('Y-m-d');
      $this->load->library('Crypter');
      $pwd = $this->crypter->decrypt($this->input->post('password'));

      $this->load->library('BCrypt');
      $bcrypt = new BCrypt(15);
      $this->password = $bcrypt->hash($pwd);
      $this->db->where('id', $this->id);
      $this->db->update('users', $this);
      }

      public function create_user() {
      $this->mail = $this->input->post('mail');
      $this->name = $this->input->post('name');
      $this->surname = $this->input->post('surname');
      $this->nickname = "";
      $this->gender = GENDER_UNSPECIFIED;
      $this->active = FALSE;
      $this->showposition = SHOW_POSITION_GROUP;
      $this->showname = SHOW_NAME_GROUP;

      $this->db->insert('users', $this);
      $this->id = $this->db->insert_id();
      }
     */
}
