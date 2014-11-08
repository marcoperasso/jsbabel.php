<?php

abstract class UserRole {

    const None = 0;
    const Admin = 1;
    Const Owner = 2;
    Const Translator = 3;

}

class Site_users_model extends MY_Model {

    public $siteId;
    public $userId;
    public $role;

    public function __construct() {
        parent::__construct();
        $this->t = "SITEUSERS";
        $this->c = array(
            'siteId' => 'SITEID',
            'userId' => 'USERID',
            'role' => 'ROLE'
        );
    }

    /*
      public function save() {
      $query = $this->db->get_where($this->t, array($this->c['host'] => $this->host));
      $row = $query->row();
      if ($row) {
      $this->object_to_row($this, $row);
      $this->db->update($this->t, $row);
      } else {
      $row = new stdClass;
      $this->object_to_row($this, $row);
      $this->db->insert($this->t, $row);
      $this->id = $this->db->insert_id();
      }
      }
     */

    public function get_user_sites($userId) {
        $query = $this->db->get_where($this->t, array($this->c['userId'] => $userId));
        $rows = array();
        foreach ($query->result() as $row) {
            $site_user = new stdClass;
            $this->row_to_object($row, $site_user);
            array_push($rows, $site_user);
        }
        return $rows;
    }

}
