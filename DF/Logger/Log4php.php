<?php


class DF_Logger_Log4php {
    static public function logger($name) {
        return self::getLog4PhpLoader()->logger($name);
    }

    static public function shutdown() {
        return self::getLog4PhpLoader()->shutdown();
    }


    static private function getLog4PhpLoader() {
        if (DF_Logger_Log4php_Version2::isSupported()) {
            return DF_Logger_Log4php_Version2::singleton();
        }
        if (DF_Logger_Log4php_OldVersion::isSupported()) {
            return DF_Logger_Log4php_OldVersion::singleton();
        }

        throw new DF_Logger_NotSupportedException("Unable to find an installed log4php instance");
    }

}

require_once 'DF/Logger/Log4php/OldVersion.php';
require_once 'DF/Logger/Log4php/Version2.php';
require_once 'DF/Logger/NotSupportedException.php';
