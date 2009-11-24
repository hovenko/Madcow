<?php

require_once 'Cache/Lite.php';

require_once 'DF/Web/Cache.php';

class DF_Web_Cache_Lite extends DF_Web_Cache {
    private $cache = NULL;
    private $config = NULL;

    public function __construct($config) {
        $this->cache = new Cache_Lite($config);
        $this->config = $config;
    }

    public function get($key) {
        return $this->cache->get($key);
    }

    public function set($key, $value, $lifetime = NULL) {
        if (!isset($lifetime)) {
            $lifetime = $this->config['lifeTime'];
        }

        $this->cache->setLifeTime($lifetime);
        return $this->cache->save($value, $key);
    }

    public function remove($key) {
        $this->cache->remove($key);
    }


    public function flush() {
        $this->cache->clean();
    }
}
