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

    public function update() {
        $this->db->trans_start();
        $row = new stdClass;
        $this->object_to_row($this, $row);
        $this->db->where($this->c["id"], $this->id);
        $this->db->update($this->t, $row);
        $this->load->model('Locales_model');
        $this->Locales_model->delete($this->id);
        if ($this->target_locales) {
            foreach ($this->target_locales as $loc) {
                $this->Locales_model->siteId = $this->id;
                $this->Locales_model->locale = $loc;
                $this->Locales_model->insert();
            }
        }
        return $this->db->trans_complete();
    }

    public function insert($ownerUserId) {
        $this->db->trans_start();
        $row = new stdClass;
        $this->object_to_row($this, $row);
        $this->db->insert($this->t, $row);

        $this->id = $this->db->insert_id();
        $this->load->model("User_sites_model");
        $this->User_sites_model->siteId = $this->id;
        $this->User_sites_model->userId = $ownerUserId;
        $this->User_sites_model->role = UserRole::Owner;
        $this->User_sites_model->insert();

        return $this->db->trans_complete();
    }

    public function get_site($host) {
        $query = $this->db->get_where($this->t, array($this->c['host'] => $host));
        $row = $query->row();
        if (!$row)
            return FALSE;
        $this->row_to_object($row, $this);
        return $this;
    }

    public function get_site_by_id($siteId) {
        $query = $this->db->get_where($this->t, array($this->c['id'] => $siteId));
        $row = $query->row();
        if (!$row)
            return FALSE;
        $this->row_to_object($row, $this);
        return $this;
    }

    public function delete_site($siteId) {
        $this->db->where($this->c['id'], $siteId);
        return $this->db->delete($this->t);
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
