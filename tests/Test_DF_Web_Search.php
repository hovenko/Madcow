<?php

require_once 'setup_tests_environment.php';

require_once 'DF/Web/Search.php';


class Test_DF_Web_Search extends UnitTestCase {

    public function Test_DF_Web_Search($name = 'Testing DF_Web_Search') {
        $this->UnitTestCase($name);
    }

    function test_simple_search_partial() {
        $input = array(
            'key1'  => 'val1',
            'key2'  => 'val2',
        );

        $search = new DF_Web_Search($input);
        
        $key1part = $search->getPartial('key1');

        $this->assertEqual(
            $key1part->getValue(),
            'val1'
        );

        $this->assertEqual(
            $key1part->getSearch(),
            array('key2' => 'val2')
        );

        $this->assertEqual(
            $key1part->getLegacyStruct(),
            array(
                'type'  => 'key1',
                'value' => 'val1',
                'search'=> array('key2' => 'val2'),
            )
        );
    }


    function test_array_partials() {
        $input = array(
            'somelist'  => array(
                'key1'      => 'val1',
                'key2'      => 'val2',
            ),
        );

        $search = new DF_Web_Search($input);
        
        $part = $search->getPartial('somelist');
        $this->assertEqual(
            $part->getValue(),
            array(
                'key1' => 'val1',
                'key2' => 'val2',
            )
        );


        $subpart = $part->getPartial('key1');
        $this->assertEqual(
            $subpart->getValue(),
            'val1'
        );

        $this->assertEqual(
            $subpart->getSearch(),
            array(
                'somelist'  => array(
                    'key2'      => 'val2',
                ),
            )
        );

        $subparts = $part->getPartials();
        $this->assertEqual(
            count($subparts),
            2,
            "Should be two sub parts"
        );

        $this->assertEqual(
            $subparts[0]->getSearch(),
            array(
                'somelist'  => array(
                    'key2'      => 'val2',
                ),
            )
        );

        $this->assertEqual(
            $subparts[1]->getSearch(),
            array(
                'somelist'  => array(
                    'key1'      => 'val1',
                ),
            )
        );
    }


    function test_array_semi_partials() {
        $input = array(
            'somelist'  => array(
                'key1'      => 'val1',
                'key2'      => 'val2',
            ),
        );

        $search = new DF_Web_Search($input);
        
        $part = $search->getPartial('somelist');
        $this->assertEqual(
            $part->getValue(),
            array(
                'key1' => 'val1',
                'key2' => 'val2',
            )
        );


        $parts = $part->getSemiPartials();
        $this->assertEqual(
            count($parts),
            2,
            "Should be two sub parts"
        );

        $this->assertEqual(
            $parts[0]->getSearch(),
            array(
                'somelist' => array(
                    'key2'  => 'val2',
                ),
            )
        );

        $this->assertEqual(
            $parts[0]->getValue(),
            'key1'
        );

        $this->assertEqual(
            $parts[0]->getKey(),
            'somelist'
        );
    }



}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Web_Search();
    $test->run(new TextReporter());
}

