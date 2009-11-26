<?php
/**
 * @package DF_URI
 */


require_once 'DF/URI/I.php';
require_once 'DF/URI/MalformedURIException.php';
require_once 'DF/URI/Scheme.php';


/**
 * 
 */
class DF_URI implements DF_URI_I {
    
    protected $string       = NULL;
    
    /**
     * @var DF_URI_Scheme
     */
    protected $scheme       = NULL;
    
    protected $hierarchical = NULL;
    protected $query        = NULL;
    protected $fragment     = NULL;
    
    
    /**
     * Constructor.
     * 
     * @param string $string
     * @return DF_URI
     */
    public function __construct($string) {
        $this->string = $string;
        
        $this->parseString($string);
    }
    
    
    static public function string_has_scheme($string) {
        if (preg_match('#^([a-z0-9\+\.-]+):#', $string, $m)) {
            return $m[1];
        }
        
        return false;
    }
    
    
    private function parseString($string) {
        $scheme = NULL;
        $hier   = NULL;
        $query  = NULL;
        $frag   = NULL;
        
        $re_scheme          = DF_URI_Scheme::$RE_SCHEME;
        $re_hierarchical    = '[^\?#]+';
        $re_query           = '[^#]*';
        $re_fragment        = '.*';
        
        $re_all = "|^($re_scheme):($re_hierarchical)(?:\?($re_query))?(?:#($re_fragment))?$|";
        
        if (preg_match($re_all, $string, $m)) {
            $scheme     = $m[1];
            $hier       = $m[2];
            $query      = $m[3];
            $frag       = $m[4];
        }
        else {
            throw new DF_URI_MalformedURIException("URI malformed: $string");
        }
        
        $scheme = new DF_URI_Scheme($scheme);
        
        
        $this->scheme       = $scheme;
        $this->hierarchical = $hier;
        $this->query        = $query;
        $this->fragment     = $frag;
    }
    
    
    public function get_string() {
        return $this->string;
    }
    
    
    public function get_scheme() {
        return $this->scheme;
    }
    
    
    public function get_hierarchical() {
        return $this->hierarchical;
    }
    
    
    public function get_query() {
        return $this->query;
    }
    
    
    public function get_fragment() {
        return $this->fragment;
    }
    

    public function __toString() {
        return $this->string;
    }
    
}
