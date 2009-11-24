<?php
/**
 * @package DF_Web
 */


/**
 */
class DF_Web_Routing_Component_Name {
    protected $string = NULL;


    /**
     * Constructor.
     * 
     * @param string $string
     * @return DF_Web_Routing_Component_Name
     */
    public function __construct($string) {
        if (NULL === $string) {
            throw new InvalidArgumentException("Not set: string");
        }

        if (!is_string($string)) {
            throw new InvalidArgumentException("Not a string: string");
        }

        $this->string = $string;
    }


    public function __toString() {
        return $this->string;
    }

}
