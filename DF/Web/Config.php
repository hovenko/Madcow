<?php

require_once 'File.php';

require_once 'DF/Web/Config/Loader.php';
require_once 'DF/Web/Environment.php';
require_once 'DF/Web/Logger.php';


# TODO add environment configurable path to read configuration
class DF_Web_Config {
    public static $LOGGER = NULL;

    private $_config = NULL;
    public static $basename = "config";


    public function __construct() {
    }


    public function find_by_id($id) {
        $config = $this->get_config();
        if (!array_key_exists($id, $config)) {
            return array();
        }

        return $config[$id];
    }


    public function get_config() {
        if ($this->_config == NULL) {
            $config = $this->read_config();
            $config = DF_Util_Arrays::asArray($config);
            $this->_config = $config;
        }

        return $this->_config;
    }

    
    private function read_config() {
        $env = DF_Web_Environment::singleton();
        $environment= $env->environment;
        $app_root   = $env->app_root;
        $basename   = self::$basename;

        if ($env->debug)
        self::$LOGGER->debug("Looking up configuration files in '$app_root'");

        # TODO configurable
        $compiled_file  = File::buildPath(array($app_root, "cache", "$basename-compiled.php"));
        if (file_exists($compiled_file)) {
            global $config;
            require_once "$compiled_file";
            return $config;
        }

        $file       = File::buildPath(array($app_root, "$basename.yaml"));
        $fileenv    = File::buildPath(array($app_root, "$basename-$environment.yaml"));

        $config = array();

        if (file_exists($file)) {
            if ($env->debug)
            self::$LOGGER->debug("Loading configuration file: $file");

            $loader = new DF_Web_Config_Loader($file);
            $config = array_merge($config, $loader->getConfig());
        }

        if (file_exists($fileenv)) {
            if ($env->debug)
            self::$LOGGER->log("Loading configuration file: $fileenv");

            $loader = DF_Web_Config_Loader($fileenv);
            $config = array_merge($config, $loader->getConfig());
        }

        $struct = "<?php\n \$config = ".var_export($config,1).";";
        file_put_contents($compiled_file, $struct);

        return $config;
    }
}

DF_Web_Config::$LOGGER = DF_Web_Logger::logger('DF_Web_Config');

require_once 'DF/Util/Arrays.php';
