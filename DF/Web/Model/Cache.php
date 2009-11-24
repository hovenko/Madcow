<?php

define('DEFAULT_CACHE_DIR', DF_Web::path_to('cache'));

class DF_Web_Model_Cache extends DF_Web_Model {
    private $_cache = NULL;

    protected $config = array(
        'cache_class'   => 'DF_Web_Cache_Lite',
        'cache_config'  => array(
            'lifeTime'      => 300,
            'cacheDir'      => DEFAULT_CACHE_DIR,
        ),
    );

    public function get_cache() {
        return $this->_cache;
    }

    public function initialize() {
        $class  = $this->config['cache_class'];
        $config = $this->config['cache_config'];

        $file = preg_replace('|_|', '/', $class);
        require_once "$file.php";
        
        $this->_cache = new $class($config);
    }
}
