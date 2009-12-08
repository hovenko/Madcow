<?php

require_once 'DF/Copyable.php';

require_once 'DF/Error/InvalidArgumentException.php';

require_once 'DF/Web/Search/EmptyPartial.php';
require_once 'DF/Web/Search/I.php';
require_once 'DF/Web/Search/Partial.php';


class DF_Web_Search implements DF_Copyable, DF_Web_Search_I {

    protected $search = NULL;

    

    /**
     * Constructor.
     *
     * Takes an array of parameters used for the search.
     * It can be the HTTP request query or other equally structured queries.
     * 
     * Basically it takes a key/value list.
     * 
     * @param array $search
     * @return DF_Web_Search
     */
    public function __construct($search) {
        $this->search = $search;
    }


    public function getPartials() {
        $partials = array();
        
        $value  = $this->getSearch();

        if (is_array($value)) {
            $keys = array_keys($value);
            foreach ($keys as $key) {
                $part = $this->getPartial($key);
                $partials[] = $part;
            }
        }

        return $partials;
    }


    public function getPartial($field) {
        $search = $this->getSearch();

        if (!is_array($search)) {
            return new DF_Web_Search_EmptyPartial($field, NULL, $search);
        }

        if (!isset($search[$field])) {
            return new DF_Web_Search_EmptyPartial($field, NULL, $search);
        }

        $value = $search[$field];
        unset($search[$field]);

        $part = new DF_Web_Search_Partial($field, $value, $search);

        return $part;
    }


    public function getSearch() {
        $copy = $this->search;
        return $copy;
    }


    /**
     * Returns a new instance of this class with copy of all the data.
     *
     * @return DF_Web_Search
     */
    public function copy() {
        $copy = new DF_Web_Search($this->getSearch());
        return $copy;
    }
}
