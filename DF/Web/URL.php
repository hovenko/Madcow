<?php


require_once 'DF/URL.php';
require_once 'DF/URL/I.php';

require_once 'DF/Web/HTTP/Path.php';
require_once 'DF/Web/Path/Part.php';


class DF_Web_URL implements DF_URL_I {

    protected $url      = NULL;

    protected $path     = NULL;



    public function __construct($url) {
        if (NULL === $url) {
            throw new InvalidArgumentException("Not set: url");
        }

        if (!$url instanceof DF_URL_I) {
            throw new DF_Error_InvalidArgumentException("url", $url, "DF_URL_I");
        }

        $this->url = $url;

        $this->path = new DF_Web_Path($url->get_path());
    }


    static public function fromString($string) {
        $url = DF_URL::fromString($string);
        return new DF_Web_URL($url);
    }


    public function __toString() {
        return "".$this->url;
    }


    public function get_path() {
        return $this->path;
    }


    public function get_authority() {
        return $this->url->get_authority();
    }
    
    
    public function get_user() {
        return $this->url->get_user();
    }

    
    public function get_host() {
        return $this->url->get_host();
    }

    
    public function get_port() {
        return $this->url->get_port();
    }


    public function get_scheme() {
        return $this->url->get_scheme();
    }

    
    public function get_hierarchical() {
        return $this->url->get_hierchical();
    }

    
    public function get_query() {
        return $this->url->get_query();
    }

    
    public function get_fragment() {
        return $this->url->get_fragment();
    }

}
