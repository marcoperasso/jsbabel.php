<?php

abstract class UserRole {

    //can be a used as a bitwise combination
    const None = 0;
    const Admin = 1;
    Const Owner = 2;
    Const Translator = 4;

    public static function Is($role, $requestedRole)
    {
        return ($role & $requestedRole) == $requestedRole;
    }
}

class User_sites_model extends MY_Model {

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

    public function insert() {
        $row = new stdClass;
        $this->object_to_row($this, $row);
        return $this->db->insert($this->t, $row);
    }

    public function get_user_sites($userId) {
        $this->load->model("Site_model");
        $this->db->select('*');
        $this->db->from($this->t);
        $this->db->join($this->Site_model->t, $this->c['siteId'] . "=" . $this->Site_model->c["id"]);
        $this->db->where($this->c['userId'], $userId);
        $query = $this->db->get();
        $rows = array();
        foreach ($query->result() as $row) {
            $user_site = new Site_model;
            $this->row_to_object($row, $user_site);
            $this->Site_model->row_to_object($row, $user_site);
            array_push($rows, $user_site);
        }
        return $rows;
    }

}
