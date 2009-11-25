<?php

require_once 'setup_tests_environment.php';

require_once 'DF/Web/Component/Loader.php';
require_once 'DF/Web/View.php';
require_once 'DF/Web/View/Smarty.php';


class Test_DF_Web_View_Smarty extends UnitTestCase {
    private $smarty = NULL;

    public function Test_DF_Web_View_Smarty($name = 'Testing DF_Web_View_Smarty') {
        $this->UnitTestCase($name);
    }

    public function setUp() {
        $this->smarty = DF_Web_Component_Loader::component('DF_Web_View_Smarty', 'DF_Web');
    }

    public function tearDown() {
        $this->smarty = NULL;
    }

    function testConfig() {
        $config = $this->smarty->config();
        $this->assertEqual($config['debugging'], true);
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Web_View_Smarty();
    $test->run(new TextReporter());
}

