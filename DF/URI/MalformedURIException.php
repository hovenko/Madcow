<?php
/**
 * @package DF_URI
 */


require_once 'DF/Exception.php';


/**
 * Exception class for malformed URIs.
 * 
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
class DF_URI_MalformedURIException extends DF_Exception {
    public function __construct($msg, $code = 0) {
        parent::__construct($msg, $code);
    }
}
