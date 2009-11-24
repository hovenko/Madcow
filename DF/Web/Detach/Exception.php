<?php

require_once 'DF/Web/Exception.php';

class DF_Web_Detach_Exception extends DF_Web_Exception {
    private $action = NULL;
    
    public function __construct($action, $msg, $code = 0) {
        parent::__construct($msg, $code);
        $this->action = $action;
    }
    
    public function get_action() {
        return $this->action;
    }
}
