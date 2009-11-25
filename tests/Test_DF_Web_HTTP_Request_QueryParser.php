<?php

require_once 'setup_tests_environment.php';

require_once 'DF/Web/HTTP/Request/QueryParser.php';


class Test_DF_Web_HTTP_Request_QueryParser extends UnitTestCase {

    public function Test_DF_Web_HTTP_Request_QueryParser($name = 'Testing DF_Web_HTTP_Request_QueryParser') {
        $this->UnitTestCase($name);
    }

    function testEmpty() {
        $query  = array();

        $parsed = DF_Web_HTTP_Request_QueryParser::parse_query_params($query);

        $this->assertEqual(0, sizeof($parsed));
    }


    function testSearchQueryParser() {
        $query  = array(
            'search_location'       => 'all',
            'search_type'           => 'all',
            'search_bedrooms-low'   => '1',
            'search_bedrooms-high'  => 'MAX',
            'search_price-low'      => '0',
            'search_price-high'     => 'MAX',
            'search_do_search'      => 'søk',
            'search_freesearch_text'    => '',
        );

        $parsed = DF_Web_HTTP_Request_QueryParser::parse_query_params($query);

        $this->assertEqual(
            'all',
            $parsed['search']['location']
        );

        $this->assertEqual(
            'søk',
            $parsed['search']['do']['search']
        );
    }


    function testSearchQueryBuilder() {
        $search = array(
            'location'  => 'Costa Blanca',
            'type'      => 'villa',
            'bedrooms'  => array(
                'low'       => 1,
                'high'      => 'max',
            ),
            'extra'     => array(
                'swimmingpool' => 'on',
            ),
        );

        $this->assertEqual(
            's.location=Costa+Blanca&s.type=villa&s.bedrooms.low=1&s.bedrooms.high=max&s.extra.swimmingpool=on',
            DF_Web_HTTP_Request_QueryParser::build_query_params_string($search)
        );
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Web_HTTP_Request_QueryParser();
    $test->run(new TextReporter());
}

