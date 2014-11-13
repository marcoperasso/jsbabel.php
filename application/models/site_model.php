<?php

class Site_model extends MY_Model {

    public $id;
    public $host;
    public $anchor;
    public $base_locale;
    public $offset;
    public $top;
    public $translation_version;
    public $target_locales = NULL;

    public function __construct() {
        parent::__construct();
        $this->t = "SITES";
        $this->c = array(
            'id' => 'ID',
            'host' => 'HOST',
            'anchor' => 'ANCHOR',
            'base_locale' => 'BASELOCALE',
            'offset' => 'OFFSET',
            'top' => 'TOP',
            'translation_version' => 'TRANSLATIONVERSION'
        );
    }

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

    public function get_site($host) {
        $query = $this->db->get_where($this->t, array($this->c['host'] => $host));
        $row = $query->row();
        if (!$row)
            return FALSE;
        $this->row_to_object($row, $this);
        return $this;
    }

    public function get_sites($userId) {
        $query = $this->db->get_where($this->t, array($this->c['host'] => $host));
        $row = $query->row();
        if (!$row)
            return FALSE;
        $this->row_to_object($row, $this);
        return $this;
    }

    public function get_target_locales() {
        if (!$this->target_locales) {
            $this->load->model('Locales_model');
            $this->target_locales = $this->Locales_model->get_target_locales($this->id);
        }
        return $this->target_locales;
    }

}
