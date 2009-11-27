<?php

require_once 'setup_tests_environment.php';

require_once 'DF/URL.php';
require_once 'DF/URL/MalformedURLException.php';


/**
 * Since DF_URL extends from DF_URI, we will not duplicate all
 * the tests with failing input data from the URI test case here.
 *
 */
class Test_DF_URL extends UnitTestCase {

    public function Test_DF_URL($name = 'Testing DF_URL') {
        $this->UnitTestCase($name);
    }

    function test_fail_no_path() {
        $input  = "http://localhost";

        try {
            $uri = DF_URL::fromString($input);
            $this->fail("URL without a path is not a valid URL");
        }
        catch (DF_URL_MalformedURLException $e) {
            $this->pass();
        }
    }



    function test_simple() {
        $input  = "http://localhost/";

        $uri = DF_URL::fromString($input);

        $this->assertEqual(
            "$uri",
            $input
        );

        $this->assertEqual(
            $uri->get_scheme()."",
            "http"
        );

        $this->assertEqual(
            $uri->get_hierarchical()."",
            "//localhost/"
        );

        $this->assertEqual(
            $uri->get_authority()."",
            "localhost"
        );

        $this->assertEqual(
            $uri->get_host()."",
            "localhost"
        );

        $this->assertEqual(
            $uri->get_path()."",
            "/"
        );
    }


    function test_longer_hierarchical() {
        $input  = "http://localhost/some/file.html";

        $uri = DF_URL::fromString($input);

        $this->assertEqual(
            "$uri",
            $input
        );

        $this->assertEqual(
            $uri->get_path()."",
            "/some/file.html"
        );
    }


    function test_port() {
        $input  = "http://localhost:8080/";

        $uri = DF_URL::fromString($input);

        $this->assertEqual(
            "$uri",
            $input
        );

        $this->assertEqual(
            $uri->get_port()."",
            "8080"
        );
    }


    function test_user() {
        $input  = "http://user:pass@localhost/";

        $uri = DF_URL::fromString($input);

        $this->assertEqual(
            "$uri",
            $input
        );

        $this->assertEqual(
            $uri->get_user()."",
            "user:pass"
        );
    }


}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_URL();
    $test->run(new TextReporter());
}

