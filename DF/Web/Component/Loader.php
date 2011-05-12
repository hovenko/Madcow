<?php

require_once 'DF/Web/Component.php';
require_once 'DF/Web/Config.php';
require_once 'DF/Web/Exception.php';

class DF_Web_Component_Loader {

    private $_components = array();

    private static function load_file_by_classname($name) {
        $filename = preg_replace('|_|', '/', $name);
        $filename .= ".php";

        try {
            include_once ($filename);
            if (!class_exists($name)) {
                throw new DF_Web_Component_LoaderException("Unable to find class $name");
            }
            return true;
        }
        catch (Exception $ex) {
            if ($ex instanceof DF_Web_Exception) {
                throw $ex;
            }
            else {
                throw new DF_Web_Component_LoaderException("File could not be loaded: ".$ex->getMessage());
            }
        }
    }


    /**
     * @var DF_Web
     */
    private $c = NULL;


    public function __construct($context) {
        if (!$context instanceof DF_Web) {
            throw new DF_Error_InvalidArgumentException("context", $context, DF_Web);
        }

        $this->c = $context;
    }

    public function component($name) {
        $base = NULL;

        $context = $this->c;

        $base = $context->get_context_class();
    
        if (array_key_exists($name, $this->_components)) {
            return $this->_components[$name];
        }

        if (!class_exists($name)) {
            if (!self::load_file_by_classname($name)) {
                throw new DF_Web_Component_LoaderException("Component class does not exist: $name");
            }
        }

        $component = new $name();

        if (! ($component instanceof DF_Web_Component)) {
            throw new DF_Web_Component_LoaderException("Class $name does not implement DF_Web_Component");
        }

        if ($config = $component->config()) {
            $config = $context->resolve_config_placeholders($config);
            $component->set_config($config);
        }

        $configname = self::config_name($name, $base);
        $config = $this->c->config()->find_by_id($configname);
        if ($config == NULL) {
            $config = array();
        }

        $component->config($config);

        $component->initialize($context);

        $this->_components[$name] = $component;

        return $component;
    }


    static public function config_name($classname, $base) {
        $config_name = str_replace($base."_", "", $classname);
        return $config_name;
    }
}


require_once 'DF/Web/Component/LoaderException.php';

