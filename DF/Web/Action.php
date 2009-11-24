<?php
/**
 * @package DF_Web
 */


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
    protected $controller   = NULL;
    protected $method       = NULL;
    protected $arguments    = NULL;


    /**
     * Constructor.
     * 
     * @param string $controller
     * @param string $method
     * @param array $args optional
     * @return DF_Web_Action
     */
    public function __construct($controller, $method, $args) {
        if (!$controller) {
            throw new InvalidArgumentException("Not set: controller");
        }

        if (!is_string($controller)) {
            throw new InvalidArgumentException("Not a string: controller");
        }

        if (!$method) {
            throw new InvalidArgumentException("Not set: method");
        }

        if ($args && !is_array($args)) {
            throw new InvalidArgumentException("Not an array: args");
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
