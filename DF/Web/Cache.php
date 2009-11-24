<?php

require_once 'DF/Web/Exception.php';

class DF_Web_Cache {
    private $resource = NULL;

    public function __construct($config) {
        throw new DF_Web_Exception("Must implement a cache class");
    }

    
    /**
     * Lookup and return an item from the cache by its key.
     */
    public function get($key) {}


    /**
     * Store something in the cache. Lifetime in seconds.
     */
    public function set($key, $value, $lifetime) {}


    /**
     * Remove an item from the cache by its key.
     */
    public function remove($key) {}


    /**
     * Clean out the entire cache.
     */
    public function flush() {}
}
