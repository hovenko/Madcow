<?php
/**
 * @package DF_Web
 */


/**
 */
class DF_Web_Component_Name {
    protected $string = NULL;


    /**
     * Constructor.
     * 
     * @param string $string
     * @return DF_Web_Component_Name
     */
    public function __construct($string) {
        if (!is_string($string)) {
            throw new DF_Error_InvalidArgumentException("string", $string, "string");
        }

        $this->string = $string;
    }


    public function __toString() {
        return $this->string;
    }

}

require_once 'DF/Error/InvalidArgumentException.php';

