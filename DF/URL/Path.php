<?php
/**
 * @package DF_URL
 */


require_once 'DF/Error/InvalidArgumentException.php';

require_once 'DF/URL/Path/I.php';


/**
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
class DF_URL_Path implements DF_URL_Path_I {

    protected $string               = NULL;
    protected $is_absolute          = NULL;
    protected $has_trailing_slash   = NULL;


    public function __construct($string) {
        if (!is_string($string)) {
            throw new DF_Error_InvalidArgumentException("string", $string, "string");
        }

        $this->setup_string($string);
    }


    private function setup_string($string) {
        $this->string = $string;

        $this->is_absolute = self::check_is_absolute($string);
        $this->has_trailing_slash = self::check_has_trailing_slash($string);
    }


    public function is_absolute() {
        return $this->is_absolute;
    }


    public function has_trailing_slash() {
        return $this->has_trailing_slash;
    }


    protected function get_string() {
        return $this->string;
    }


    public function __toString() {
        return $this->get_string();
    }


    static private function check_has_trailing_slash($string) {
        $last_idx = strlen($string) - 1;
        if ($last_idx < 0) {
            return false;
        }

        if ($string[$last_idx] == "/") {
            return true;
        }

        return false;
    }


    static private function check_is_absolute($string) {
        if ($string && $string[0] == '/') {
            return true;
        }

        return false;
    }

}
