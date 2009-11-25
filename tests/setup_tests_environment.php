<?php

$dir = dirname(__FILE__);
set_include_path("$dir/..:" . get_include_path());

#define('LOG4PHP_CONFIGURATION', dirname(__FILE__).'/../logger.conf');
require_once 'DF/Web/Logger.php';
DF_Web_Logger::setActiveLogger('log4php');

require_once 'simpletest/unit_tester.php';
require_once 'simpletest/reporter.php';

require_once 'DF/Web/Config.php';
require_once 'DF/Web/Environment.php';

$environment = DF_Web_Environment::singleton();
$environment->app_root = "$dir/..";

DF_Web_Config::$basename = "tests";
