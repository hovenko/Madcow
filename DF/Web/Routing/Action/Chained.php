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

    /**
     * @var DF_Web_Path
     */
    protected $chained      = NULL;
    
    protected $captures     = NULL;
    protected $is_chained_root  = false;


    /**
     * (non-PHPdoc)
     * @see DF_Web_Routing_Action::init_local()
     * @param DF_Web_Routing_Config_Action $config
     */
    protected function init_local($config) {
        parent::init_local($config);

        if (!$this->has_config_chained($config)) {
            throw new InvalidArgumentException("Missing configuration property: chained");
        }
        
        $this->chained  = $this->prepare_chained($config);
        $this->captures = $this->prepare_captures($config);

        $this->path     = $this->prepare_path($config);
        $this->is_chained_root  = $this->prepare_is_chained_root($this->chained);
    }


    /**
     * @return DF_Web_Path
     */
    public function get_chained() {
        return $this->chained;
    }


    /**
     * 
     * @param DF_Web_Routing_Config_Action $config
     * @return DF_Web_Path
     */
    protected function prepare_chained($config) {
        $chained        = $this->get_config_chained($config);
        $chained_path   = DF_Web_Path::fromString($chained);

        if (!$chained_path->is_absolute()) {
            $controller = DF_Web_Path::fromString("/".$this->controller_path);
            $chained_path = $controller->append_path($chained_path);
        }

        return $chained_path;
    }


    /**
     * (non-PHPdoc)
     * @see DF_Web_Routing_Action::get_path_match()
     * @return DF_Web_Path
     */
    public function get_path_match() {
        if ($this->is_endpoint()) {
            $stars = array();
            $args = $this->args;
            if ($args instanceof DF_Web_Routing_ActionArgs_Any) {
                $stars[] = "**";
            }
            else {
                for ($i=0; $i<$args->get_numargs(); $i++) {
                    $stars[] = "*";
                }
            }
    
            $argspath = join("/", $stars);
    
            $path = $this->get_path();
    
            if ($argspath) {
                $path = $path->append_path(DF_Web_Path::fromString($argspath));
            }
    
            return $path;
        }
        
        return $this->get_path();
    }


    /**
     * 
     * @param DF_Web_Routing_Config_Action $config
     * @return DF_Web_Path
     */
    protected function prepare_path($config) {
        $stars = array();
        $captures = $this->captures;
        for ($i=0; $i<$captures->get_numargs(); $i++) {
            $stars[] = "*";
        }

        $argspath = join("/", $stars);

        $path = $config->get_path();

        #if (!$path->is_absolute()) {
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


    /**
     * 
     * @param DF_Web_Routing_Config_Action $config
     * @return DF_Web_Routing_ActionArgs
     */
    protected function prepare_captures($config) {
        $numargs = 0;
        if ($config->has('captures')) {
            $numargs = $config->get('captures');
        }

        $args = new DF_Web_Routing_ActionArgs($numargs);

        return $args;
    }


    /**
     * 
     * @param DF_Web_Routing_Config_Action $config
     */
    protected function get_config_chained($config) {
        return $config->get('chained');
    }


    /**
     * 
     * @param DF_Web_Routing_Config_Action $config
     */
    protected function has_config_chained($config) {
        if ($config->has('chained')) {
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
