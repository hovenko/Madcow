<?php
/**
 * @package DF_Web
 */


require_once 'DF/Error/InvalidArgumentException.php';


/**
 * An object instance of this class represents an action to execute in a
 * controller.
 * 
 * Multiple actions can be stacked and will be executed in the order they
 * are stacked.
 * 
 * When forwarding to a method in the same or a new controller, a new action
 * instance is created and placed on top of the stack, to be executed
 * immediately.
 * 
 * An object of this class is immutable. Respect that when subclassing.
 */
class DF_Web_Action {
    public static $LOGGER = NULL;
    
    protected $controller   = NULL;
    protected $method       = NULL;
    protected $arguments    = NULL;


    /**
     * Constructor.
     * 
     * @param string $controller
     * @param string $method
     * @param array $args
     * @return DF_Web_Action
     */
    public function __construct($controller, $method, $args) {
        if (!is_string($controller)) {
            throw new DF_Error_InvalidArgumentException('controller', $controller, 'string');
        }

        if (!is_string($method)) {
            # TODO Could this parameter be anything other than string?
            throw new DF_Error_InvalidArgumentException('method', $method, 'string');
        }

        if ($args && !is_array($args)) {
            throw new DF_Error_InvalidArgumentException('args', $args, 'array');
        }
    
        $this->controller   = $controller;
        $this->method       = $method;
        $this->arguments    = $args;
    }


    public function get_controller() {
        return $this->controller;
    }


    public function get_method() {
        return $this->method;
    }


    public function get_arguments() {
        return $this->arguments;
    }


    public function execute($c) {
        if (!$c instanceof DF_Web) {
            throw new DF_Error_InvalidArgumentException("c", $c, DF_Web);
        }
        
        $ctrl_name      = $this->get_controller();;
        $method_name    = $this->get_method();
        $arguments      = $this->get_arguments();

        if ($c->is_debug())
        self::$LOGGER->debug("Controller: $ctrl_name, Action: $method_name");

        $controller     = $c->controller($ctrl_name);

        # Prepend the context object to the argumentlist
        if ($arguments == NULL) {
            $arguments = array();
        }
        array_unshift($arguments, $c);

        if (!method_exists($controller, $method_name)) {
            $argstr = DF_Web_Utils_Arguments::flatten_arguments_list($arguments);
            throw new DF_Web_Exception("No such method: ${ctrl_name}->${method_name}($argstr)");
        }
        # TODO how can I check the PHP access level of a method?
        #elseif (!is_callable($controller, $method_name)) {
        #    throw new DF_Web_Exception("Method is not public: $ctrl_name::$method_name");
        #}

        try {
            $ret = call_user_func_array(
                array($controller, $method_name),
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
        $args = DF_Web_Utils_Arguments::flatten_arguments_list($this->arguments);
        $ctrl   = $this->controller;
        $method = $this->method;

        return "{$ctrl}->{$method}({$args})";
    }
}

DF_Web_Action::$LOGGER = DF_Web_Logger::logger('DF_Web_Action');
