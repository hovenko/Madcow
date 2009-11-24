<?php

class DF_Web_Utils_Components {

    
    static public function name_to_path($name) {
        $p = "";

        // FIXME all strtolower and replace _ with /
        // I must have been tired when writing this
        
        $rest = "$name";
        for ($i = 0; $i < strlen($rest); $i++) {
            $chr = $rest[$i];
            if ($i == 0) {
                $p .= strtolower($chr);
            }
            elseif ($chr == '_') {
                $p .= "/";
            }
            elseif (preg_match('#^[A-Z]$#', $chr)) {
                $p .= strtolower($chr);
            }
            else {
                $p .= $chr;
            }
        }

        return $p;
    }

}
