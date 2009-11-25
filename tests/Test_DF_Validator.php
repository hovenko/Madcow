<?php

require_once 'setup_tests_environment.php';

require_once 'DF/Validator.php';


class Test_DF_Validator extends UnitTestCase {

    protected $rules = array(
        'string'        => array(
            'type'          => 'string',
        ),
        'float'         => array(
            'type'          => 'float',
        ),
        'integer'       => array(
            'type'          => 'integer',
        ),
        'optional'      => array(
            'optional'      => 1,
            'type'          => 'string',
        ),
        'ldap_dn'       => array(
            'type'          => 'ldap_dn',
        ),
        'sub.value'     => array(
            'type'          => 'float',
        ),
        'sub.value2'    => array(
            'type'          => 'float',
        ),
    );

    protected $input = array(
        'string'        => 'sample string',
        'float'         => 1.5,
        'integer'       => 1,
        'optional'      => 'asd',
        'ldap_dn'       => 'dc=utenlandsbolig,dc=no',
        'sub'           => array(
            'value'         => 13,
            'value2'        => 24,
        ),
    );


    public function Test_DF_Validator($name = 'Testing DF_Validator') {
        $this->UnitTestCase($name);
    }

    function testEmpty() {
        $rules = array();
        $input = array();

        $validator = new DF_Validator($rules);
        $res = $validator->validate($input);
        
        $this->assertEqual(0, sizeof($res));
        $this->assertEqual(array(), $res);
    }

    function testDefaults() {
        $rules = $this->rules;
        $input = $this->input;

        $validator = new DF_Validator($rules);
        $res = $validator->validate($input);

        $this->assertEqual(0, sizeof($res));
        $this->assertEqual(array(), $res);
    }

    function testOptional() {
        $rules = $this->rules;
        $input = $this->input;

        $input['optional'] = NULL;
        unset($input['optional']);

        $validator = new DF_Validator($rules);
        $res = $validator->validate($input);

        $this->assertEqual(0, sizeof($res));
        $this->assertEqual(array(), $res);
    }

    function testFailFloatAsInteger() {
        $rules = $this->rules;
        $input = $this->input;

        $input['integer']   = "1.0";

        $validator = new DF_Validator($rules);
        $res = $validator->validate($input);

        $this->assertEqual(1, sizeof($res));
        $this->assertEqual(
            array('integer'),
            array_keys($res)
        );
    }

    function testCastValues() {
        $rules = $this->rules;
        $input = $this->input;

        $input['integer']   = "1";
        $input['float']     = "13.51";

        $validator = new DF_Validator($rules);
        $res = $validator->validate($input);

        $this->assertEqual(0, sizeof($res));
        $this->assertEqual(array(), $res);
    }

    function testFailRequires() {
        $rules = $this->rules;
        $input = array();

        $validator = new DF_Validator($rules);
        $res = $validator->validate($input);

        $this->assertEqual(6, sizeof($res));
    }
    
    function testFailRequireString() {
        $rules = $this->rules;
        $input = $this->input;

        $input['string'] = NULL;

        $validator = new DF_Validator($rules);
        $res = $validator->validate($input);

        $this->assertEqual(
            array('string'),
            array_keys($res)
        );
    }

    function testFailTypes() {
        $rules = $this->rules;
        $input = $this->input;

        $input['integer']   = 0.5;
        $input['float']     = "failing";

        $validator = new DF_Validator($rules);
        $res = $validator->validate($input);

        $this->assertEqual(2, sizeof($res));
        $this->assertEqual(
            array(
                'float',
                'integer',
            ),
            array_keys($res)
        );
    }
    
    function testFailLdapDnType() {
        $rules = $this->rules;
        $input = $this->input;

        $input['ldap_dn']   = "failing";

        $validator = new DF_Validator($rules);
        $res = $validator->validate($input);

        $this->assertEqual(1, sizeof($res));
        $this->assertEqual(
            array(
                'ldap_dn',
            ),
            array_keys($res)
        );
    }
}

if (!defined('TEST_RUNNING')) {
    define('TEST_RUNNING', true);
    $test = new Test_DF_Validator();
    $test->run(new TextReporter());
}

