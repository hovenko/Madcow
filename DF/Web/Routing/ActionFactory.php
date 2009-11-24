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
            require_once 'DF/Web/Routing/Action/Chained.php';
            $action = new DF_Web_Routing_Action_Chained($config);
        }
        else {
            require_once 'DF/Web/Routing/Action/Path.php';
            $action = new DF_Web_Routing_Action_Path($config);
        }

        return $action;
    }


    static protected function is_chained($config) {
        return $config->has('chained');
    }

}
