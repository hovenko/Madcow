<?php

if (!class_exists('Smarty'))
require_once 'smarty/Smarty.class.php';

/**
 * FIXME need a better error handler around Smarty,
 * since Smarty uses trigger_error instead of throwing exceptions.
 */
class DF_Web_View_Smarty extends DF_Web_View {
    public static $LOGGER = NULL;

    private $_smarty    = NULL;

    private $template   = NULL;
    private $error      = FALSE;

    protected $config = array(
        'debugging'         => false,
        'debugging_ctrl'    => 'NONE',
        'caching'           => 0,
        'layout'            => 'layout.tpl',
        'wrapper'           => 'html.tpl',
        'error'             => 'error.tpl',
    );


    /**
     * @return Smarty
     */
    public function getSmarty() {
        return $this->_smarty;
    }


    /**
     * @return boolean
     */
    public function isError() {
        return $this->error;
    }


    /**
     * Sets up the Smarty template engine.
     */
    public function initialize() {
        $config = $this->config();

        $smarty = new Smarty();

        $env        = DF_Web_Environment::singleton();
        $app_root   = $env->app_root;
       
        $smarty->template_dir   = "$app_root/templates";
        $smarty->compile_dir    = "$app_root/templates_c";
        $smarty->cache_dir      = "$app_root/smarty/cache";

        if (@$tmp = $config['templates_dir']) {
            $smarty->templates_dir  = $tmp;
        }

        if (@$tmp = $config['compile_dir']) {
            $smarty->compile_dir    = $tmp;
        }

        if (@$tmp = $config['cache_dir']) {
            $smarty->cache_dir    = $tmp;
        }

        if (@$tmp = $config['plugins_dir']) {
            if (!is_array($tmp)) {
                $tmp = array($tmp);
            }

            foreach ($tmp as $dir) {
                $smarty->plugins_dir[]  = $dir;
            }
        }
        else {
            $smarty->plugins_dir[]  = "$app_root/smarty/plugins";
        }

        $smarty->debugging      = $config['debugging'];
        $smarty->debugging_ctrl = $config['debugging_ctrl'];
        $smarty->caching        = $config['caching'];

        $this->_smarty = $smarty;
    }


    public function set_template($template) {
        if (!is_string($template)) {
            throw new DF_Error_InvalidArgumentException('template', $template, 'string');
        }

        $this->template = $template;
    }


    public function assign($key, $value) {
        $this->_smarty->assign($key, $value);
    }


    public function render() {
        $config     = $this->config;
        $smarty     = $this->getSmarty();
        $template   = $this->template;
        $output     = '';

        if (!$template) {
            throw new DF_Web_Exception("Missing template to render");
        }

        if (!is_file($smarty->template_dir."/$template")) {
            $smarty->assign('error', "Template does not exist: $template");
            $this->error = true;
            $template = $this->config['error'];
        }

        self::$LOGGER->debug("Rendering template: $template");

        $smarty->assign('template', $template);

        if (isset($config['layout']) && $config['layout']) {
            $output = $smarty->fetch($config['layout']);
            $smarty->assign('content', $output);
        }
        
        if (isset($config['wrapper']) && $config['wrapper']) {
            $output = $smarty->fetch($config['wrapper']);
        }

        if (strlen($output) === 0) {
            $output = $smarty->fetch($template);
        }

        self::$LOGGER->debug("Render success");

        return $output;
    }
}

DF_Web_View_Smarty::$LOGGER = DF_Web_Logger::logger('DF_Web_View_Smarty');
