<?php
/**
 * @package DF_Web
 */


require_once 'DF/Web/Path.php';


/**
 */
class DF_Web_Path_Part
        extends DF_Web_Path {
    
    protected $string = NULL;


    /**
     * Constructor.
     * 
     * @param string $string
     * @return DF_Web_Path_Part
     */
    public function __construct($string) {
        parent::__construct($string);
    }


    public function is_absolute() {
        return false;
    }


    public function equals($other) {
        if (NULL === $other) {
            return false;
        }
        
        if ($other === $this) {
            return true;
        }

        if ($other->string == $this->string) {
            return true;
        }

        return false;
    }


    protected function has_trailing_slash() {
        return false;
    }


    public function append_path() {
        throw new Exception("Not supported");
    }
    


}
