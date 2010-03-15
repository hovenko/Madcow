<?php

require_once 'setup_tests_environment.php';

require_once 'DF/Web/Utils/Config.php';


class Test_DF_Web_Utils_Config extends UnitTestCase {

    public function Test_DF_Web_Utils_Config($name = 'Testing DF_Web utils config') {
        $this->UnitTestCase($name);
    }

    function test_config_no_merge() {
        $config = array(
            'foo'   => 1,
        );

        $this->assertEqual(
            $config,
            DF_Web_Utils_Config::merge_hashes($config)
        );
    }

    function test_config_replace() {
        $config1 = array(
            'foo'   => 1,
        );

        $config2 = array(
            'foo'   => 2,
        );

        $expect = array(
            'foo'   => 2,
        );

        $this->assertEqual(
            $expect,
            DF_Web_Utils_Config::merge_hashes($config1, $config2)
        );
    }

    function test_config_add_key() {
        $config1 = array(
            'foo'   => 1,
        );

        $config2 = array(
            'bar'   => 2,
        );

        $expect = array(
            'foo'   => 1,
            'bar'   => 2,
        );

        $this->assertEqual(
            $expect,
            DF_Web_Utils_Config::merge_hashes($config1, $config2)
        );
    }


    function test_config_deep() {
        $config1 = array(
            'foo'   => array(
                'foo_a' => 1
            ),
        );

        $config2 = array(
            'foo'   => array(
                'foo_b' => 2,
            ),
        );

        $expect = array(
            'foo'   => array(
                'foo_a' => 1,
                'foo_b' => 2,
            ),
        );

        $this->assertEqual(
            $expect,
            DF_Web_Utils_Config::merge_hashes($config1, $config2)
        );
    }

}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Web_Utils_Config();
    $test->run(new TextReporter());
}

