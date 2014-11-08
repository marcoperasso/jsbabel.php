<?php

class Crypter {
    const s1 = <<<EOT
    this.crypt = function(s){
        var ret = '';
        for (var i = 0; i < s.length; i++){
            var code = s.charCodeAt(i) + 1;
            ret += String.fromCharCode(code);
        }
        return ret;
    };
EOT;
    const s2 = <<<EOT
    this.crypt = function(s){
        var ret = '';
        for (var i = 0; i < s.length; i++){
            var code = s.charCodeAt(i) -1;
            ret += String.fromCharCode(code);
        }
        return ret;
    };
EOT;
    const s3 = <<<EOT
    this.crypt = function(s){
        var ret = '';
        for (var i = 0; i < s.length; i=i+2){
            ret += s.charAt(i);
        }
        for (var i = 1; i < s.length; i=i+2){
            ret += s.charAt(i);
        }
        return ret;
    };
EOT;
    const s4 = <<<EOT
    this.crypt = function(s){
        var ret = '';
        for (var i = 1; i < s.length; i=i+2){
            ret += s.charAt(i);
        }
        for (var i = 0; i < s.length; i=i+2){
            ret += s.charAt(i);
        }
        return ret;
    };
EOT;
    static $scripts = array(Crypter::s1, Crypter::s2, Crypter::s3, Crypter::s4);
    const CryptIndex = "CryptIndex";

    public function get_script() {
        $CI = & get_instance();
        $CI->load->library('session');
        $i = rand(0, count(Crypter::$scripts) - 1);
        $CI->session->set_flashdata(Crypter::CryptIndex, $i);
        return Crypter::$scripts[$i];
    }

    public function decrypt($str) {
        $CI = & get_instance();
        $CI->load->library('session');
        $index = $CI->session->flashdata(Crypter::CryptIndex);
        $ret = "";
        $strlen = strlen($str);

        switch ($index) {
            case 0: {
                    for ($i = 0; $i < $strlen; $i++) {
                        $char = ord($str[$i]);
                        $char--;
                        $ret .= chr($char);
                    }
                    break;
                }
            case 1: {
                    for ($i = 0; $i < $strlen; $i++) {
                        $char = ord($str[$i]);
                        $char++;
                        $ret .= chr($char);
                    }
                    break;
                }
            case 2: {
                    $mid = (int)ceil($strlen / 2.0);

                    for ($j = 0; $j < $mid; $j++) {
                        $ret .= $str[$j];
                        $j2 = $j + $mid;
                        if ($j2 < $strlen) {
                            $ret .= $str[$j2];
                        }
                    }
                    break;
                }
            case 3: {
                    $mid = (int)floor($strlen / 2.0);
                    for ($j = $mid; $j < $strlen; $j++) {
                        $ret .= $str[$j];
                        $j2 = $j - $mid;
                        if ($j2 < $mid) {
                            if ($j2 >= strlen($str))
                                echo 'a';
                            $ret .= $str[$j2];
                        }
                    }
                    break;
                }
            default:
                throw show_error("ERROR");
        }
        return $ret;
    }

}

?>
