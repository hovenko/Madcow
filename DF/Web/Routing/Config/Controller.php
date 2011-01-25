<?php
/**
 * @package DF_Web
 */


require_once 'DF/Web/Component/Name.php';
require_once 'DF/Web/Path.php';
require_once 'DF/Web/Routing/Config.php';
require_once 'DF/Web/Routing/Config/Action.php';
require_once 'DF/Web/Utils/Components.php';


/**
 */
class DF_Web_Routing_Config_Controller
        extends DF_Web_Routing_Config {

    protected $actions      = NULL;
    protected $namespace    = NULL;


    /**
     * Constructor.
     * 
     * @param string $name
     * @param array $config
     * @return DF_Web_Routing_Config_Controller
     */
    public function __construct($name, $config) {
        parent::__construct($name, $config);

        $this->actions      = $this->prepare_actions();
        $this->namespace    = $this->prepare_namespace();

        $this->path         = $this->prepare_path();

        # Unsure how to remove the config if this class is ever subclassed
        unset($this->config);
    }


    protected function prepare_namespace() {
        $ns     = NULL;
        $name   = $this->name;

        if ($this->has('namespace')) {
            $ns = $this->get('namespace');
        }
        else {
            $ns = DF_Web_Utils_Components::name_to_path($name);
        }

        return $ns;
    }


    /**
     * @return DF_Web_Path
     */
    protected function prepare_path() {
        $path = DF_Web_Path::fromString($this->namespace);

        return $path;
    }


    public function prepare_actions() {
        $action_cfgs = array();

        foreach ($tmp = $this->get_config_actions() as $name => $config) {
            $name = new DF_Web_Component_Name($name);
            $action_cfg = new DF_Web_Routing_Config_Action($name, $config, $this);
            $name       = $action_cfg->get_name();
            $action_cfgs["$name"] = $action_cfg;
        }

        return $action_cfgs;
    }


    public function get_actions() {
        return $this->actions;
    }


    public function get_namespace() {
        return $this->namespace;
    }


    protected function get_config_actions() {
        if ($this->has('actions')) {
            $actions = $this->get('actions');
            if (!is_array($actions)) {
                throw new DF_Error_InvalidArgumentException('Controller configuration "actions"', $actions, "array");
            }
            return $actions;
        }

        return array();
    }


    protected function has_namespace() {
        return $this->has('namespace');
    }


}
