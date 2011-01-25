<?php
/**
 * @package DF_Web
 */


require_once 'DF/URL/Path/I.php';


/**
 */
class DF_Web_Path_Part
        implements DF_URL_Path_I {
    
    protected $part = NULL;


    /**
     * Constructor.
     * 
     * @param DF_URL_Path_I $part
     * @return DF_Web_Path_Part
     */
    public function __construct($part) {
        if (NULL === $part) {
            throw new InvalidArgumentException("Not set: part");
        }

        if (!$part instanceof DF_URL_Path_I) {
            throw new DF_Error_InvalidArgumentException("part", $part, "DF_URL_Path_I");
        }

        $this->part = $part;
    }


    static public function fromString($string) {
        $path = DF_Web_Path::fromString($string);
        return new DF_Web_Path_Part($path);
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

        if ("".$other == "".$this) {
            // equal string-wise
            return true;
        }

        return false;
    }


    public function has_trailing_slash() {
        return false;
    }


    public function append_path() {
        throw new Exception("Not supported");
    }
    

    public function __toString() {
        return "".$this->part;
    }

}

require_once 'DF/Web/Path.php';
