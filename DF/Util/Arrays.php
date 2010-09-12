<?php


class DF_Util_Arrays {

    static public function asArray($mix) {
        $ret = array();
        if (is_array($mix)) {
            return $mix;
        }

        if (NULL === $mix) {
            return $ret;
        }

        $ret[] = $mix;
        return $ret;
    }

}
