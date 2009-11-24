<?php

class DF_Web_Routing_Action_PathMismatchException extends DF_Web_Exception {
    public function __construct($msg, $code = 0) {
        parent::__construct($msg, $code);
    }

}
