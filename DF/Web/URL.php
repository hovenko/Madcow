<?php


require_once 'DF/Web/HTTP/Path.php';
require_once 'DF/Web/Path/Part.php';


class DF_Web_URL {

    protected $string   = NULL;

    protected $schema   = NULL;
    protected $host    = NULL;
    protected $port    = NULL;
    protected $path    = NULL;
    protected $query    = NULL;


    public function __construct($string) {
        if (NULL === $string) {
            throw new InvalidArgumentException("Not set: string");
        }

        if (!is_string($string)) {
            throw new InvalidArgumentException("Not a string: string");
        }

        $this->string = $string;

        $this->init_url($string);
    }


    protected function init_url($string) {
        $schema = NULL;
        $host   = NULL;
        $port   = NULL;
        $path   = NULL;
        $query  = NULL;

        if (preg_match('#^([^:]+)://([^:/]+)(?::([^/]))?(/[^\?]*)(?:\?(.*))?#', $string, $matches)) {
            $schema = $matches[1];
            $host   = $matches[2];
            $port   = $matches[3];
            $path   = $matches[4];
            $query  = $matches[5];
        }
        else {
            throw new InvalidArgumentException("URL malformed: $string");
        }

        $this->schema   = $schema;
        $this->host     = $host;
        $this->port     = $port;
        $this->path     = $path;
        $this->query    = $query;
    }


    public function __toString() {
        $schema = $this->schema;
        $host   = $this->host;
        $port   = $this->port;
        $path   = $this->path;
        $query  = $this->query;

        $str = "$schema://$host";

        if ($port) {
            $str .= ":$port";
        }
        
        $str .= "$path";

        if ($query) {
            $str .= "?$query";
        }

        return $str;
    }


    public function get_path() {
        $path = new DF_Web_Path($this->path);
        return $path;
    }

    
}
