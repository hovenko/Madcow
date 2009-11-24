<?php

class DF_Web_Utils_Strings {


    static public function sanitizeUrlString($string) {
        $tmp = $string;

        // All lowercase
        $tmp = mb_strtolower($tmp, 'UTF-8');

        // Replace spaces, "%", "/", "?", "&", "$", "#" with "-".
        $tmp = preg_replace('|[\s%/\?&$#]+|', '-', $tmp);

        // max one - at the time
        $tmp = preg_replace('#-+#', '-', $tmp);

        // trim - at the beginning and end
        $tmp = preg_replace('#^-*(.*?)-$#', '$1', $tmp);

        return $tmp;
    }


}
