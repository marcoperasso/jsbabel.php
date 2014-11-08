<?php

abstract class StringType {

    const Text = 0;
    const Move = 1;
    const Image = 2;
    const Ignore = 3;

}

class Translations_model extends MY_Model {

    const IMAGE_CHAR = '!';
    const MOVE_CHAR = '?';
    const IGNORE_CHAR = ':';
    const TEXT_CHAR = '*';

    var $baseString = "";
    var $targetString = "";
    var $type = StringType::Text;
    var $pageSpecific;
    var $used;

    public function __construct($parameters = array()) {
        foreach ($parameters as $key => $value) {
            $this->$key = $value;
        }
    }

    public function get_base_string() {
        return $this->baseString;
    }

    public function get_target_string() {
        return $this->targetString;
    }

    public function get_type() {
        return $this->type;
    }

    public function is_page_specific() {
        return $this->pageSpecific;
    }

    public function to_string() {
        return "Translation{baseString=" . $this->baseString . ", targetString=" . $this->targetString . ", type=" . $this->type . ", pageSpecific=" . $this->pageSpecific + '}';
    }

    public function get_separator($type) {
        switch ($type) {
            case StringType::Image:
                return Translations_model::IMAGE_CHAR;
            case StringType::Move:
                return Translations_model::MOVE_CHAR;
            case StringType::Ignore:
                return Translations_model::IGNORE_CHAR;
            case StringType::Text:
            default:
                return Translations_model::TEXT_CHAR;
        }
    }

    public function from_separator($ch) {
        switch ($ch) {
            case Translations_model::IMAGE_CHAR:
                return StringType::Image;
            case Translations_model::MOVE_CHAR:
                return StringType::Move;
            case Translations_model::IGNORE_CHAR:
                return StringType::Ignore;
            case Translations_model::TEXT_CHAR:
                return StringType::Text;
            default:
                return null;
        }
    }

    public function save_translations($siteid, $tt, $page, $targetLanguage, $append) {
        $this->db->trans_start();
        //insert page if not existing
        $query = $this->db->get_where('pages', array('siteid' => $siteid, 'page' => $page));
        $pageId = null;
        if ($query->num_rows() === 0) {
            $this->db->insert('pages', array('siteid' => $siteid, 'page' => $page));
            $pageId = $this->db->insert_id();
        } else {
            $row = $query->row();
            $pageId = $row->ID;
        }
        if (!$append) { //delete all string - page associations
            $this->db->where('pageid', $pageId);
            $this->db->delete('PAGESTRINGS');
        }
        foreach ($tt as $t) {

            if (!$t->get_target_string() && $t->get_type() != StringType::Ignore) {
                continue;
            }
            $query = $this->db->query("SELECT * FROM BASESTRINGS WHERE SITEID = ? AND (PAGEID IS NULL OR PAGEID = ?) AND TEXT = ? AND TYPE = ?", array($siteid, $pageId, $t->get_base_string(), $t->get_type()));
            $baseId = 0;
            if ($query->num_rows() == 0) {
                $this->db->insert('BASESTRINGS', array(
                    'siteid' => $siteid,
                    'pageid' => $t->is_page_specific() ? $pageId : null, //if translation is not page specific, this field has to be empty!
                    'text' => $t->get_base_string(),
                    'type' => $t->get_type()
                ));
                $baseId = $this->db->insert_id();
            } else {
                //there may be multiple occurrencies because of case insensitivity of the query
                foreach ($query->result() as $row) {
                    if ($row->TEXT == $t->get_base_string()) {
                        $baseId = $row->ID;
                        break;
                    }
                }
            }

            $this->db->insert('PAGESTRINGS', array('PAGEID' => $pageId, 'STRINGID' => $baseId));

            //insert target string
            $query = $this->db->get_where("TARGETSTRINGS", array("BASEID" => $baseId, "LOCALE" => $targetLanguage));
            if ($query->num_rows() == 0) {
                $this->db->insert('TARGETSTRINGS', array(
                    'BASEID' => $baseId,
                    'LOCALE' => $targetLanguage,
                    'TEXT' => $t->get_target_string()
                ));
            } else {
                $row = $query->row();
                if ($row->TEXT != $t->get_target_string()) {
                    $this->db->where('BASEID', $baseId);
                    $this->db->update('TARGETSTRINGS', array('TEXT' => $t->get_target_string()));
                }
            }
        }

        $this->db->trans_complete();
    }

    public function read_translations($siteid, $locale, $page) {
        $query = $this->db->query("SELECT DISTINCT B.TYPE AS TYPE, B.TEXT AS BASESTRING, T.TEXT AS TARGETSTRING, B.PAGEID IS NOT NULL  AS PAGESPECIFIC
            FROM BASESTRINGS B INNER JOIN TARGETSTRINGS T ON T.BASEID = B.ID INNER JOIN PAGESTRINGS ON B.ID = PAGESTRINGS.STRINGID 
INNER JOIN PAGES ON PAGES.ID = PAGESTRINGS.PAGEID 
WHERE PAGES.SITEID = ? AND T.LOCALE = ? AND PAGES.PAGE = ? AND (B.PAGEID =PAGES.ID OR B.PAGEID IS NULL)", array($siteid, $locale, $page));
        if (!$query) {
            return "";
        }
        $rows = $query->result();
        $result = "";
        foreach ($rows as $t) {

            $bs = $t->BASESTRING;
            $ts = $t->TARGETSTRING;
            $bs = encode_URI_Component($bs);
            $ts = encode_URI_Component($ts);
            $pageSpecific = $t->PAGESPECIFIC;
            $sep = $this->get_separator($t->TYPE);
            $result .= strlen($bs) . $sep . $bs . strlen($ts) . $sep . $ts . ($pageSpecific ? '1' : '0');
        }
        return $result;
    }

    private function parse_string($s, &$idx, &$ret, &$type) {
        while (true) {
            $ch = $s[$idx];
            $type = $this->from_separator($ch);
            if ($type != null) {
                break;
            }
            $ret .= ch;
        }
    }

    public function parse($s) {
        $idx = 0;
        $tot = strlen($s);
        while ($idx < $tot) {
            $base = "";
            $type_base = StringType::Text;
            $this->parse_string($s, $idx, $base, $type_base);

            $target = "";
            $type_target = StringType::Text;
            $this->parse_string($s, $idx, $target, $type_target);
            $pageSpecific = $s[$idx++] == '1';
            // getTranslations().add(new Translation(Helper.decodeURIComponent(b.Text), Helper.decodeURIComponent(t.Text), pageSpecific, b.Type, this));
        }
    }

}
