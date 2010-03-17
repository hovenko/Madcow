<?php

# TODO make autoload for errors
require_once 'DF/Error.php';
require_once 'DF/Error/InvalidArgumentException.php';


class DF_Web_Utils_Arguments {
    public static $LOGGER = NULL;


    static public function flatten_arguments_list($arguments) {
        if (!is_array($arguments)) {
            throw new DF_Error_InvalidArgumentException('arguments', $arguments, 'array');
        }
        
        $str = array();

        foreach ($arguments as $arg) {
            if (NULL === $arg) {
                $str[] = "NULL";
            }
            elseif (gettype($arg) == 'object') {
                $str[] = get_class($arg);
            }
            elseif (gettype($arg) == 'string') {
                $str[] = '"'.$arg.'"';
            }
            elseif (gettype($arg) == 'array') {
                $str[] = 'Array';
            }
            else {
                $type = gettype($arg);
                self::$LOGGER->warn("Building argument list. Unknown type: $type - value: $arg");
                $str[] = '"'.$arg.'"';
            }
        }

        return join(', ', $str);
    }

}

DF_Web_Utils_Arguments::$LOGGER = DF_Web_Logger::logger('DF_Web_Utils_Arguments');

