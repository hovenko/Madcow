<?php

$DIR_SCRIPT = dirname(__FILE__);
$DIR_ROOT   = "$DIR_SCRIPT/..";
$DIR_LIB    = "{$DIR_ROOT}/lib";

$MADCOW_LIB = "$DIR_ROOT/..";

$DIR_LIB    = "$DIR_LIB:$MADCOW_LIB";

set_include_path($DIR_LIB.':'.get_include_path());

#define('LOG4PHP_CONFIGURATION', dirname(__FILE__).'/../logger.conf');
require_once 'DF/Web/Logger.php';
DF_Web_Logger::setActiveLogger('error_log');

require_once 'Sample.php';

$environment = DF_Web_Environment::singleton();
$environment->app_root = $DIR_ROOT;
$environment->trusted_proxies = 0;
$environment->base_path = dirname($_SERVER['SCRIPT_NAME']);

if (getenv('ENVIRONMENT')) {
    $environment->environment = getenv('ENVIRONMENT');
}

$environment->debug = 1;

# for production
#$environment->environment = 'production';
#$environment->debug = 0;


$context = new Sample();

$context->execute();

$context->finalize();

$context = NULL;

DF_Web_Logger::shutdown();
