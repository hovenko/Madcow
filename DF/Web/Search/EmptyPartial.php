<?php


require_once 'DF/Web/Search.php';
require_once 'DF/Web/Search/I.php';
require_once 'DF/Web/Search/Partial/I.php';


/**
 * Immutable
 *
 */
class DF_Web_Search_EmptyPartial implements DF_Web_Search_Partial_I {

    protected $partial  = NULL;

    protected $key      = NULL;



    public function __construct($key, $value, $partial) {
        $this->key      = $key;
        # value is ignored
        $this->partial  = new DF_Web_Search($partial);
    }


    public function getKey() {
        return $this->key;
    }


    public function getValue() {
        return NULL;
    }


    public function getPartials() {
        return array();
    }


    public function getSemiPartials() {
        return array();
    }


    public function getPartial($field) {
        return $this->partial->getPartial($field);
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
