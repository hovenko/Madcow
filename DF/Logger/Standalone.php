<?php

require_once 'Log.php';

class DF_Logger_Standalone {
    public static function logger($name) {
        return Log::factory('error_log', PEAR_LOG_TYPE_SYSTEM, $name);
    }

    static public function shutdown() {
        # nothing
    }
}

if (!defined('STDERR')) {
    define('STDERR', 'php://stderr');
}

if (!defined('STDOUT')) {
    define('STDOUT', 'php://stdout');
}
