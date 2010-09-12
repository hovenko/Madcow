<?php

$dir = dirname(__FILE__);
set_include_path("$dir/..:" . get_include_path());

require_once 'DF/Web/Logger.php';
DF_Web_Logger::setActiveLogger('error_log');

require_once 'simpletest/unit_tester.php';
require_once 'simpletest/reporter.php';

require_once 'DF/Web.php';

$environment = DF_Web_Environment::singleton();
$environment->app_root = "$dir/..";

DF_Web_Config::$basename = "tests";
