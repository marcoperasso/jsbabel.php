<?php

class MY_Model extends CI_Model {

    protected $t;
    protected $c;

    public function __construct() {
        $this->load->database();
    }

    protected function object_to_row($obj, $row) {
        foreach ($this->c as $field => $column) {
            $row->$column = $obj->$field;
        }
    }

    protected function row_to_object($row, $obj) {
        foreach ($this->c as $field => $column) {
            $obj->$field = $row->$column;
        }
    }

    public function insert_object() {
        $row = new stdClass;
        $this->object_to_row($this, $row);
        $this->db->insert($this->t, $row);
    }

    public function update_object() {
        $this->db->trans_start();
        $row = new stdClass;
        $this->object_to_row($this, $row);
        $this->db->where($this->c["id"], $this->id);
        $this->db->update($this->t, $row);
        $this->update_slaves();
        return $this->db->trans_complete();
    }

    public function update_slaves() {
        
    }

}
