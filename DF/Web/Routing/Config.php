<?php
/**
 * @package DF_Web
 */


/**
 */
abstract class DF_Web_Routing_Config {
    protected $name         = NULL;
    protected $config       = NULL;

    /**
     * @var DF_Web_Path
     */
    protected $path         = NULL;


    /**
     * Constructor.
     * 
     * @param DF_Web_Routing_Component_Name $name
     * @param array $config
     * @return DF_Web_Routing_Config
     */
    public function __construct($name, $config) {
        if (!$name instanceof DF_Web_Component_Name) {
            throw new DF_Error_InvalidArgumentException("name", $name, DF_Web_Component_Name);
        }

        if (!is_array($config)) {
            throw new DF_Error_InvalidArgumentException("config", $config, "array");
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


    /**
     * @return DF_Web_Path
     */
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

require_once 'DF/Error/InvalidArgumentException.php';

