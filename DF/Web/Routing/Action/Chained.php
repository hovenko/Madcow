<?php
/**
 * @package DF_Web
 */


require_once 'DF/Web/Path.php';
require_once 'DF/Web/Routing/Action.php';
require_once 'DF/Web/Routing/Config/Action.php';


/**
 */
class DF_Web_Routing_Action_Chained
        extends DF_Web_Routing_Action {
    public static $LOGGER = NULL;

    protected $config       = NULL;
    protected $chained      = NULL;
    protected $captures     = NULL;
    protected $is_chained_root  = false;


    protected function init_local($config) {
        parent::init_local($config);

        $this->config = $config;
        
        if (!$this->has_config_chained()) {
            throw new InvalidArgumentException("Missing configuration property: chained");
        }
        
        $this->chained  = $this->prepare_chained();
        $this->captures = $this->prepare_captures($config);

        $this->path     = $this->prepare_path();
        $this->is_chained_root  = $this->prepare_is_chained_root($this->chained);

        unset($this->config);
    }


    public function get_chained() {
        return $this->chained;
    }


    protected function prepare_chained() {
        $chained        = $this->get_config_chained();
        $chained_path   = DF_Web_Path::fromString($chained);

        if (!$chained_path->is_absolute()) {
            $controller = DF_Web_Path::fromString("/".$this->controller->get_path());
            $chained_path = $controller->append_path($chained_path);
        }

        return $chained_path;
    }


    public function get_path_match() {
        if ($this->is_endpoint()) {
            return parent::get_path_match();
        }
        
        return $this->get_path();
    }


    protected function prepare_path() {
        $config = $this->config;

        $stars = array();
        $captures = $this->captures;
        for ($i=0; $i<$captures->get_numargs(); $i++) {
            $stars[] = "*";
        }

        $argspath = join("/", $stars);

        $path = $config->get_path();

        #if ($this->is_chained_root() && $path->is_absolute()) {
        #    $controller = new DF_Web_Path("/".$this->controller->get_path());
        #    $path = $controller->append_path($path);
        #}

        if ($argspath) {
            $path = $path->append_path(DF_Web_Path::fromString($argspath));
        }
        
        return $path;
    }


    public function get_captures() {
        return $this->captures;
    }


    protected function prepare_is_chained_root($chained) {
        if ($chained == "/") {
            return true;
        }

        return false;
    }


    protected function prepare_captures($config) {
        $numargs = 0;
        if ($config->has('captures')) {
            $numargs = $config->get('captures');
        }

        $args = new DF_Web_Routing_ActionArgs($numargs);

        return $args;
    }


    protected function get_config_chained() {
        return $this->config->get('chained');
    }


    protected function has_config_chained() {
        if ($this->config->has('chained')) {
            return true;
        }
        
        return false;
    }


    public function is_chained_root() {
        return $this->is_chained_root;
    }


    public function is_endpoint() {
        if ($this->get_args()) {
            return true;
        }

        return false;
    }


    public function __toString() {
        $parent = parent::__toString();

        $chained = $this->chained;
        $captures   = $this->captures;
        $root_chained = $this->is_chained_root ? "Root chained" : "Chained";
        
        $str = "$parent, $root_chained: $chained, Captures: $captures";
        return $str;
    }
}

DF_Web_Routing_Action_Chained::$LOGGER = DF_Web_Logger::logger('DF_Web_Routing_Action_Chained');
