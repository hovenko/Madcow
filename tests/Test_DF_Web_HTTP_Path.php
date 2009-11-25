<?php

require_once 'setup_tests_environment.php';

require_once 'DF/Web/HTTP/Path.php';


class Test_DF_Web_HTTP_Path extends UnitTestCase {

    public function Test_DF_Web_HTTP_Path($name = 'Testing DF_Web_HTTP_Path') {
        $this->UnitTestCase($name);
    }

    function testEmpty() {
        $path   = "/";
        $parts  = DF_Web_HTTP_Path::split_parts($path);

        $this->assertEqual(0, sizeof($parts));
    }

    function testSimple() {
        $path   = "/utenlandsbolig/";
        $parts  = DF_Web_HTTP_Path::split_parts($path);

        $this->assertEqual(1, sizeof($parts));
    }

    function testMulti() {
        $path   = "/utenlandsbolig/spania/agentA/bolig12";
        $parts  = DF_Web_HTTP_Path::split_parts($path);

        $this->assertEqual(4, sizeof($parts));
    }

    function testNoParams() {
        $path   = "/utenlandsbolig/spania/agentA/bolig12/";
        $parts  = DF_Web_HTTP_Path::split_params($path);
        
        $this->assertEqual(2, sizeof($parts));
        $this->assertEqual('/utenlandsbolig/spania/agentA/bolig12/', $parts[0]);
        $this->assertEqual('', $parts[1]);
    }

    function testParams() {
        $path = "/utenlandsbolig/spania/agentA/bolig12/?arg=tull&id=123";
        $parts = DF_Web_HTTP_Path::split_params($path);

        $this->assertEqual(2, sizeof($parts));
        $this->assertEqual('/utenlandsbolig/spania/agentA/bolig12/', $parts[0]);
        $this->assertEqual('arg=tull&id=123', $parts[1]);
    }

    function testParamsAndPath() {
        $path = "/utenlandsbolig/spania/agentA/bolig12/?arg=tull&id=123";
        $parts = DF_Web_HTTP_Path::split_params($path);
        $new_path = $parts[0];
        
        $arguments = DF_Web_HTTP_Path::split_parts($new_path);

        $this->assertEqual(4, sizeof($arguments));
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Web_HTTP_Path();
    $test->run(new TextReporter());
}

