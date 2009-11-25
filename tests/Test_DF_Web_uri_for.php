<?php

require_once 'setup_tests_environment.php';

require_once 'DF/Web.php';
require_once 'DF/Web/Environment.php';


class Test_DF_Web_uri_for extends UnitTestCase {
    private $c = NULL;

    public function Test_DF_Web_uri_for($name = 'Testing Test_DF_Web_uri_for') {
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

    function testBasePathRoot() {
        $env = DF_Web_Environment::singleton();
        $env->base_path = '/utenlandsbolig';

        $c = $this->c;
        $c->setup_base_path('/utenlandsbolig');
        
        $this->assertEqual(
            'http://localhost/utenlandsbolig/',
            $c->uri_for('/')
        );
    }

    function testRootUri() {
        $c = $this->c;
        $this->assertEqual(
            'http://localhost/',
            $c->uri_for('/')
        );
    }

    function testSimpleUri() {
        $c = $this->c;
        $this->assertEqual(
            'http://localhost/land/ES',
            $c->uri_for('/land/ES')
        );
    }

    function testPartsUri() {
        $c = $this->c;
        $this->assertEqual(
            'http://localhost/land/ES',
            $c->uri_for('/land', 'ES')
        );
        $this->assertEqual(
            'http://localhost/land/ES/boliger',
            $c->uri_for('/land/', 'ES', 'boliger')
        );
    }

    function testSimpleQueryUri() {
        $c = $this->c;
        $this->assertEqual(
            'http://localhost/land/ES?k1=v1',
            $c->uri_for('/land/ES', array('k1' => 'v1'))
        );

        // The array is order sensitive
        $this->assertEqual(
            'http://localhost/land/ES?k1=v1&k2=v2',
            $c->uri_for('/land/ES', array('k1' => 'v1', 'k2' => 'v2'))
        );
    }

    function testPartsQueryUri() {
        $c = $this->c;
        $this->assertEqual(
            'http://localhost/land/ES?k1=v1',
            $c->uri_for('/land', 'ES', array('k1' => 'v1'))
        );

        // The array is order sensitive
        $this->assertEqual(
            'http://localhost/land/ES/boliger?k1=v1&k2=v2',
            $c->uri_for('/land', 'ES', 'boliger', array('k1' => 'v1', 'k2' => 'v2'))
        );
    }

    function testEscapePartsUri() {
        $c = $this->c;
        $this->assertEqual(
            'http://localhost/land/ES/asd123%26',
            $c->uri_for('/', 'land', 'ES', 'asd123&')
        );
    }

    function testEscapeQueryUri() {
        $c = $this->c;
        $this->assertEqual(
            'http://localhost/land/ES?k1=v1%26',
            $c->uri_for('/land/ES', array('k1' => 'v1&'))
        );
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Web_uri_for();
    $test->run(new TextReporter());
}

