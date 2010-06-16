<?php

require_once 'DF/Web/Component.php';
require_once 'DF/Web/Config.php';
require_once 'DF/Web/Exception.php';

class DF_Web_Component_Loader {

    private static $_components = array();

    private static function load_file_by_classname($name) {
        $filename = preg_replace('|_|', '/', $name);
        $filename .= ".php";

        try {
            include_once ($filename);
            if (!class_exists($name)) {
                throw new DF_Web_Exception("Unable to find class $name");
            }
            return true;
        }
        catch (Exception $ex) {
            if ($ex instanceof DF_Web_Exception) {
                throw $ex;
            }
            else {
                throw new DF_Web_Exception("File could not be loaded: ".$ex->getMessage());
            }
        }
    }

    public static function component($name, $context) {
        $base = NULL;

        if (NULL === $context) {
            throw new DF_Web_Exception("A context object must be given");
        }
        elseif (is_string($context)) {
            $base = $context;
        }
        elseif (!$context instanceof DF_Web) {
            throw new DF_Error_InvalidArgumentException("context", $context, DF_Web);
        }
        else {
            $base = $context->get_context_class();
        }
    
        if (array_key_exists($name, self::$_components)) {
            return self::$_components[$name];
        }

        if (!class_exists($name)) {
            if (!self::load_file_by_classname($name)) {
                throw new DF_Web_Exception("Component class does not exist: $name");
            }
        }

        $component = new $name();

        if (! ($component instanceof DF_Web_Component)) {
            throw new DF_Web_Exception("Class $name does not implement DF_Web_Component");
        }

        $configname = self::config_name($name, $base);
        $config = DF_Web_Config::find_by_id($configname);
        if ($config == NULL) {
            $config = array();
        }

        $component->config($config);

        $component->initialize($context);

        self::$_components[$name] = $component;

        return $component;
    }


    static public function config_name($classname, $base) {
        $config_name = str_replace($base."_", "", $classname);
        return $config_name;
    }
}
