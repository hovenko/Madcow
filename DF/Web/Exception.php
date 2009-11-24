<?php

class DF_Web_Exception extends Exception {
    public function __construct($msg, $code = 0) {
        parent::__construct($msg, $code);
    }

}
