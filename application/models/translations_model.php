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

    public $baseString = "";
    public $targetString = "";
    public $type = StringType::Text;
    public $pageSpecific;
    public $used;

    public function __construct($parameters = array()) {
        parent::__construct();
        foreach ($parameters as $key => $value) {
            $this->$key = $value;
        }
    }

    public function to_string() {
        return "Translation{baseString=" . $this->baseString . ", targetString=" . $this->targetString . ", type=" . $this->type . ", pageSpecific=" . $this->pageSpecific + '}';
    }

    public function create($base, $target, $type, $pageSpecific) {
        return new Translations_model(
                array("baseString" => $base, "targetString" => $target,
            "pageSpecific" => $pageSpecific,
            "type" => $type));
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

            if (!$t->targetString && $t->type != StringType::Ignore) {
                continue;
            }
            $query = $this->db->query("SELECT * FROM BASESTRINGS WHERE SITEID = ? AND (PAGEID IS NULL OR PAGEID = ?) AND TEXT = ? AND TYPE = ?", array($siteid, $pageId, $t->baseString, $t->type));
            $baseId = 0;
            $found = FALSE;
            //there may be multiple occurrencies because of case insensitivity of the query
            foreach ($query->result() as $row) {
                if ($row->TEXT == $t->baseString) {
                    $baseId = $row->ID;
                    $found = TRUE;
                    break;
                }
            }
            if (!$found) {
                $this->db->insert('BASESTRINGS', array(
                    'siteid' => $siteid,
                    'pageid' => $t->pageSpecific ? $pageId : null, //if translation is not page specific, this field has to be empty!
                    'text' => $t->baseString,
                    'type' => $t->type
                ));
                $baseId = $this->db->insert_id();
            }

            $this->db->insert('PAGESTRINGS', array('PAGEID' => $pageId, 'STRINGID' => $baseId));

            //insert target string
            $query = $this->db->get_where("TARGETSTRINGS", array("BASEID" => $baseId, "LOCALE" => $targetLanguage));
            if ($query->num_rows() == 0) {
                $this->db->insert('TARGETSTRINGS', array(
                    'BASEID' => $baseId,
                    'LOCALE' => $targetLanguage,
                    'TEXT' => $t->targetString
                ));
            } else {
                $row = $query->row();
                if ($row->TEXT != $t->targetString) {
                    $this->db->where('BASEID', $baseId);
                    $this->db->update('TARGETSTRINGS', array('TEXT' => $t->targetString));
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

    //this function receives an array of non translated strings
    //and tries to find the translations in the database
    public function auto_translate($siteId, $locale, $tt) {
        $baseStringsWithParametersQuery = null;
        foreach ($tt as $t) {
            //first of all, search plain text in db
            $query = $this->db->query("SELECT B.TYPE AS TYPE, B.TEXT AS BASESTRING, T.TEXT AS TARGETSTRING
            FROM BASESTRINGS B INNER JOIN TARGETSTRINGS T ON T.BASEID = B.ID 
WHERE B.SITEID = ? AND T.LOCALE = ? AND B.PAGEID IS NULL AND B.TEXT = ?", array($siteId, $locale, $t->baseString));
            if ($query) {

                //loop on results because query is case insensitive
                foreach ($query->result() as $row) {
                    if ($row->BASESTRING === $t->baseString) {
                        $t->targetString = $row->TARGETSTRING;
                        $t->type = $row->TYPE;
                        continue 2; //next translation to find
                    }
                }
            }

            //if plain text is not found, search strings with parameters
            //I have to select all strings with parameters and then find which one
            //applies to mine
            //strings with parameters contain at least %1% character
            if ($baseStringsWithParametersQuery === null) {
                $baseStringsWithParametersQuery = $this->db->query("SELECT B.TYPE AS TYPE, B.TEXT AS BASESTRING, T.TEXT AS TARGETSTRING
            FROM BASESTRINGS B INNER JOIN TARGETSTRINGS T ON T.BASEID = B.ID 
WHERE B.SITEID = ? AND T.LOCALE = ? AND B.PAGEID IS NULL AND B.TEXT LIKE '%\%1\%%'", array($siteId, $locale));
            }
            if ($baseStringsWithParametersQuery) {
                //loop on results and try to match parameterized strings
                foreach ($baseStringsWithParametersQuery->result() as $row) {
                    if (match_base_string($row->BASESTRING, $t->baseString)) {
                        $t->baseString = $row->BASESTRING; //replace constant value with parameterized one
                        $t->targetString = $row->TARGETSTRING;
                        $t->type = $row->TYPE;
                        continue 2; //next translation to find
                    }
                }
            }
        }
    }

    private function parse_string($s, $tot, &$idx, &$ret, &$type) {
        $len = "";
        while ($idx < $tot) {
            $ch = $s[$idx++];
            $type = $this->from_separator($ch);
            if ($type !== null) {
                break;
            }
            $len .= $ch;
        }

        $ret = urldecode(substr($s, $idx, $len));
        $idx += $len;
    }

    public function parse($s) {
        $tt = array();
        $idx = 0;
        $tot = strlen($s);
        while ($idx < $tot) {
            $base = "";
            $type_base = StringType::Text;
            $this->parse_string($s, $tot, $idx, $base, $type_base);

            $target = "";
            $type_target = StringType::Text;
            $this->parse_string($s, $tot, $idx, $target, $type_target);
            $pageSpecific = $s[$idx++] == '1';
            array_push($tt, $this->Translations_model->create($base, $target, $type_base, $pageSpecific));
        }
        return $tt;
    }

    public function serialize($tt) {

        $result = "";
        if ($tt) {
            foreach ($tt as $t) {

                $bs = $t->baseString;
                $ts = $t->targetString;
                $bs = encode_URI_Component($bs);
                $ts = encode_URI_Component($ts);
                $pageSpecific = $t->pageSpecific;
                $sep = $this->get_separator($t->type);
                $result .= strlen($bs) . $sep . $bs . strlen($ts) . $sep . $ts . ($pageSpecific ? '1' : '0');
            }
        }
        return $result;
    }

}
