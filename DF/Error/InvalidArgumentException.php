<?php

require_once 'DF/Error.php';

class DF_Error_InvalidArgumentException extends InvalidArgumentException
        implements DF_Error {
    protected $name = NULL;
    protected $type = NULL;
    protected $expected = NULL;
    protected $message  = NULL;

    public function __construct($name, $value, $expected) {
        if (!is_string($name)) {
            throw new InvalidArgumentException("Not a string: name");
        }
        
        if (!is_string($expected)) {
            throw new InvalidArgumentException("Not a string: expected");
        }

        $lead = "Invalid";
        if (NULL === $value) {
            $lead = "Not set";
        }
    
        $type = gettype($value);
        $got_msg = $type;
        if (is_object($value)) {
            $class = get_class($value);
            $got_msg = "$type ($class)";
        }

        $this->name = $name;
        $this->type = $type;
        $this->expected = $expected;
        $this->message = $msg;
        
        $msg = "$lead: $name - Got: $got_msg - Expected: $expected";
        parent::__construct($msg, 0);
    }

}
