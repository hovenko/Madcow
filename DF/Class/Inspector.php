<?php

class DF_Class_Inspector {

    /**
     * Returns a list of all the public methods of a class.
     * 
     * @param string $class might also be an instance
     * @return array
     */
    static public function methods($class) {
        return get_class_methods($class);
    }
}
