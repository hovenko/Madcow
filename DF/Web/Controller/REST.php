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

#    public function handle_auto($c) {
#        // Override if you like
#    }
#
#    public function handle_index($c) {
#        // Override if you like
#    }
#
#    public function handle_begin($c) {
#        // Override if you like
#    }

    public function handle_end($c) {
        $this->stash['current_view'] = '';
    }
}
