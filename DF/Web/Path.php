<?php


require_once 'DF/Web/HTTP/Path.php';
require_once 'DF/Web/Path/Part.php';


class DF_Web_Path {

    protected $string = NULL;

    public function __construct($string) {
        if (NULL === $string) {
            throw new InvalidArgumentException("Not set: string");
        }

        if (!is_string($string)) {
            $type = gettype($string);
            if ($type == 'object') {
                $class = get_class($string);
                $type = "object($class)";
            }
            throw new InvalidArgumentException("Not a string: string Was: $type");
        }

        $this->string = $string;
    }


    public function is_absolute() {
        if (strpos($this->string, '/') === 0) {
            return true;
        }

        return false;
    }


    protected function has_trailing_slash() {
        $string = $this->string;

        $last_idx = strlen($string) - 1;
        if ($string[$last_idx] == "/") {
            return true;
        }

        return false;
    }


    public function replace_last_part($sub) {
        if (!$sub) {
            throw new InvalidArgumentException("Not set: sub");
        }

        if (!$sub instanceof DF_Web_Path_Part) {
            throw new InvalidArgumentException("Not of type DF_Web_Path: sub");
        }

        $parts = $this->get_path_parts();
        array_pop($parts);
        $parts[] = $sub;

        $new_path = "";
        
        foreach ($parts as $part) {
            if ($new_path) {
                $new_path .= "/";
            }

            $new_path .= "$part";
        }

        if ($this->is_absolute()) {
            $new_path = "/$new_path";
        }

        return new DF_Web_Path($new_path);
    }


    public function append_path($sub) {
        if (!$sub) {
            throw new InvalidArgumentException("Not set: sub");
        }

        if (!$sub instanceof DF_Web_Path) {
            throw new InvalidArgumentException("Not of type DF_Web_Path: sub");
        }

        $sub_str = "$sub";

        if ($sub->is_absolute()) {
            $sub_str = substr($sub_str, 1);
        }

        $base_str = "$this";
        if (!$this->has_trailing_slash()) {
            $base_str .= "/";
        }

        $new_path = "$base_str$sub_str";

        return new DF_Web_Path($new_path);
    }


    protected function toString() {
        return $this->string;
    }


    public function __toString() {
        return "".$this->toString();
    }

    
    public function has_base_path($base_path) {
        return DF_Web_HTTP_Path::has_base_path($this->string, $base_path);
    }


    public function get_path_stripped_base($base_path) {
        $stripped = DF_Web_HTTP_Path::strip_base_path($this->string, $base_path);
        return new DF_Web_Path($stripped);
    }


    public function get_path_stripped_query_params() {
        $stripped = DF_Web_HTTP_Path::strip_query_params($this->string);
        return new DF_Web_Path($stripped);
    }


    public function get_params() {
        $arr = DF_Web_HTTP_Path::split_params($this->string);
        $stripped   = $arr[0];
        $query      = $arr[1];

        return $query;
    }
    

    public function get_path_parts() {
        $arr = DF_Web_HTTP_Path::split_parts($this->string);
        $parts = array();

        foreach ($arr as $str) {
            $part = new DF_Web_Path_Part($str);
            $parts[] = $part;
        }

        return $parts;
    }
    
}
