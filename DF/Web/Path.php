<?php


require_once 'DF/URL/Path/I.php';


class DF_Web_Path implements DF_URL_Path_I {

    /**
     * @var DF_URL_Path_I
     */
    protected $path = NULL;



    public function __construct($path) {
        if (!$path instanceof DF_URL_Path_I) {
            throw new DF_Error_InvalidArgumentException("path", $path, "DF_URL_Path_I");
        }

        $this->path = $path;
    }


    /**
     * 
     * @param string $string
     * @return DF_Web_Path
     */
    static public function fromString($string) {
        $path = new DF_URL_Path($string);
        return new DF_Web_Path($path);
    }


    public function is_absolute() {
        return $this->path->is_absolute();
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

        return DF_Web_Path::fromString($new_path);
    }


    /**
     * 
     * @param DF_Web_Path $sub
     * @return DF_Web_Path
     */
    public function append_path($sub) {
        if (!$sub instanceof DF_Web_Path) {
            throw new DF_Error_InvalidArgumentException('sub', $sub, DF_Web_Path);
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

        return DF_Web_Path::fromString($new_path);
    }


    public function has_trailing_slash() {
        return $this->path->has_trailing_slash();
    }


    public function __toString() {
        return "".$this->path;
    }

    
    public function has_base_path($base_path) {
        return DF_Web_HTTP_Path::has_base_path($this->path."", $base_path);
    }


    public function get_path_stripped_base($base_path) {
        $stripped = DF_Web_HTTP_Path::strip_base_path($this->path."", $base_path);
        return new DF_Web_Path($stripped);
    }


    public function get_path_stripped_query_params() {
        $stripped = DF_Web_HTTP_Path::strip_query_params($this->path."");
        return new DF_Web_Path($stripped);
    }


    public function get_params() {
        $arr = DF_Web_HTTP_Path::split_params($this->path."");
        $stripped   = $arr[0];
        $query      = $arr[1];

        return $query;
    }
    

    /**
     * @return array
     */
    public function get_path_parts() {
        $arr = DF_Web_HTTP_Path::split_parts($this->path."");
        $parts = array();

        foreach ($arr as $str) {
            $part = DF_Web_Path_Part::fromString($str);
            $parts[] = $part;
        }

        return $parts;
    }
    
}

require_once 'DF/Error/InvalidArgumentException.php';
require_once 'DF/URL/Path.php';
require_once 'DF/Web/HTTP/Path.php';
require_once 'DF/Web/Path/Part.php';
