<?php
/**
 * @package DF_URL
 */


require_once 'DF/Error/InvalidArgumentException.php';

require_once 'DF/URL/I.php';
require_once 'DF/URL/MalformedURLException.php';
require_once 'DF/URL/Path.php';


/**
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
class DF_URL implements DF_URL_I {
    
    /**
     * @var DF_URI_I
     */
    protected $uri  = NULL;
    
    protected $user = NULL;
    protected $host = NULL;
    protected $port = NULL;
    protected $path = NULL;
    
    protected $authority    = NULL;
    
    
    
    /**
     * Constructor.
     * 
     * @param DF_URI_I $uri
     * @return DF_URL
     */
    public function __construct($uri) {
        if (NULL === $uri) {
            throw new InvalidArgumentException("Not set: uri");
        }
        
        if (!$uri instanceof DF_URI_I) {
            $class = get_class($uri);
            throw new DF_Error_InvalidArgumentException("uri", $uri, "DF_URI_I");
        }
        
        $this->uri = $uri;
        
        $this->setup_hierarchical($uri->get_hierarchical());
    }
    
    
    /**
     * Factory method.
     * 
     * Creates a DF_URL object from a string.
     * Creates a DF_URI object that gets passed to the DF_URL constructor.
     * 
     * Depends on DF_URI
     * 
     * @param string $string
     * @return DF_URL
     */
    static public function fromString($string) {
        require_once 'DF/URI.php';
        $uri = new DF_URI($string);
        $self = new DF_URL($uri);
        return $self;
    }
    
    
    static private function string_parse_authority($string) {
        $user   = NULL;
        $host   = NULL;
        $port   = NULL;
        
        $rest = $string;
        
        if (preg_match('#^(.*)@(.*)#', $rest, $m)) {
            $user = $m[1];
            $rest = $m[2];
        }
        
        if (preg_match('#(.*[^:]):(.+)$#', $rest, $m)) {
            $rest = $m[1];
            $port = $m[2];
            
            if (!preg_match('#^[a-z0-9-]+$#', $port)) {
                throw new DF_URL_MalformedURLException("Bad port number of name: $port");
            }
        }
        
        $host = $rest;
        
        return array($user, $host, $port);
    }
    
    
    /**
     * @param DF_URI_I $uri
     * @return string
     */
    private function setup_hierarchical($hier) {
        # Requires at least a character of the authority and a "/" of the path
        if (preg_match('#^//([^/]+)/(.*)#', $hier, $m)) {
            $authority  = $m[1];

            // All paths are relative, this one is relative to the root
            $path       = new DF_URL_Path($m[2]);
            
            $this->authority    = $authority;
            $this->path         = $path;
            
            list($user, $host, $port) = self::string_parse_authority($authority);
            $this->user = $user;
            $this->host = $host;
            $this->port = $port;
        }
        else {
            throw new DF_URL_MalformedURLException("Missing authority or path: $hier");
        }
    }
    
    
    public function get_scheme() {
        return $this->uri->get_scheme();
    }
    
    
    public function get_query() {
        return $this->uri->get_query();
    }
    
    
    public function get_fragment() {
        return $this->uri->get_fragment();
    }

    
    public function get_hierarchical() {
        return $this->uri->get_hierarchical();
    }
    
    
    public function get_authority() {
        return $this->authority;
    }
    
    
    public function get_path() {
        return $this->path;
    }
    
    
    public function get_user() {
        return $this->user;
    }
    
    
    public function get_host() {
        return $this->host;
    }
    
    
    public function get_port() {
        return $this->port;
    }
    
    
    protected function get_string() {
        return $this->uri->get_string();
    }
    
    
    public function __toString() {
        return $this->get_string();
    }
    
}
