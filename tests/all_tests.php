<?php

require_once 'setup_tests_environment.php';

define('TEST_RUNNING', true);


class All_ULB_Tests extends GroupTest {


    /**
     * Constructor.
     *
     * Sets up the test suite.
     *
     * @param string $name
     * @return All_ULB_Tests
     */
    public function All_ULB_Tests($name = "Running all ULB tests") {
        $this->GroupTest($name);
        $dir = dirname(__FILE__)."/";
        $this->AddTestFile($dir.'Test_DF_URI.php');
        $this->AddTestFile($dir.'Test_DF_URL.php');
        $this->AddTestFile($dir.'Test_DF_Validator.php');
        $this->AddTestFile($dir.'Test_DF_Web_resolve_uri.php');
        $this->AddTestFile($dir.'Test_DF_Web_uri_for.php');
        $this->AddTestFile($dir.'Test_DF_Web_HTTP_Path.php');
        $this->AddTestFile($dir.'Test_DF_Web_HTTP_Request_QueryParser.php');
        $this->AddTestFile($dir.'Test_DF_Web_Model_Testmodel.php');
        $this->AddTestFile($dir.'Test_DF_Web_Routing.php');
        $this->AddTestFile($dir.'Test_DF_Web_Utils_Config.php');
        $this->AddTestFile($dir.'Test_DF_Web_View_Smarty.php');
    }

}


$test = new All_ULB_Tests();
$test->run(new TextReporter());



