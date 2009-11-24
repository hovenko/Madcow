<?php
/**
 * @package DF_Web
 */


require_once 'DF/Web/Routing/ActionArgs.php';


/**
 */
class DF_Web_Routing_ActionArgs_Any
    extends DF_Web_Routing_ActionArgs {


    /**
     * Constructor.
     * 
     * @return DF_Web_Routing_ActionArgs_Any
     */
    public function __construct() {
        $this->numargs = NULL;
    }



    public function __toString() {
        return "any";
    }
}
