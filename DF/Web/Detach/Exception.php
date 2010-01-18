<?php

require_once 'DF/Error/InvalidArgumentException.php';
require_once 'DF/Web/Exception.php';

class DF_Web_Detach_Exception extends DF_Web_Exception {
    private $action = NULL;
    
    public function __construct($action, $msg, $code = 0) {
        if (!$action instanceof DF_Web_Action) {
            throw new DF_Error_InvalidArgumentException("action", $action, "DF_Web_Action");
        }

        parent::__construct($msg, $code);
        $this->action = $action;
    }
    
    public function get_action() {
        return $this->action;
    }
}
