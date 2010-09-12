<?php
/**
 * @package DF_Exception
 */


require_once 'DF/Exception.php';


/**
 * 
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
class DF_Logger_NotSupportedException extends DF_Exception {
    public function __construct($msg, $code = 0) {
        parent::__construct($msg, $code);
    }
}
