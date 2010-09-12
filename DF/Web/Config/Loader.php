<?php
/**
 * @package DF_Web
 */



/**
 * This class reads and holds the configuration parameters for the
 * DF_Web framework.
 *
 * @package DF_Web
 */
class DF_Web_Config_Loader {
    
    /**
     * This holds the configuration structure.
     *
     * @var array
     */
    protected $config       = NULL;
    
    
    /**
     * Holds the path to the configuration file.
     *
     * @var string
     */
    protected $configFile   = NULL;
    
    
    
    /**
     * Constructor
     *
     * @param string $file
     * @return DF_Web_Config_Loader
     */
    public function __construct($file) {
        if (!$file) {
            throw new Exception("Missing path to configuration file");
        }
        
        $this->configFile   = $file;
    }
    
    
    /**
     * The configuration is lazy loaded. It is parsed the first time this
     * method is called, and then it will be cached for later usage during
     * the lifetime of this instance.
     *
     * @return array
     */
    public function getConfig() {
        if ($this->config) {
            return $this->config;
        }
        
        $this->config = $this->parse($this->configFile);
        return $this->config;
    }
    
    
    /**
     * This method prints the configuration data structure.
     */
    public function dump() {
        print_r($this->getConfig());
    }
    
    
    /**
     * This method parses the configuration file and returns the data structure.
     *
     * @param string $file
     * @return array
     */
    protected function parse($file) {
        self::loadSpycLibrary();
        $config = Spyc::YAMLLoad($file);
        
        if (!$config) {
            throw new Exception("Configuration is either empty or contains fatal errors");
        }
        
        return $config;
    }


    static private function loadSpycLibrary() {
        if (!class_exists('Spyc')) {
            @include_once 'Spyc/spyc.php5';
        }

        if (!class_exists('Spyc')) {
            @include_once 'spyc.php';
        }

        if (!class_exists('Spyc')) {
            throw new DF_Exception("YAML Spyc library not found");
        }
    }
}


