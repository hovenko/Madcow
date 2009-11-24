<?php

require_once 'DF/Logger.php';
require_once 'DF/Validator/Result.php';

class DF_Validator {
    public static $LOGGER = NULL;
    
    protected $rules = NULL;
    
    
    public function __construct($rules) {
        $this->rules = $rules;
    }


    public function validate($value_map) {
        $errors = array();

        $rules = $this->rules;

        $flattened_rules    = self::flatten_rules($rules);
        $flattened_map      = self::flatten_value_map($value_map);

        $fields = array();
        foreach ($flattened_rules as $field => $rule) {
            $fields[$field] = NULL;
        }

        foreach ($flattened_map as $field => $value) {
            $fields[$field] = $value;
        }

        foreach ($fields as $field => $value) {
            $rule   = $flattened_rules[$field];
            $res = self::validate_field_by_rule($rule, $field, $value);
            if ($res->isError()) {
                $errors[$field] = $res->asStruct();
            }
        }

        return $errors;
    }


    static protected function flatten_rules($rules) {
        $map = array();
        foreach ($rules as $field => $value) {
            $submap = self::flatten_rules_pair($field, $value);
            foreach ($submap as $tmp1 => $tmp2) {
                $map[$tmp1] = $tmp2;
            }
        }

        return $map;
    }


    static protected function flatten_rules_pair($field, $value) {
        $map = array();

        if (is_array($value)) {
            if (isset($value['type'])) {
                $type = $value['type'];
                if (is_string($type)) {
                    $map[$field] = $value;
                    return $map;
                }
            }
            
            foreach ($value as $subfield => $subvalue) {
                $tmpfield = "$field.$subfield";
                $submap = self::flatten_rules_pair($tmpfield, $subvalue);

                foreach ($submap as $tmp1 => $tmp2) {
                    $map[$tmp1] = $tmp2;
                }
            }
        }
        else {
            $map[$field] = $value;
        }

        return $map;
    }


    static protected function flatten_value_map($value_map) {
        $map = array();
        foreach ($value_map as $field => $value) {
            $submap = self::flatten_value_map_pair($field, $value);
            foreach ($submap as $tmp1 => $tmp2) {
                $map[$tmp1] = $tmp2;
            }
        }

        return $map;
    }


    static protected function flatten_value_map_pair($field, $value) {
        $map = array();

        if (is_array($value)) {
            foreach ($value as $subfield => $subvalue) {
                $tmpfield = "$field.$subfield";
                $submap = self::flatten_value_map_pair($tmpfield, $subvalue);

                foreach ($submap as $tmp1 => $tmp2) {
                    $map[$tmp1] = $tmp2;
                }
            }
        }
        else {
            $map[$field] = $value;
        }

        return $map;
    }


    public function validate_by_values($value_map) {
        $errors = array();

        foreach ($value_map as $key => $value) {
            $res = $this->validate_field($key, $value);
            if ($res->isError()) {
                $errors[$key] = $res->asStruct();
            }
        }

        return $errors;
    }


    public function validate_field($field, $value) {
        $rule = $this->rules[$field];

        return self::validate_field_by_rule($rule, $field, $value);
    }


    static protected function validate_field_by_rule($rule, $field, $value) {
        $res = new DF_Validator_Result($rule, $field);

        if (!$rule) {
            self::$LOGGER->debug("No rule is configured for field: $field");
            return $res;
        }

        $isEmpty = false;

        if (NULL == $value || "" === $value) {
            $isEmpty = true;
        }

        if ($isEmpty && !self::isOptional($rule)) {
            $res->setError('missing');
            return $res;
        }
        elseif ($isEmpty) {
            return $res;
        }
        
        $type = self::ruleType($rule);
        switch ($type) {
            case 'float':
                $tmp = $value + 0.0;
                if ("$tmp" !== "$value") {
                    $res->setError('type');
                }
                break;

            case 'integer':
                $tmp = intval($value);
                if ("$tmp" !== "$value") {
                    $res->setError('type');
                }
                break;

            case 'boolean':
                // always either true or false
                break;

            case 'ldap_dn':
                if (!ldap_explode_dn($value, 0)) {
                    $res->setError('type');
                }
                break;
                
            case 'uuid':
                if (!preg_match('#^.{36}$#', $value)) {
                    $res->setError('type');
                }
                break;

            case 'string':
                if (!is_string($value)) {
                    $res->setError('type');
                }
                break;

            case 'email':
                if (!self::isEmail($value)) {
                    $res->setError('type');
                }
                break;

            default:
                self::$LOGGER->warn("Unknown ruletype: $type");
        }


        if (!$res->isError()) {
            if ($minlength = self::ruleMinlength($rule)) {
                if (strlen($value) < $minlength) {
                    $res->setError('minlength');
                }
            }
        }

        return $res;
    }


    static public function isEmail($email) {
        $tmp = mb_strtolower($email, 'UTF-8');
        if (preg_match('#^\w[\w\d\+\-_\.]*@\w+[\.\w\d]*\w$#', $tmp)) {
            return true;
        }

        return false;
    }


    static protected function isOptional($rule) {
        return $rule['optional'] ? TRUE : FALSE;
    }


    static protected function ruleType($rule) {
        $type = $rule['type'];
        
        if (NULL == $type) {
            return 'string';
        }

        return $type;
    }


    static protected function ruleMinlength($rule) {
        if (isset($rule['minlength'])) {
            return (int) $rule['minlength'];
        }

        # No validation
        return 0;
    }


    static public function isError($res) {
        return isset($res['error']) && $res['error'] ? TRUE : FALSE;
    }
}

DF_Validator::$LOGGER = DF_Logger::logger('DF_Validator');
