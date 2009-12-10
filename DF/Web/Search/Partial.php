<?php


require_once 'DF/Web/Search.php';
require_once 'DF/Web/Search/I.php';
require_once 'DF/Web/Search/Partial/I.php';


class DF_Web_Search_Partial implements DF_Web_Search_Partial_I {

    protected $partial  = NULL;

    protected $key      = NULL;
    protected $value   = NULL;

    

    /**
     * Constructor.
     *
     * @return DF_Web_Search_Partial
     */
    public function __construct($key, $value, $partial) {
        $this->key      = $key;
        $this->value    = new DF_Web_Search($value);
        $this->partial  = new DF_Web_Search($partial);
    }


    public function getKey() {
        return $this->key;
    }


    public function getValue() {
        return $this->value->getSearch();
    }


    public function getPartials() {
        $partials = array();
        
        $value  = $this->getValue();
        if (is_array($value)) {
            $keys = array_keys($value);
            foreach ($keys as $key) {
                $part = $this->getPartial($key);
                $partials[] = $part;
            }
        }

        return $partials;
    }


    public function getSemiPartials() {
        $partials = array();

        $key    = $this->getKey();
        $value  = $this->getValue();
        $search = $this->getSearch();

        if (is_array($value)) {
            foreach ($value as $key2 => $value2) {
                $newvalue   = $value;
                unset($newvalue[$key2]);
                $newpartial = $search;
                $newpartial[$key] = $newvalue;
                $part = new DF_Web_Search_Partial($key, $key2, $newpartial);
                $partials[] = $part;
            }
        }

        return $partials;
    }


    public function getPartial($field) {
        # Injecting the key and value again, but excluding the requested field
        $value  = $this->getValue();
        $key    = $this->getKey();
        $search = $this->getSearch();

        if (!is_array($value)) {
            return new DF_Web_Search_EmptyPartial($field, NULL, $value);
        }

        if (!isset($value[$field])) {
            return new DF_Web_Search_EmptyPartial($field, NULL, $value);
        }
        
        $newvalue   = $value[$field];
        unset($value[$field]);

        $newpartial = $search;
        $newpartial[$key] = $value;
        
        $part = new DF_Web_Search_Partial($field, $newvalue, $newpartial);

        return $part;
    }


    public function getSearch() {
        return $this->partial->getSearch();
    }


    public function getLegacyStruct() {
        $part = array(
            'type'      => $this->getKey(),
            'value'     => $this->getValue(),
            'search'    => $this->getSearch(),
        );

        return $part;
    }


}
