<?php

class Locales_model extends MY_Model {

    public $siteId;
    public $locale;

    public function __construct() {
        parent::__construct();
        $this->t = "SITELOCALES";
        $this->c = array(
            'siteId' => 'SITEID',
            'locale' => 'LOCALE'
        );
    }

    public function get_target_locales($siteId) {
        $this->db->select($this->c['locale']);
        $query = $this->db->get_where($this->t, array($this->c['siteId'] => $siteId));
        $locales = array();
        foreach ($query->result() as $loc) {
            $t = $this->c['locale'];
            array_push($locales, $loc->$t);
        }
        return $locales;
    }

}
