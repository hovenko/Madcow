<?php
/**
 * @package DF_Web
 */


require_once 'DF/Web/Path.php';
require_once 'DF/Web/Routing/Config.php';


/**
 */
class DF_Web_Routing_Config_Action
        extends DF_Web_Routing_Config {

    protected $controller;


    /**
     * Constructor.
     * 
     * @param string $name
     * @param array $config
     * @param DF_Web_Routing_Config_Controller $controller
     * @return DF_Web_Routing_Config_Action
     */
    public function __construct($name, $config, $controller) {
        parent::__construct($name, $config);
        $this->controller = $controller;

        $this->path         = $this->prepare_path();
    }


    protected function prepare_path() {
        $path = NULL;

        if ($this->has('path')) {
            $path = $this->get('path');
        }
        else {
            $path = $this->get_name();
        }

        return DF_Web_Path::fromString("$path");
    }


    /**
     * @return DF_Web_Routing_Config_Controller
     */
    public function get_controller() {
        return $this->controller;
    }




}
