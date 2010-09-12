<?php

class DF_Validator_Type_LdapDN {

    public function validate($value) {
        if ($this->isLdapExtLoaded()) {
            return $this->validateUsingLdapExt($value);
        }

        return $this->validateUsingPcre($value);
    }


    protected function validateUsingLdapExt($value) {
        if (!ldap_explode_dn($value, 0)) {
            return false;
        }

        return true;
    }


    /**
     * Should not be trusted, doesnt validate that each part is a valid RDN
     */
    protected function validateUsingPcre($value) {
        $nodes = preg_split('#\s*,\s*#', $value);
        if (count($nodes) > 1) {
            return true;
        }

        return false;
    }


    public function isLdapExtLoaded() {
        if (function_exists('ldap_explode_dn')) {
            return true;
        }

        return false;
    }


}
