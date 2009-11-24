<?php
/**
 * @package DF_Web
 */


require_once 'DF/Web/Path.php';
require_once 'DF/Web/Routing/Action.php';
require_once 'DF/Web/Routing/Config/Action.php';


/**
 */
class DF_Web_Routing_Action_Path
        extends DF_Web_Routing_Action {

    protected $config       = NULL;


    protected function init_local($config) {
        parent::init_local($config);

        $this->config = $config;
        
        $this->path     = $this->prepare_path();

        unset($this->config);
    }


    protected function prepare_path() {
        $config = $this->config;

        $path = $config->get_path();

        return $path;
    }

}
