<?php

class DF_Web_HTTP_Request_QueryParser {
    static public function build_query_params($search, $prefix = 's') {
        if (!$search) {
            return array();
        }

        $list = array();
        self::flatten_query_structure(&$list, $prefix, NULL, $search);

        return $list;
    }


    static public function build_query_params_string($search, $prefix = 's') {
        $query = self::build_query_params($search, $prefix);

        $query_arr = array();
        foreach ($query as $key => $value) {
            $query_arr[] = sprintf('%s=%s', urlencode($key), urlencode($value));
        }
        return join('&', $query_arr);
    }


    private static function flatten_query_structure(&$list, $name, $part, $value) {
        if (!is_string($name)) {
            throw new DF_Error_InvalidArgumentException('name', $name, 'string');
        }

        if (NULL === $part) {
            $part = "";
        }

        if (!strlen($name)) {
            $name = $part;
        }
        elseif (strlen($part)) {
            $name .= ".$part";
        }

        if (is_array($value)) {
            foreach ($value as $key => $sub) {
                self::flatten_query_structure($list, $name, $key, $sub);
            }
        }
        else {
            $list[$name] = $value;
        }
    }


    static public function parse_query_params($params) {
        if (!is_array($params)) {
            throw new DF_Error_InvalidArgumentException('params', $params, 'array');
        }
        
        $p = array();
        foreach ($params as $key => $val) {
            $key_parts = NULL;

            // PHP replaces . with _ in parameter names, for some reason
            if (strpos($key, '_') !== false) {
                $key_parts = split('_', $key);
            }
            else {
                $key_parts = array($key);
            }

            $last_arr =& $p;
            foreach ($key_parts as $part) {
                if (!isset($last_arr[$part])) {
                    $last_arr[$part] = NULL;
                }

                $last_arr =& $last_arr[$part];
            }

            $last_arr = $val;
        }

        return $p;
    }
}
