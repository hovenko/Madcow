<?php

require_once 'DF/Web/Exception.php';

class DF_Web_Component_LoaderException extends DF_Web_Exception {
    public function __construct($msg, $code = 0) {
        parent::__construct($msg, $code);
    }

}
