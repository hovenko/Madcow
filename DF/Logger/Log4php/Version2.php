<?php


/**
 * Supports log4php version 2 (Apache project)
 */
class DF_Logger_Log4php_Version2 {
    static private $INSTANCE = NULL;

    static public function singleton() {
        if ($i = self::$INSTANCE) {
            return $i;
        }

        self::$INSTANCE = new DF_Logger_Log4php_Version2();
        return self::$INSTANCE;
    }

    public function logger($name) {
        return Logger::getLogger($name);
    }

    public function shutdown() {
        Logger::shutdown();
    }


    static public function isSupported() {
        @include_once 'log4php/Logger.php';
        if (class_exists('Logger')) {
            if (!method_exists('Logger', 'configure')) {
                return false;
            }
            Logger::configure(LOG4PHP_CONFIGURATION);
            return true;
        }

        return false;
    }
  

}
