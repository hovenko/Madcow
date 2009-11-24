<?php

require_once 'DF/Web/Component/Loader.php';


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
        if ($config === NULL) {
            return $this->config;
        }

        $this->config = array_merge($this->config, $config);
    }

    
    /**
     * Called after configuration is set.
     */
    public function initialize() {
        // does nothing. you should override if you need it
    }
}
