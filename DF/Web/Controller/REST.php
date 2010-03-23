<?php

require_once 'DF/Web/Action/REST.php';
require_once 'DF/Web/Controller.php';

class DF_Web_Controller_REST extends DF_Web_Controller {

    /**
     * Called after configuration is set.
     * 
     * @param DF_Web $c
     */
    public function initialize($c) {
        $c->register_action_class(DF_Web_Action_REST);
    }


}
