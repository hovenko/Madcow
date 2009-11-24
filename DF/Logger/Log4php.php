<?php

require_once 'log4php/LoggerManager.php';

class DF_Logger_Log4php {
    static public function logger($name) {
        return LoggerManager::getLogger($name);
    }

    static public function shutdown() {
        LoggerManager::shutdown();
    }
}
