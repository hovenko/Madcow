<?php
/**
 * @package DF_Web
 */


require_once 'DF/Web/Routing/ActionArgs.php';
require_once 'DF/Web/Routing/ActionArgs/Any.php';


/**
 */
class DF_Web_Routing_Action {
    public static $LOGGER = NULL;

    protected $name         = NULL;
    protected $path         = NULL;
    protected $args         = NULL;

    protected $controller   = NULL;


    /**
     * Constructor.
     * 
     * @param DF_Web_Routing_Config_Action $config
     * @return DF_Web_Routing_Action
     */
    public function __construct($config) {
        if (!$config) {
            throw new InvalidArgumentException("Not set: config");
        }

        if (! $config instanceof DF_Web_Routing_Config_Action) {
            throw new InvalidArgumentException("Not of type DF_Web_Routing_Config_Action: config");
        }

        $ctrl = $config->get_controller();
        $this->controller   = $ctrl;

        $this->name     = $config->get_name();
        $this->path     = $config->get_path();
        $this->args     = $this->prepare_args($config);

        $this->init_local($config);
    }


    protected function init_local($config) {}


    protected function prepare_args($config) {
        $args = NULL;

        if ($config->has('args')) {
            $numargs = $config->get('args');

            if (is_int($numargs)) {
                $args = new DF_Web_Routing_ActionArgs($numargs);
            }
            else {
                $args = new DF_Web_Routing_ActionArgs_Any();
            }
        }

        return $args;
    }


    public function get_path_match() {
        $stars = array();
        $args = $this->args;
        if ($args instanceof DF_Web_Routing_ActionArgs_Any) {
            $stars[] = "**";
        }
        else {
            for ($i=0; $i<$args->get_numargs(); $i++) {
                $stars[] = "*";
            }
        }

        $argspath = join("/", $stars);

        $path = $this->get_path();
        if (!strlen("$path")) {
            $path = $this->controller->get_path();
        }
        elseif (!$path->is_absolute()) {
            $path = $this->controller->get_path()->append_path($path);
        }

        if ($argspath) {
            $path = $path->append_path(DF_Web_Path::fromString($argspath));
        }

        if (!$path->is_absolute()) {
            $root = DF_Web_Path::fromString("/");
            $path = $root->append_path($path);
        }
        
        return $path;
    }


    public function get_private_path() {
        $name   = $this->get_name();
        $ctrl   = $this->get_controller();
        $ctrlpath   = $ctrl->get_path();

        $private_path = "$name";
        if (strlen($private_path)) {
            $private_path = "/$private_path";
        }

        if ("$ctrlpath") {
            $private_path = "/$ctrlpath$private_path";
        }

        return DF_Web_Path::fromString($private_path);
    }


    public function get_args() {
        return $this->args;
    }


    public function get_name() {
        return $this->name;
    }


    public function get_controller() {
        return $this->controller;
    }


    public function get_path() {
        return $this->path;
    }


    public function get_method() {
        $name = $this->name;
        $method = "handle_$name";
        return $method;
    }


    public function get_code_ref() {
        $ctrl = $this->get_controller()->get_name()."";
        $method = $this->get_method();
        $ref = array($ctrl, $method);
        return $ref;
    }


    public function __toString() {
        $ctrl   = $this->get_controller()->get_name()."";
        $method = $this->get_method();

        $path_match = $this->get_path_match();

        $name   = $this->name;
        $path   = $this->path;
        $args   = $this->args;
        
        $str = "$ctrl::$method Pathmatch: $path_match, Name: $name, Path: $path, Args: $args";
        return $str;
    }

}
DF_Web_Routing_Action::$LOGGER = DF_Web_Logger::logger('DF_Web_Routing_Action');
