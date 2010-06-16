<?php
/**
 * @package DF_Web
 */


/**
 */
class DF_Web_Routing_ActionFactory {


    static public function resolve($config) {
        $action = NULL;
        
        if (self::is_chained($config)) {
            $action = new DF_Web_Routing_Action_Chained($config);
        }
        else {
            $action = new DF_Web_Routing_Action_Path($config);
        }

        return $action;
    }


    static protected function is_chained($config) {
        return $config->has('chained');
    }

}

require_once 'DF/Web/Routing/Action/Chained.php';
require_once 'DF/Web/Routing/Action/Path.php';

