<?php

require_once 'setup_tests_environment.php';


class Test_DF_Web_Model_Testmodel extends UnitTestCase {
    private $c = NULL;
    private $m = NULL;

    public function Test_DF_Web_Model_Testmodel($name = 'Testing DF_Web model Testmodel') {
        $this->UnitTestCase($name);
    }

    public function setUp() {
        $this->c = new DF_Web();
        $this->m = $this->c->model("Testmodel");
    }

    public function tearDown() {
        $this->c = NULL;
        $this->m = NULL;
    }

    function test_config_override() {
        $m = $this->m;
        $config = $m->config();
        $this->assertEqual(
            $config['aaa'],
            "new_value"
        );
    }

    function test_config_keep_untouched() {
        $m = $this->m;
        $config = $m->config();
        $this->assertEqual(
            $config['bbb'],
            "BBB"
        );

    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Web_Model_Testmodel();
    $test->run(new TextReporter());
}

