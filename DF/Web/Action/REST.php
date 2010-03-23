<?php
/**
 * @package DF_Web
 */


require_once 'DF/Class/Inspector.php';
require_once 'DF/Error/InvalidArgumentException.php';


/**
 * 
 */
class DF_Web_Action_REST extends DF_Web_Action {
    public static $LOGGER = NULL;
    

    /**
     * Constructor.
     * 
     * @return DF_Web_Action_REST
     */
    public function __construct() {
    }


    public function get_name() {
        return "REST";
    }


    public function initialize($c) {
    }


    /**
     * Returns a list of HTTP methods allowed for an action.
     *
     * @param string $ctrl
     * @param DF_Web $c
     * @param string $name name of the action
     * @return array
     */
    static public function get_allowed_methods($ctrl, $c, $name) {
        $allowed = array();
        $methods = DF_Class_Inspector::methods($ctrl);
        foreach ($methods as $method) {
            if (preg_match("#^$name\_(.+)$#", $method, $m)) {
                $allowed[] = $m[1];
            }
        }

        return $allowed;
    }



    public function dispatch($c, $action) {
        if (!$c instanceof DF_Web) {
            throw new DF_Error_InvalidArgumentException('c', $c, 'DF_Web');
        }

        if (!$action instanceof DF_Web_Action) {
            throw new DF_Error_InvalidArgumentException('action', $action, 'DF_Web_Action');
        }

        $ctrl_name      = $action->get_controller();;
        $method         = $action->get_method();
        $arguments      = $action->get_arguments();
        
        $req            = $c->request;
        $verb           = $req->get_method();
        $verb           = strtoupper($verb);
        $rest_method    = $method."_".$verb;

        $controller     = $c->controller($ctrl_name);

        # Prepend the context object to the argumentlist
        if ($arguments == NULL) {
            $arguments = array();
        }
        array_unshift($arguments, $c);

        if (!method_exists($controller, $rest_method)) {
            $c->response->status(405); #Method not allowed TODO check that it is 405 or other?
            $argstr = DF_Web_Utils_Arguments::flatten_arguments_list($arguments);
            self::$LOGGER->info("Action ${ctrl_name}->$method does not handle $verb");
            #throw new DF_Web_Detach_Exception($action, "Method not allowed: $verb");
            #throw new DF_Web_Exception("Method not allowed: $verb");
            return false;
        }

        try {
            $ret = call_user_func_array(
                array($controller, $rest_method),
                $arguments
            );

            return $ret;
        }
        catch (DF_Web_Detach_Exception $ex) {
            // Rethrow the exception to continue breaking the chain
            throw $ex;
        }
        catch (DF_Web_Exception $ex) {
            $c->add_error($ex);
            self::$LOGGER->error("Fatal exception: ".$ex->getMessage());
        }
        catch (Exception $ex) {
            $c->add_error($ex);
            self::$LOGGER->error("Fatal exception, breaking off chain: ".$ex->getMessage());
            throw new DF_Web_Detach_Exception(
                $this,
                "Got an error we cannot handle"
            );
        }

        return false;
    }


    public function equals($action) {
        if (NULL === $action) {
            return false;
        }
        
        if ($this === $action) {
            return true;
        }

        if (get_class($action) !== get_class($this)) {
            return false;
        }

        if ($this->get_controller() != $action->get_controller()) {
            return false;
        }

        if ($this->get_method() != $action->get_method()) {
            return false;
        }

        if ($this->get_arguments() != $action->get_arguments()) {
            return false;
        }

        return true;
    }


    public function toString() {
        $args = "";
        if ($this->arguments) {
            $args = join(', ', $this->arguments);
        }

        $ctrl   = $this->controller;
        $method = $this->method;

        return "{$ctrl}->{$method}({$args})";
    }
}

DF_Web_Action_REST::$LOGGER = DF_Web_Logger::logger('DF_Web_Action_REST');
