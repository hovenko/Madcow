<?php

require_once 'File.php';

require_once 'DF/Web/Config/Loader.php';
require_once 'DF/Web/Environment.php';
require_once 'DF/Web/Logger.php';


# TODO add environment configurable path to read configuration
class DF_Web_Config {
    public static $LOGGER = NULL;

    private static $_config = NULL;
    public static $basename = "config";

    public static function find_by_id($id) {
        $config = self::get_config();
        if (!array_key_exists($id, $config)) {
            return array();
        }

        return $config[$id];
    }


    public static function get_config() {
        if (self::$_config == NULL) {
            $env = DF_Web_Environment::singleton();
            $environment= $env->environment;
            $app_root   = $env->app_root;
            $basename   = self::$basename;

            if ($env->debug)
            self::$LOGGER->debug("Looking up configuration files in '$app_root'");

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

            self::$_config = $config;
        }

        return self::$_config;
    }
}

DF_Web_Config::$LOGGER = DF_Web_Logger::logger('DF_Web_Config');

