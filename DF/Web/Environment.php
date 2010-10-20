<?php
/**
 * @package DF_Web
 */


/**
 * Holds all the environment variables for the current request.
 * 
 * Defaults to run in production mode.
 */
class DF_Web_Environment {
    private static $_singleton = NULL;

    /**
     * @return DF_Web_Environment
     */
    public static function singleton() {
        if (self::$_singleton == NULL) {
            self::$_singleton = new DF_Web_Environment();
        }

        return self::$_singleton;
    }


    /**
     * Private constructor.
     *
     * @see singleton
     * @return DF_Web_Environment
     */
    private function __construct() {
        // singleton
        $this->environment = self::$ENV_PRODUCTION;
    }


    public static $ENV_PRODUCTION   = 'production';
    public static $ENV_DEVELOPMENT  = 'development';
    public static $ENV_TEST         = 'test';
    public static $ENV_STAGE        = 'stage';

    /**
     * This environment is used for running unit and functional tests.
     * In this environment we should not send HTTP headers.
     * @var string
     */
    public static $ENV_TESTS        = 'tests';

    public $app_root        = NULL;
    public $environment     = NULL;
    public $trusted_proxies = 0;

    /**
     * Autodetects the base path in index.php.
     * If this fails, you should set it to a value in index.php (or other).
     *
     * Sample values:
     *  - '' for http://hostname/
     *  - 'some/other/dir' for http://hostname/some/other/dir/
     */
    public $base_path       = NULL;


    /**
     * By enabling this a stack trace with debug output will be
     * printed in case of an error.
     */
    public $debug           = false;
}
