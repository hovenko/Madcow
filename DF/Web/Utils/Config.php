<?php


class DF_Web_Utils_Config {


    /**
     * Merges two arrays. Only mappings are merged.
     * Lists are not merged together.
     * 
     * @param array $left
     * @param array $right [optional]
     * @return array
     */
    static public function merge_hashes($left, $right = NULL) {
        if (NULL === $right) {
            return $left;
        }

        $new = $left;

        foreach ($right as $key => $val) {
            $left_ref = false;
            if (isset($left[$key])) {
                $left_ref = is_array($left[$key]);
            }
            
            if (is_array($val) && $left_ref) {
                $new[$key] = self::merge_hashes($left[$key], $val);
            }
            else {
                $new[$key] = $val;
            }
        }

        return $new;
    }
}
