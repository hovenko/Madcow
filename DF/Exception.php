<?php
/**
 * @package DF_Exception
 */




/**
 * Base exception class for all DF libraries.
 * 
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
class DF_Exception extends Exception {
    public function __construct($msg, $code = 0) {
        parent::__construct($msg, $code);
    }
}
