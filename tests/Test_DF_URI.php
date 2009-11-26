<?php

require_once 'setup_tests_environment.php';

require_once 'DF/URI.php';
require_once 'DF/URI/Scheme.php';


class Test_DF_URI extends UnitTestCase {

    public function Test_DF_URI($name = 'Testing DF_URI') {
        $this->UnitTestCase($name);
    }

    function test_fail_empty() {
        $input  = "";

        try {
            $uri = new DF_URI($input);
            $this->fail("An empty string is not a valid URI");
        }
        catch (DF_URI_MalformedURIException $e) {
            $this->pass();
        }
    }


    function test_fail_no_scheme() {
        $input  = "://asd";

        try {
            $uri = new DF_URI($input);
            $this->fail("Missing scheme");
        }
        catch (DF_URI_MalformedURIException $e) {
            $this->pass();
        }
    }


    function test_fail_no_scheme_2() {
        $input  = "asd";

        try {
            $uri = new DF_URI($input);
            $this->fail("Missing scheme");
        }
        catch (DF_URI_MalformedURIException $e) {
            $this->pass();
        }
    }


    function test_fail_only_scheme() {
        $input  = "asd:";

        try {
            $uri = new DF_URI($input);
            $this->fail("Only scheme");
        }
        catch (DF_URI_MalformedURIException $e) {
            $this->pass();
        }
    }


    function test_fail_bad_scheme() {
        $input  = "asd_123://asd";

        try {
            $uri = new DF_URI($input);
            $this->fail("A scheme cannot contain underscore (_)");
        }
        catch (DF_URI_MalformedURIException $e) {
            $this->pass();
        }
    }


    function test_fail_bad_scheme_2() {
        $input = "asd/";
        try {
            $scheme = new DF_URI_Scheme($input);
            $this->fail("A scheme cannot contain slash (/)");
        }
        catch (DF_URI_MalformedSchemeException $e) {
            $this->pass();
        }
    }


    function test_fail_bad_scheme_3() {
        $input = "asd:";
        try {
            $scheme = new DF_URI_Scheme($input);
            $this->fail("A scheme cannot contain colon (:)");
        }
        catch (DF_URI_MalformedSchemeException $e) {
            $this->pass();
        }
    }


    function test_simple() {
        $input  = "http://localhost/";

        $uri = new DF_URI($input);

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
    }


    function test_longer_hierarchical() {
        $input  = "http://localhost/some/file.html";

        $uri = new DF_URI($input);

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
            "//localhost/some/file.html"
        );
    }


    function test_query() {
        $input  = "http://localhost/some/file.html?id=1&asd=123";

        $uri = new DF_URI($input);

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
            "//localhost/some/file.html"
        );

        $this->assertEqual(
            $uri->get_query()."",
            "id=1&asd=123"
        );
    }


    function test_query_fragment() {
        $input  = "http://localhost/some/file.html?id=1&asd=123#section1";

        $uri = new DF_URI($input);

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
            "//localhost/some/file.html"
        );

        $this->assertEqual(
            $uri->get_query()."",
            "id=1&asd=123"
        );

        $this->assertEqual(
            $uri->get_fragment()."",
            "section1"
        );
    }


    function test_empty_fragment() {
        $input  = "http://localhost/#";

        $uri = new DF_URI($input);

        $this->assertEqual(
            "$uri",
            $input
        );

        $this->assertEqual(
            $uri->get_fragment()."",
            ""
        );
    }


    function test_query_mixed_fragment() {
        $input  = "http://localhost/?a=1#asd#123+/&()asd";

        $uri = new DF_URI($input);

        $this->assertEqual(
            "$uri",
            $input
        );

        $this->assertEqual(
            $uri->get_query(),
            "a=1"
        );

        $this->assertEqual(
            $uri->get_fragment()."",
            "asd#123+/&()asd"
        );
    }


    function test_no_query_mixed_fragment() {
        $input  = "http://localhost/#asd#123+/&()asd";

        $uri = new DF_URI($input);

        $this->assertEqual(
            "$uri",
            $input
        );
        
        $this->assertEqual(
            $uri->get_hierarchical()."",
            "//localhost/"
        );

        $this->assertEqual(
            $uri->get_query(),
            ""
        );

        $this->assertEqual(
            $uri->get_fragment()."",
            "asd#123+/&()asd"
        );
    }

}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_URI();
    $test->run(new TextReporter());
}

