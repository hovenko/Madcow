<?php

class DF_Web_HTTP_Request_MagicQuotes {
    static public function stripslash_request() {
        $gpc = array(&$_POST, &$_REQUEST, &$_GET, &$_COOKIE);

        foreach ($gpc as &$var) {
            $var = self::stripslashes_deep($var);
        }
    }


    static public function stripslashes_deep($value) {
        $value  = is_array($value)
                ? array_map(array(__CLASS__, 'stripslashes_deep'), $value)
                : stripslashes($value);

        return $value;
    }
}
