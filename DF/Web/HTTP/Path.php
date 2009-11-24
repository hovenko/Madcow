<?php

class DF_Web_HTTP_Path {

    public static function has_base_path($path, $base_path) {
        if (strpos($path, $base_path) === 0) {
            return true;
        }

        return false;
    }

    public static function strip_base_path($path, $base_path) {
        $path = preg_replace("#^$base_path#", '', $path);

        if (!preg_match("#^/#", $path)) {
            $path = "/$path";
        }

        return $path;
    }
    
    public static function strip_query_params($path) {
        $path = preg_replace("#\?.*#", '', $path);

        return $path;
    }

    public static function split_params($path) {
        $params = array();
        if (preg_match('#^([^\?]*)(?:\?(.*))?#', $path, $matches)) {
            $params[] = $matches[1];
            if (isset($matches[2])) {
                # query params, without leading "?"
                $params[] = $matches[2];
            }
            else {
                $params[] = null;
            }
        }

        return $params;
    }

    public static function split_parts($path) {
        $arguments = array();

        $parts = split('/', $path);
        if (!$parts) {
            return $path;
        }

        if (!$parts[0]) {
            array_shift($parts);
        }

        foreach ($parts as $part) {
            $arguments[]    = urldecode($part);
        }

        if (count($arguments) === 0) {
            if ($path) {
                $arguments[] = $path;
            }
            return $arguments;
        }

        $last_idx = count($arguments)-1;
        if (strlen($arguments[$last_idx]) === 0) {
            unset($arguments[$last_idx]);
        }

        return $arguments;
    }
}
