<?php
/**
 * @package DF_Web
 */


/**
 */
class DF_Web_Routing_ActionArgs {
    protected $numargs  = NULL;


    /**
     * Constructor.
     * 
     * @param integer $numargs
     * @return DF_Web_Routing_ActionArgs
     */
    public function __construct($numargs) {
        if (NULL === $numargs) {
            throw new InvalidArgumentException("Not set: numargs");
        }

        if (!is_int($numargs)) {
            throw new InvalidArgumentException("Not an integer: numargs");
        }

        $this->numargs  = $numargs;
    }


    public function get_numargs() {
        return $this->numargs;
    }


    public function __toString() {
        return "".$this->numargs;
    }
}
