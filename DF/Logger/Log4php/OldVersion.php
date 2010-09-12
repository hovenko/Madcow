<?php

class DF_Logger_Log4php_OldVersion {

    static private $INSTANCE = NULL;

    static public function singleton() {
        if ($i = self::$INSTANCE) {
            return $i;
        }

        self::$INSTANCE = new DF_Logger_Log4php_OldVersion();
        return self::$INSTANCE;
    }


    static public function isSupported() {
        @include_once 'log4php/LoggerManager.php';
        if (class_exists('LoggerManager')) {
            return true;
        }

        return false;
    }


    public function logger($name) {
        return LoggerManager::getLogger($name);
    }

    public function shutdown() {
        LoggerManager::shutdown();
    }

}
