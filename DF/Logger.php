<?php

class DF_Logger {
    static private $activeLogger    = NULL;
    static private $loggers         = array();
    
    static public function setActiveLogger($name) {
        self::$activeLogger = $name;
    }
    
    static public function logger($name) {
        switch (self::$activeLogger) {
            case 'log4php':
                return self::logger_log4php($name);

            case 'standalone':
                return self::logger_standalone($name);

            case 'error_log':
                return self::logger_errorlog($name);
                
            default:
                return self::logger_errorlog($name);
        }
    }

    static private function logger_log4php($name) {
        require_once 'DF/Logger/Log4php.php';
        self::$loggers["DF_Logger_Log4php"] = 1;
        return DF_Logger_Log4php::logger($name);
    }

    static private function logger_standalone($name) {
        require_once 'DF/Logger/Standalone.php';
        self::$loggers["DF_Logger_Standalone"] = 1;
        return DF_Logger_Standalone::logger($name);
    }


    static private function logger_errorlog($name) {
        require_once 'DF/Logger/ErrorLog.php';
        self::$loggers["DF_Logger_ErrorLog"] = 1;
        return DF_Logger_ErrorLog::logger($name);
    }


    static public function shutdown() {
        foreach (self::$loggers as $logger => $enabled) {
            if ($enabled) {
                call_user_func(array($logger, "shutdown"));
            }
        }
    }
}
