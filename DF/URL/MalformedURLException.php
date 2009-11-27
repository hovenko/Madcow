<?php
/**
 * @package DF_URL
 */


require_once 'DF/Exception.php';


/**
 * Exception class for malformed URLs.
 * 
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
class DF_URL_MalformedURLException extends DF_Exception {
    public function __construct($msg, $code = 0) {
        parent::__construct($msg, $code);
    }
}
