<?php

require_once 'DF/Web/Component/Loader.php';
require_once 'DF/Web/Utils/Config.php';


/**
 * Base class for all framework components.
 *
 * Be minded that no classes that implement this class should have its own constructor.
 */
class DF_Web_Component {
    protected $config = array();

    public function __construct() {
        // empty
    }

    public function config($config = NULL) {
        $this->config = DF_Web_Utils_Config::merge_hashes($this->config, $config);
        return $this->config;
    }

    
    /**
     * Called after configuration is set.
     * 
     * @param DF_Web $c
     */
    public function initialize($c) {
        // does nothing. you should override if you need it
    }
}
