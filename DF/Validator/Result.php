<?php


class DF_Validator_Result {
    protected $rule     = NULL;
    protected $field    = NULL;
    protected $error    = NULL;
    
    public function __construct($rule, $field) {
        $this->rule     = $rule;
        $this->field    = $field;
    }


    public function setError($error) {
        $this->error = $error;
    }

    public function isError() {
        return $this->error ? TRUE : FALSE;
    }

    public function asStruct() {
        $struct = $this->rule;

        if ($this->isError()) {
            $struct['error'] = $this->error;
        }

        return $struct;
    }
}
