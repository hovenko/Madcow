<?php

require_once 'setup_tests_environment.php';

require_once 'DF/Web.php';
require_once 'DF/Web/Environment.php';


class Test_DF_Web_resolve_uri extends UnitTestCase {
    private $c = NULL;

    public function Test_DF_Web_resolve_uri($name = 'Testing DF_Web::resolve_uri') {
        $this->UnitTestCase($name);
    }

    public function setUp() {
        $env = DF_Web_Environment::singleton();
        $env->app_root = dirname(__FILE__).'/../';
        $env->trusted_proxies = 0;
        $env->base_path = '/';
        $env->environment = DF_Web_Environment::$ENV_TESTS;
        $env->debug = 1;

        $this->c = new DF_Web();
        $this->c->request->hostname = 'localhost';
    }

    public function tearDown() {
        $this->c = NULL;
    }

    function test_http() {
        $uri = "http://example.com/foo";
        
        $this->assertEqual(
            $this->c->resolve_uri($uri),
            $uri
        );
    }


    function test_madcow_uri_for() {
        $env = DF_Web_Environment::singleton();
        $env->base_path = '/utenlandsbolig';
        $c = $this->c;
        $c->setup_base_path('/utenlandsbolig');

        $uri = "madcow:uri_for:/";
        
        $this->assertEqual(
            $c->resolve_uri($uri),
            "http://localhost/utenlandsbolig/"
        );
    }

}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Web_resolve_uri();
    $test->run(new TextReporter());
}

