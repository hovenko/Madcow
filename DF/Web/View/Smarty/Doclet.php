<?php

require_once 'DF/Web/View/Smarty.php';

class DF_Web_View_Smarty_Doclet extends DF_Web_View_Smarty {
    public static $LOGGER = NULL;

    protected $config = array(
        'debugging'         => false,
        'debugging_ctrl'    => 'NONE',
        'caching'           => 0,
        'error'             => 'error.tpl',
    );
}

DF_Web_View_Smarty::$LOGGER = DF_Web_Logger::logger('DF_Web_View_Smarty');
