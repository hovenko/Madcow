<?php


require_once 'DF/Web/Action.php';
require_once 'DF/Web/Exception.php';
require_once 'DF/Web/Routing/ActionFactory.php';
require_once 'DF/Web/Routing/ActionMatch.php';
require_once 'DF/Web/Routing/Action/PathMismatchException.php';
require_once 'DF/Web/Routing/Component/Name.php';
require_once 'DF/Web/Routing/Config/Controller.php';
require_once 'DF/Web/URL.php';


class DF_Web_Routing {
    public static $LOGGER = NULL;

    protected $config       = NULL;

    protected $actions      = array();


    public function __construct($config) {
        $this->config   = $config;
        $this->init();
    }


    protected function init() {
        $config = $this->config;
        $controllers    = self::controller_configs($config);
        $paths          = self::resolve_controller_paths($controllers);
        $actions        = self::resolve_actions($paths);
        $chained        = self::resolve_chained_actions($actions);

        $this->actions = $actions;
        $this->chained = $chained;
    }


    static protected function resolve_chained_actions($actions) {
        $resolved = array();

        foreach ($actions as $actionpath => $action) {
            if (! $action instanceof DF_Web_Routing_Action_Chained) {
                continue;
            }

            if (!$action->is_endpoint()) {
                continue;
            }

            $chain = array();

            $more = true;

            $chained    = $action;
            $nextpath   = $actionpath;
            $path       = NULL;

            while ($more) {
                $chain[] = $chained;

                if ($path && "$path") {
                    $path   = $chained->get_path_match()->append_path($path);
                }
                else {
                    $path   = $chained->get_path_match();
                }

                # TODO implement recursion
                if ($chained->is_chained_root()) {
                    $more = false;
                }
                else {
                    $chainpath = $chained->get_chained();

                    if (!$chainpath) {
                        throw new DF_Web_Exception("Missing chained on action: $nextpath");
                    }

                    if ($chained = $actions["$chainpath"]) {
                        if (! $chained instanceof DF_Web_Routing_Action_Chained) {
                            throw new DF_Web_Exception("Action not chained: $chainpath ($nextpath)");
                        }

                        $nextpath = "$chainpath";
                    }
                    else {
                        throw new DF_Web_Exception("Chained action not found: $chainpath ($nextpath)");
                    }
                }
            }

            #if (!$path->is_absolute()) {
                $rootpath = DF_Web_Path::fromString("/");
                $path = $rootpath->append_path($path);
            #}

            $chain = array_reverse($chain);
            $resolved["$path"][] = $chain;
        }

        return $resolved;
    }


    static protected function resolve_actions($controllers) {
        $actions = array();

        foreach ($controllers as $path => $ctrl) {
            foreach ($tmp = $ctrl->get_actions() as $action_cfg) {
                $action = DF_Web_Routing_ActionFactory::resolve($action_cfg);

                $action_name = $action->get_name();
                $private_path = "/$action_name";
                if ($path) {
                    $private_path = "/$path$private_path";
                }
                
                $actions[$private_path] = $action;
            }
        }

        return $actions;
    }


    /**
     * Resolves all controller routing configurations.
     * 
     * @param array $config
     * @return array of name and controller configs
     */
    static protected function controller_configs($config) {
        $controller_configs = array();

        foreach ($config as $key => $value) {
            if (preg_match('#^Controller_(.*)#', $key, $matches)) {
                $name = $matches[1];
                $name = new DF_Web_Routing_Component_Name($name);
                $ctrl = new DF_Web_Routing_Config_Controller($name, $value);
                $controller_configs["$name"] = $ctrl;
            }
        }

        return $controller_configs;
    }


    static protected function resolve_controller_paths($controllers) {
        $paths = array();
        foreach ($controllers as $controller) {
            $path = $controller->get_path();

            if (isset($paths["$path"])) {
                $found = $paths["$path"]->get_name();
                throw new DF_Web_Exception("Namespace for $controller already configured on controller $found: $path");
            }

            $paths["$path"] = $controller;
        }

        return $paths;
    }


    protected function find_path_actions_by_path($path) {
        $best = NULL;
    
        foreach ($this->actions as $actionpath => $action) {
            if (!$action instanceof DF_Web_Routing_Action_Path) {
                continue;
            }

            $actionpath = $action->get_path();
            $reason = "";

            $score = -1;
            try {
                $score = self::path_match($path, $actionpath, $action);
            }
            catch (DF_Web_Routing_Action_PathMismatchException $e) {
                $reason = $e->getMessage();
                # path not matched
                $score = -1;
            }

            $match = new DF_Web_Routing_ActionMatch($score, $actionpath, array($action));
            if ($match->is_better_than($best)) {
                $best = $match;
            }
            else {
                #self::$LOGGER->debug("Not matched $path -> $match - $reason");
            }
        }

        if ($best) {
            self::$LOGGER->info("Matched $path -> $best");
        }
        else {
            self::$LOGGER->warn("Not matched $path");
        }

        return $best;
    }


    protected function find_chained_actions_by_path($path) {
        $best = NULL;
    
        foreach ($this->chained as $actionpath => $chained) {
            foreach ($chained as $actions) {
                $actionpath = DF_Web_Path::fromString("$actionpath");
                $reason = "";

                $score = -1;
                try {
                    $score = self::chained_match($path, $actionpath, $actions);
                }
                catch (DF_Web_Routing_Action_PathMismatchException $e) {
                    $reason = $e->getMessage();
                    # path not matched
                    $score = -1;
                }

                $match = new DF_Web_Routing_ActionMatch($score, $actionpath, $actions);
                if ($match->is_better_than($best)) {
                    $best = $match;
                }
                else {
                    #self::$LOGGER->debug("Not matched $path -> $match - $reason");
                }
            }
        }

        if ($best) {
            self::$LOGGER->info("Matched $path -> $best");
        }
        else {
            self::$LOGGER->warn("Not matched $path");
        }

        return $best;
    }


    static protected function path_match($path, $a_path, $action) {
        $a_parts    = $a_path->get_path_parts();
        $a_numparts = count($a_parts);

        $parts      = $path->get_path_parts();
        $numparts   = count($parts);

        if ($numparts < $a_numparts) {
            return -1;
        }
        
        $tmp_parts = $action->get_path()->get_path_parts();
        if (count($parts) < $tmp_parts->get_numparts) {
            $c_rest     = count($rest);
            $c_a_parts  = $tmp_parts->get_numparts();
            throw new DF_Web_Routing_Action_PathMismatchException("Not enough parts for $a_path to eat ($c_rest < $c_a_parts): $path");
            return -1;
        }

        $parts = self::eat_path_parts($action, $parts);

        return count($parts);
    }


    static protected function chained_match($path, $a_path, $actions) {
        $a_parts    = $a_path->get_path_parts();
        $a_numparts = count($a_parts);

        $parts      = $path->get_path_parts();
        $numparts   = count($parts);

        if ($numparts < $a_numparts) {
            return -1;
        }

        $rest = $parts;

        foreach ($actions as $action) {
            $tmp_parts  = $action->get_path()->get_path_parts();
            $c_rest     = count($rest);
            if ($c_rest < $tmp_parts->get_numparts) {
                $c_a_parts  = $tmp_parts->get_numparts();
                throw new DF_Web_Routing_Action_PathMismatchException("Not enough parts for $a_path to eat ($c_rest < $c_a_parts): $path");
            }

            $rest = self::eat_path_parts($action, $rest);
        }

        return count($rest);
    }


    static protected function eat_path_parts($action, $parts) {
        $a_path     = $action->get_path();
        $a_parts    = $a_path->get_path_parts();

        foreach ($a_parts as $tmp) {
            $part = array_shift($parts);

            if ($tmp == '*') {
                // OK, match everything
            }
            elseif ($tmp != $part) {
                throw new DF_Web_Routing_Action_PathMismatchException("Path part not matching action $a_path: $part");
            }
        }

        self::$LOGGER->debug("Action: $action");

        $args = $action->get_args();
        if (NULL === $args) {
            return $parts;
        }

        if ($args instanceof DF_Web_Routing_ActionArgs_Any) {
            return $parts;
        }

        # Got args, need to match it all
        $numargs = $args->get_numargs();
        for ($i = $numargs; $i > 0; $i--) {
            if (!$parts) {
                throw new DF_Web_Routing_Action_PathMismatchException("Not enough path parts to match $a_path. Missing $i args");
            }
        
            $part = array_shift($parts);
        }

        if ($count = count($parts)) {
            throw new DF_Web_Routing_Action_PathMismatchException("Too many path parts to match $a_path. Unmatched: $count");
        }

        return $parts;
    }


    static protected function match_paths($path, $a_path) {
        $a_parts    = $a_path->get_path_parts();
        $a_numparts = count($a_parts);

        $parts      = $path->get_path_parts();
        $numparts   = count($parts);

        if ($numparts < $a_numparts) {
            # Path is too short
            return false;
        }

        for ($i = 0; $i < $numparts; $i++) {
            $a_part = $a_parts[$i];
            $part   = $parts[$i];

            if (!$a_part) {
                # Path is longer
                return true;
            }

            if ($a_part == "*") {
                continue;
            }

            if (! $a_part->equals($part)) {
                return false;
            }
        }

        return true;
    }


    static protected function build_path_action($path, $actions) {
        $ret = array();

        $parts = $path->get_path_parts();

        foreach ($actions as $action) {
            $arguments = array();

            $a_path = $action->get_path();
            $a_parts = $a_path->get_path_parts();
            foreach ($a_parts as $a_part) {
                #error_log("Shifting off $a_part");
                $part = array_shift($parts);

                if ($a_part == '*') {
                    #error_log("Got argument $part");
                    $arguments[] = "$part";
                    continue;
                }
            }
            
            $args = $action->get_args();

            if ($args instanceof DF_Web_Routing_ActionArgs_Any) {
                foreach ($parts as $part) {
                    $arguments[] = "$part";
                }
            }
            elseif ($args) {
                for ($i = 0; $i < $action->get_args()->get_numargs(); $i++) {
                    $part = array_shift($parts);
                    $arguments[] = "$part";
                }
            }

            $obj = new DF_Web_Action(
                $action->get_controller()->get_name()."",
                $action->get_method()."",
                $arguments
            );

            $ret[] = $obj;
        }

        return $ret;
    }


    static protected function build_chain($path, $actions) {
        $ret = array();

        $parts = $path->get_path_parts();

        foreach ($actions as $action) {
            $arguments = array();

            $a_path = $action->get_path();
            $a_parts = $a_path->get_path_parts();
            foreach ($a_parts as $a_part) {
                #error_log("Shifting off $a_part");
                $part = array_shift($parts);

                if ($a_part == '*') {
                    #error_log("Got argument $part");
                    $arguments[] = "$part";
                    continue;
                }
            }

            $captures   = $action->get_captures();

            if ($captures->get_numargs() != count($arguments)) {
                $expect = $captures->get_numargs();
                $got    = count($arguments);
                throw new DF_Web_Exception("Bad match in capture args (got $got != expected $expect");
            }

            // For endpoints
            if ($args = $action->get_args()) {
                for ($i = 0; $i < $action->get_args()->get_numargs(); $i++) {
                    $part = array_shift($parts);
                    $arguments[] = "$part";
                }
            }

            $obj = new DF_Web_Action(
                $action->get_controller()->get_name()."",
                $action->get_method()."",
                $arguments
            );

            $ret[] = $obj;
        }

        return $ret;
    }


    public function find_actions_by_path($path) {
        if (!$path instanceof DF_Web_Path) {
            throw new DF_Error_InvalidArgumentException("path", $path, "DF_Web_Path");
        }
        
        $ret = array();

        if ($best = $this->find_path_actions_by_path($path)) {
            $actions = $best->get_actions();
            $ret = self::build_path_action($path, $actions);
        }
        elseif ($best = $this->find_chained_actions_by_path($path)) {
            $actions = $best->get_actions();
            $ret = self::build_chain($path, $actions);
        }
        else {
            #throw new Exception("No actions found by path: $path");
        }

        return $ret;
    }


    public function find_actions_by_url($url) {
        $url    = DF_Web_URL::fromString($url);
        $path   = $url->get_path();
        #$path   = new DF_Web_Path($url);

        $actions = $this->find_actions_by_path($path);

        return $actions;
    }


}

DF_Web_Routing::$LOGGER = DF_Web_Logger::logger('DF_Web_Routing');
