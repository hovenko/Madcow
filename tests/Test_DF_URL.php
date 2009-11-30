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
            $url = DF_URL::fromString($input);
            $this->fail("URL without a path is not a valid URL");
        }
        catch (DF_URL_MalformedURLException $e) {
            $this->pass();
        }
    }



    function test_simple() {
        $input  = "http://localhost/";

        $url = DF_URL::fromString($input);

        $this->assertEqual(
            "$url",
            $input
        );

        $this->assertEqual(
            $url->get_scheme()."",
            "http"
        );

        $this->assertEqual(
            $url->get_hierarchical()."",
            "//localhost/"
        );

        $this->assertEqual(
            $url->get_authority()."",
            "localhost"
        );

        $this->assertEqual(
            $url->get_host()."",
            "localhost"
        );

        $this->assertEqual(
            $url->get_path()."",
            ""
        );
    }


    function test_longer_hierarchical() {
        $input  = "http://localhost/some/file.html";

        $url = DF_URL::fromString($input);

        $this->assertEqual(
            "$url",
            $input
        );

        $this->assertEqual(
            $url->get_path()."",
            "some/file.html"
        );

        $path = $url->get_path();
        $this->assertFalse(
            # All paths of URLs are relative (to the root)
            $path->is_absolute()
        );
    }


    function test_path_absolute() {
        $input = "/some/file.html";

        $path = new DF_URL_Path($input);
        $this->assertTrue(
            $path->is_absolute()
        );
    }


    function test_path_relative() {
        $input = "some/file.html";

        $path = new DF_URL_Path($input);
        $this->assertFalse(
            $path->is_absolute()
        );
    }


    function test_port() {
        $input  = "http://localhost:8080/";

        $url = DF_URL::fromString($input);

        $this->assertEqual(
            "$url",
            $input
        );

        $this->assertEqual(
            $url->get_port()."",
            "8080"
        );
    }


    function test_user() {
        $input  = "http://user:pass@localhost/";

        $url = DF_URL::fromString($input);

        $this->assertEqual(
            "$url",
            $input
        );

        $this->assertEqual(
            $url->get_user()."",
            "user:pass"
        );
    }


}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_URL();
    $test->run(new TextReporter());
}

