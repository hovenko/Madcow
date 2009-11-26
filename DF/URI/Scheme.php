<?php


require_once 'DF/URI/MalformedSchemeException.php';


class DF_URI_Scheme {
    
    public static $RE_SCHEME = "[a-z0-9\+\.-]+";
    
    protected $string;
    
    
    /**
     * Constructor.
     * 
     * @param string $string
     * @return DF_URI_Scheme
     */
    public function __construct($string) {
        if (NULL === $string) {
            throw new InvalidArgumentException("Not set: string");
        }
        
        $re_scheme = self::$RE_SCHEME;
        if (!preg_match("#^$re_scheme\$#", $string)) {
            throw new DF_URI_MalformedSchemeException(
                "Malformed URI scheme. Contains invalid characters: $string"
            );
        }
        
        $this->string = $string;
    }
    
    
    public function get_string() {
        return $this->string;
    }
    
    
    public function __toString() {
        return $this->get_string();
    }
}
