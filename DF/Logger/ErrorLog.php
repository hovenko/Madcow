<?php


class DF_Logger_ErrorLog {
    
    protected $name = NULL;

    public static function logger($name) {
        return new DF_Logger_ErrorLog($name);
    }


    public function __construct($name) {
        $this->name = $name;
    }


    protected function _log($msg, $sev) {
        $sev_str = self::severity_to_string($sev);
        $name   = $this->name;
        error_log("$name [$sev_str] $msg");
    }


    public function warn($msg) {
        $this->_log($msg, 'warn');
    }


    public function debug($msg) {
        $this->_log($msg, 'debug');
    }


    static protected function severity_to_string($sev) {
        switch ($sev) {
            default:
                return strtoupper($sev);
        }
    }
}

