<?php
/**
 * @package DF_Web
 */


/**
 */
class DF_Web_Routing_Config {
    protected $name         = NULL;
    protected $config       = NULL;

    protected $path         = NULL;


    /**
     * Constructor.
     * 
     * @param DF_Web_Routing_Component_Name $name
     * @param array $config
     * @return DF_Web_Routing_Config
     */
    public function __construct($name, $config) {
        if (!$name) {
            throw new InvalidArgumentException("Not set: name");
        }

        if (!$name instanceof DF_Web_Routing_Component_Name) {
            $was = gettype($name);
            throw new InvalidArgumentException("Not of type DF_Web_Routing_Component_Name: name, was $was");
        }

        if (!is_array($config)) {
            throw new InvalidArgumentException("Not an array: config");
        }
    
        $this->name         = $name;
        $this->config       = $config;
    }


    public function __toString() {
        $path = $this->path;
        $name   = $this->name;
        return "$name";
    }


    public function get_name() {
        return $this->name;
    }


    public function get_path() {
        return $this->path;
    }


    public function get($key) {
        return $this->config[$key];
    }


    public function has($key) {
        if (isset($this->config[$key])) {
            return true;
        }

        return false;
    }
}
