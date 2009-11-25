<?php

require_once 'setup_tests_environment.php';

require_once 'DF/Web/Routing.php';


class Test_DF_Web_Routing extends UnitTestCase {
    private $routing;

    public function Test_ULB_Routing($name = 'Testing DF_Web_Routing') {
        $this->UnitTestCase($name);

        $config = DF_Web_Config::get_config();

        $this->routing = new DF_Web_Routing($config);
    }

    public function test_url_match() {
        $url = "http://www.utenlandsbolig.no/land/ES";
        $actions = $this->routing->find_actions_by_url($url);
        #error_log("Found ".count($actions). " actions");
        $this->assertTrue(count($actions) > 0);
    }


    public function test_action_country_page() {
        $url = "http://localhost/land/ES";
        $action = new DF_Web_Action(
            'Country',
            'handle_country_page',
            array()
        );

        $actions = $this->routing->find_actions_by_url($url);

        if ($actions) {
            $this->pass("Found some actions");

            $this->assertEqual(
                $action->toString(),
                $actions[count($actions)-1]->toString()
            );
        }
        else {
            $this->fail("No actions found");
        }

    }


    public function test_action_residence_page() {
        $url = "http://localhost/land/ES/sted/Costa%20Blanca/bolig/3-roms-leilighet";
        $action = new DF_Web_Action(
            'Residences',
            'handle_show',
            array()
        );

        $actions = $this->routing->find_actions_by_url($url);

        if ($actions) {
            $this->pass("Found some actions");

            $this->assertEqual(
                $action->toString(),
                $actions[count($actions)-1]->toString()
            );
        }
        else {
            $this->fail("No actions found");
        }
    }


    public function test_actions_matching_no_order_new() {
        $url = "http://localhost/land/ES/sted/Costa%20Blanca/bolig/3-roms-leilighet";
        $expects = array(
            new DF_Web_Action('Root', 'handle_chained', NULL),
            new DF_Web_Action('Country', 'handle_chained', array("ES")),
            new DF_Web_Action('Country', 'handle_location_chained', array("Costa Blanca")),
            new DF_Web_Action('Residences', 'handle_chained', array("3-roms-leilighet")),
            new DF_Web_Action('Residences', 'handle_show', array()),
        );

        $actions = $this->routing->find_actions_by_url($url);

        foreach ($actions as $idx => $action) {
            $found = false;
            foreach ($expects as $expect) {
                if ($action->equals($expect)) {
                    $found = true;
                }
            }

            if ($found) {
                $this->pass("Action found at index $idx: ".$action->toString());
            }
            else {
                $this->fail("Action not expected at index $idx: ".$action->toString());
            }
        }

        foreach ($expects as $idx => $expect) {
            $found = false;
            foreach ($actions as $action) {
                if ($expect->equals($action)) {
                    $found = true;
                }
            }

            if ($found) {
                $this->pass("Action found at index $idx: ".$expect->toString());
            }
            else {
                $this->fail("Got action not expected at index $idx: ".$expect->toString());
            }
        }

    }


#    public function test_actions_matching_no_order() {
#        $url = "http://localhost/land/ES/sted/Costa%20Blanca/bolig/3-roms-leilighet";
#        $expects = array(
#            new DF_Web_Action('Root', 'handle_auto', NULL),
#            new DF_Web_Action('Page', 'handle_auto', NULL),
#            new DF_Web_Action('Residences', 'handle_search_options', NULL),
#            new DF_Web_Action('Country', 'handle_country', array("ES")),
#            new DF_Web_Action('Country', 'handle_location', array("Costa Blanca")),
#            new DF_Web_Action('Residences', 'handle_show', array("3-roms-leilighet")),
#        );
#
#        $actions = $this->routing->find_actions_by_url($url);
#
#        foreach ($actions as $idx => $action) {
#            $found = false;
#            foreach ($expects as $expect) {
#                if ($action->equals($expect)) {
#                    $found = true;
#                }
#            }
#
#            if ($found) {
#                $this->pass("Action found at index $idx: ".$action->toString());
#            }
#            else {
#                $this->fail("Action not expected at index $idx: ".$action->toString());
#            }
#        }
#
#        foreach ($expects as $idx => $expect) {
#            $found = false;
#            foreach ($actions as $action) {
#                if ($expect->equals($action)) {
#                    $found = true;
#                }
#            }
#
#            if ($found) {
#                $this->pass("Action found at index $idx: ".$expect->toString());
#            }
#            else {
#                $this->fail("Got action not expected at index $idx: ".$expect->toString());
#            }
#        }
#
#    }

}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Web_Routing();
    $test->run(new TextReporter());
}

