<?php

# TODO make autoload for errors
require_once 'DF/Error.php';
require_once 'DF/Error/InvalidArgumentException.php';

require_once 'DF/Web/Action.php';
require_once 'DF/Web/Component/Loader.php';
require_once 'DF/Web/Config.php';
require_once 'DF/Web/Controller.php';
require_once 'DF/Web/Detach/Exception.php';
require_once 'DF/Web/Environment.php';
require_once 'DF/Web/HTTP/Path.php';
require_once 'DF/Web/HTTP/Request.php';
require_once 'DF/Web/HTTP/Request/MagicQuotes.php';
require_once 'DF/Web/HTTP/Response.php';
require_once 'DF/Web/Logger.php';
require_once 'DF/Web/Model.php';
require_once 'DF/Web/Routing.php';
require_once 'DF/Web/SessionHandler.php';
require_once 'DF/Web/Time.php';
require_once 'DF/Web/Utils/Arguments.php';
require_once 'DF/Web/View.php';


class DF_Web {
    public static $LOGGER = NULL;

    public $request     = NULL;
    protected $actions  = NULL;
    public $response    = NULL;
    public $session     = NULL;
    protected $base_path    = NULL;
    protected $errors   = array();
    protected $config   = NULL;
    protected $debug    = false;
    public $environment = NULL;

    protected $start_time = 0;

    public $stash       = array();
    protected $stack    = array();

    protected $action_classes   = array();

    protected $user         = NULL;
    protected $_tried_auth  = false;



    public function __construct() {
        $this->start_time = DF_Web_Time::microtime_float();

        $this->config = DF_Web_Config::get_config();

        $this->setup_include_path();

        $this->setup_error_handler();

        $this->setup_environment();

        # Request
        $this->setup_base_path();
        $this->setup_request();
        $this->setup_session($this->base_path);

        $this->routing = $this->prepare_routing();


        # Response
        $this->response = new DF_Web_HTTP_Response();

        $this->setup_defaults();

        if ($this->debug) {
            self::$LOGGER->debug("###");
            self::$LOGGER->debug("### Context initialized");
        }
    }


    /**
     * Returns true if debugging is enabled, false if not.
     * 
     * This is controlled from the environment setup
     * in {@see setup_environment()}
     *
     * @return boolean
     */
    public function is_debug() {
        return $this->debug;
    }


    protected function setup_error_handler() {
        set_error_handler(
            array($this, 'default_error_handler'),
            E_ALL & ~E_STRICT & ~E_NOTICE
        );
    }


    /**
     * The default error handler of Madcow.
     *
     * It logs all messages as warnings using the default logger.
     */
    public function default_error_handler($errno, $errstr, $errfile, $errline) {
        if (preg_match('#DF/Web/.*\.php#', $errfile)) {
            // internal code
            throw new DF_Web_Exception("$errstr errno:$errno file:$errfile line:$errline");
        }
        self::$LOGGER->warn("$errstr $errno $errfile $errline");
    }


    private function setup_environment() {
        $env = DF_Web_Environment::singleton();
        $this->environment = $env;
        $this->set_debug($env->debug);

        if ($env->environment == DF_Web_Environment::$ENV_DEVELOPMENT) {
            ini_set('display_errors', 1);
        }
    }


    public function getConfig() {
        $conf = DF_Util_Arrays::asArray($this->config);
        return $conf;
    }


    public function setup_include_path() {
        $config     = $this->getConfig();
        $conf_php   = DF_Util_Arrays::asArray($config['php']);
        $paths      = DF_Util_Arrays::asArray($conf_php['includes']);
    
        foreach ($paths as $path) {
            self::$LOGGER->debug("Adding include path: $path");
            set_include_path($path . ":" . get_include_path());
        }
    }


    /**
     * Use to enable or disable debugging.
     *
     * @param boolean $bool
     */
    public function set_debug($bool) {
        $bool = $bool ? TRUE : FALSE;
        $this->debug            = $bool;
        $this->stash['debug']   = $bool;
    }


    /**
     * Same as is_debug()
     *
     * @see is_debug()
     * @deprecated use is_debug() instead
     * @return boolean
     */
    public function debug() {
        return $this->debug;
    }


    // FIXME cannot be here
    protected function setup_defaults() {
        $this->stash['site']['title']   = $this->config['title'];

        $this->response->add_stylesheet(
            "reset",
            $this->uri_for('/static/css/yahoo/reset-fonts-grids-min.css')
        );

        $this->response->add_stylesheet(
            "layout",
            $this->uri_for('/static/css/layout.css')
        );

        $this->response->add_stylesheet(
            "public",
            $this->uri_for('/static/css/public.css')
        );
    }


    /**
     * Use to build URLs for the web site of the current context.
     * It will prepend the base path of the web site automatically
     * together with the domain name.
     *
     * It takes a flexible number of parameters.
     * The first parameter should be a path,
     * absolute to the web root or relative to the current request.
     *
     * Any number of path arguments can be given, where all paths will be
     * joined together separated with slash (/).
     *
     * The last argument can optionally be an array of query parameters,
     * a map of param name and value.
     * 
     * Examples of possible arguments:
     *  - "/"
     *  - "some_other_actions"
     *  - "/some_action", array("key" => "value")
     *  - array("key" => "value")
     *
     * @return string
     */
    public function uri_for() {
        $base_path = $this->base_path;
        $hostname  = $this->request->hostname;

        $numargs    = func_num_args();
        $args       = func_get_args();

        if (!$args) {
            return '';
        }

        $query  = array();

        if (is_array($args[$numargs-1])) {
            $query = array_pop($args);
        }

        // The first part is the main path, we dont urlencode this
        $path   = array_shift($args);

        if ($args) {
            // Strip of the last / of the part before adding pathparts
            $path = preg_replace('|/$|', '', $path);
            foreach ($args as $arg) {
                $arg = urlencode($arg);
                $path .= '/'.$arg;
            }
        }

        $query_arr = array();
        if ($query)
        foreach ($query as $key => $val) {
            $query_arr[] = sprintf('%s=%s', urlencode($key), urlencode($val));
        }

        if ($query_arr) {
            $path .= "?" . join('&', $query_arr);
        }

        $req_url = $this->request->url;

        if (preg_match('#^/#', $path)) {
            // Absolute path

            if ($base_path != '/') {
                // Prepends the base path if the web application is not on web root
                $path = $base_path . $path;
            }

            # TODO detect http/https
            $uri = "http://$hostname$path";
        }
        elseif (preg_match('#^\?#', $path)) {
            $tmp = preg_replace('#\?.*#', '', $req_url);
            // Path only includes query params
            $uri = $tmp.$path;
        }
        else {
            // Relative path, we remove the last path part from request url
            $tmp = preg_replace('#/[^/]*$#', '', $req_url);
            if ($path) {
                $tmp .= "/";
            }
            $uri = $tmp.$path;
        }

        return $uri;
    }


    /**
     * Loads a component by class name.
     * 
     * If the component class has a method named "ACCEPT_CONTEXT"
     * that method will be invoked on the object instance
     * after the component is initialized.
     * The context object (instance of this class) will be passed in as
     * an argument to ACCEPT_CONTEXT.
     *
     * @see DF_Web_Component_Loader for component initialization
     * @param string $class
     * @return DF_Web_Component
     */
    protected function component($class) {
        if (!is_string($class)) {
            throw new DF_Error_InvalidArgumentException("class", $class, "string");
        }
        
        #self::$LOGGER->debug("Looking up component: $class");
        $component = NULL;

        try {
            $component = DF_Web_Component_Loader::component($class, $this);
        }
        catch (DF_Web_Component_LoaderException $ex) {
            self::$LOGGER->error("Error loading component $class:\n".$ex->__toString());
            // Dont need the entire stack trace of the component loader
            throw new DF_Web_Exception("Failed loading component $class:\n".$ex->getMessage());
        }
        catch (DF_Web_Exception $ex) {
            #self::$LOGGER->error("Error initializing component $class:\n".$ex->__toString());
            throw $ex;
            #throw new DF_Web_Exception($ex->getMessage());
        }

        if (method_exists($component, 'ACCEPT_CONTEXT')) {
            try {
                return $component->ACCEPT_CONTEXT($this);
            }
            catch (DF_Web_Exception $ex) {
                throw $ex;
                #throw new DF_Web_Exception(
                #    "Failed executing {$class}->ACCEPT_CONTEXT(): "
                #    .$ex->getMessage().", exception in "
                #    .sprintf("%s(%d)",$ex->getFile(),$ex->getLine())
                #);
            }
        }

        return $component;
    }


    /**
     * Returns the class name of the context.
     *
     * @return string
     */
    public function get_context_class() {
        $classname = get_class($this);
        return $classname;
    }


    public function view($name) {
        if (!is_string($name)) {
            throw new DF_Error_InvalidArgumentException("name", $name, "string");
        }
        
        $classname = $this->get_context_class();
        $prefix = "{$classname}_View_";

        try {
            return $this->component_prefixed($prefix, $name);
        }
        catch (DF_Web_Exception $ex) {
            // Dont need the entire stack trace of the component loader
            throw new DF_Web_Exception($ex->getMessage());
        }
    }
    

    public function model($name) {
        if (!is_string($name)) {
            throw new DF_Error_InvalidArgumentException("name", $name, "string");
        }
        
        if (isset($this->config['testmodels'])) {
            $testmodels = $this->config['testmodels'];
            if (isset($this->config['testmodels'][$name])) {
                return $this->component($testmodels[$name]);
            }
        }

        $classname = $this->get_context_class();
        $prefix = "{$classname}_Model_";

        try {
            return $this->component_prefixed($prefix, $name);
        }
        catch (DF_Web_Exception $ex) {
            throw $ex;
            // Dont need the entire stack trace of the component loader
            throw new DF_Web_Exception($ex->getMessage());
        }
    }


    public function controller($name) {
        if (!is_string($name)) {
            throw new DF_Error_InvalidArgumentException("name", $name, "string");
        }
        
        $classname = $this->get_context_class();
        $prefix = "{$classname}_Controller_";

        try {
            return $this->component_prefixed($prefix, $name);
        }
        catch (DF_Web_Exception $ex) {
            // Dont need the entire stack trace of the component loader
            throw new DF_Web_Exception($ex->getMessage());
        }
    }


    private function component_prefixed($prefix, $name) {
        if (!is_string($prefix)) {
            throw new DF_Error_InvalidArgumentException("prefix", $prefix, "string");
        }

        if (!is_string($name)) {
            throw new DF_Error_InvalidArgumentException("name", $name, "string");
        }

        $class  = "{$prefix}{$name}";
        return $this->component($class);
    }


    public function prepare_routing() {
        $config = $this->config;
        return new DF_Web_Routing($config);
    }


    static public function split_uri($uri) {
        if (!preg_match('|^(\w+):(.*)|', $uri, $matches)) {
            self::$LOGGER->warning("Not a valid URI: $uri");
            return;
        }

        $schema = $matches[1];
        $rest   = $matches[2];

        return array(
            'schema'    => $schema,
            'rest'      => $rest,
            'uri'       => $uri,
        );
    }


    public function resolve_uri($uri) {
        $parts = self::split_uri($uri);

        $schema = $parts['schema'];
        $rest   = $parts['rest'];

        switch ($schema) {
            case 'http':
            case 'https':
                return $uri;

            case 'madcow':
                $uri = $this->resolve_madcow_uri($rest);
                return $this->resolve_uri($uri);
        }

        return $this->resolve_uri_local($uri);
    }


    protected function resolve_uri_local($uri) {
        $parts = self::split_uri($uri);

        $schema = $parts['schema'];
        $rest   = $parts['rest'];

        return $uri;
        #throw new DF_Web_Exception("Don't know how to handle schema: $schema");
    }


    public function process_uri($uri) {
        $uri = $this->resolve_uri($uri);

        $parts = self::split_uri($uri);
        $schema = $parts['schema'];
        $rest   = $parts['rest'];

        self::$LOGGER->debug("Handling schema $schema of $rest");

        switch ($schema) {
            case 'http':
            case 'https':
                return $this->response->redirect($uri);

            case 'file':
                # TODO content type
                $this->response->body(file_get_contents($rest));
                return;

            default:
                $this->response->status(404);
                $this->response->body("Unable to locate resource");
                return;
        }
    }


    private function resolve_madcow_uri($uri) {
        preg_match('|^(\w+):(.*)|', $uri, $matches);
        $schema = $matches[1];
        $rest   = $matches[2];
        
        switch ($schema) {
            case 'uri_for':
                return $this->uri_for($rest);

            default:
                throw new DF_Web_Exception("Unknown madcow schema: $schema");
        }
    }


    /**
     * @return DF_Web_Routing
     */
    public function getRouting() {
        return $this->routing;
    }


    public function execute() {
        $request    = $this->request;

        $path       = DF_Web_Path::fromString($request->get_path());
        $actions    = $this->routing->find_actions_by_path($path);
        
        if (!$actions) {
            $this->response->status(500);
            $ex = new DF_Web_Exception("No action found from path: $path");
            $this->add_error($ex);
            return;
        }

        $lastaction = NULL;
        $lastend    = NULL;

        $auto_run   = array();

        try {
            foreach ($actions as $action) {
                $name = $action->get_controller();
                $controller = $this->controller($name);

                if (!isset($auto_run[$name])) {
                    if (method_exists($controller, 'handle_auto')) {
                        $autoaction = new DF_Web_Action(
                            $name,
                            'handle_auto',
                            NULL
                        );

                        $auto_run[$name] = 1;

                        $ret = $autoaction->dispatch($this);

                        if (!$ret) {
                            throw new DF_Web_Detach_Exception($autoaction, "Returned false from $name/handle_auto");
                        }
                    }
                }

                $ret = $action->dispatch($this);
                $lastaction = $action;

                if (method_exists($controller, 'handle_end')) {
                    $lastend = $action;
                }

                if (false === $ret) {
                    self::$LOGGER->info("Execution of action aborted: ".$action->get_controller()."/".$action->get_method());
                    break;
                }
            }
        }
        catch (DF_Web_Detach_Exception $ex) {
            $action = $ex->get_action();
            self::$LOGGER->debug("Detached after action: ".$action->get_controller()."/".$action->get_method());
            
            $lastaction = $action;
            $controller = $this->controller($action->get_controller());
            if (method_exists($controller, 'handle_end')) {
                $lastend = $action;
            }
        }
        catch (DF_Web_Exception $ex) {
            self::$LOGGER->error("Failed executing action: ".$ex->getMessage());

            $action_err = new DF_Web_Action(
                'Error',
                'handle_exception',
                array($ex)
            );

            try {
                $action_err->dispatch($this);
                $lastaction = $action_err;
            }
            catch (DF_Web_Exception $inner_ex) {
                self::$LOGGER->info("No Error controller to dispatch exception to");
                $this->add_error($ex);
            }
        }

        if ($lastend) {
            $endaction = new DF_Web_Action(
                $lastend->get_controller(),
                'handle_end',
                NULL
            );

            try {
                self::$LOGGER->debug("Executing end action controller: ".$endaction->get_controller());
                $endaction->dispatch($this);
            }
            catch (DF_Web_Exception $ex) {
                self::$LOGGER->error("Failed executing action: ".$ex->getMessage());

                $action_err = new DF_Web_Action(
                    'Error',
                    'handle_exception',
                    array($ex)
                );

                $action_err->dispatch($this);
                $lastaction = $action_err;
            }
        }

        # Autodetect template only if not redirecting?
        if (!isset($this->stash['template']) && !$this->response->redirect()) {
            $template = self::template_from_action($lastaction);
            $this->stash['template'] = $template;
        }
    }


    /**
     * Returns an autodetected path to a template based on
     * the private path of the given action.
     *
     * @param DF_Web_Action $action
     * @return string
     */
    static protected function template_from_action($action) {
        if (!$action instanceof DF_Web_Action) {
            throw new DF_Error_InvalidArgumentException('action', $action, DF_Web_Action);
        }
        
        $template = sprintf(
            '%s.tpl',
            $action->get_private_path()
        );

        return $template;
    }


    /**
     * This method creates a new action instance and executes it.
     * After the action is finished running the code continues
     * from where this method was called.
     * 
     * @param mixed $command [required] either controller or method name
     * @param mixed $args [optional] either method name or arguments
     * @param mixed $args2 [optional] arguments
     * @return boolean true if action was successfully executed, false if not
     */
    public function forward($command, $args = NULL, $args2 = NULL) {
        $action = $this->buildAction($command, $args, $args2);
        return $action->dispatch($this);
    }


    /**
     * This method creates a new action instance and executes it.
     * After the action is finished it throws an DF_Web_Detach_Exception'
     * to tell the framework to about further execution.
     *
     * If the $command argument is a null reference, the execution will
     * detach.
     * 
     * @param mixed $command [optional] either controller or method name
     * @param mixed $args [optional] either method name or arguments
     * @param mixed $args2 [optional] arguments
     * @throws DF_Web_Detach_Exception
     */
    public function detach($command = NULL, $args = NULL, $args2 = NULL) {
        $action = NULL;
        
        if ($command !== NULL) {
            $action = $this->buildAction($command, $args, $args2);
            $ret    = $action->dispatch($this);
        }
        else {
            $action = $this->get_current_action();
        }

        throw new DF_Web_Detach_Exception($action, "Detached from action");
    }


    /**
     * This method creates a new action instance.
     * 
     * This method can support various combinations of arguments.
     * Here are some examples:
     *  - forward("SomeController", "some_method", array("arg1", 1337));
     *  - forward($other_ctrl, "do_something", array("arg1", 1337));
     *  - forward("my_own_method", array("arg1", 1337));
     *  - forward("my_own_method");
     * 
     * @param mixed $command [required] either controller or method name
     * @param mixed $args [optional] either method name or arguments
     * @param mixed $args2 [optional] arguments
     * @return DF_Web_Action
     */
    protected function buildAction($command, $args = NULL, $args2 = NULL) {
        $action = NULL;

        if ($args2 != NULL && !is_array($args2)) {
            // args2 must be an array of arguments or NULL
            throw new DF_Web_Exception("The third argument must be an array of arguments or NULL. Got $args2 (".gettype($args2).")");
        }

        if ($command == NULL) {
            self::$LOGGER->debug("No action to forward to");
            throw new DF_Web_Exception("No action to forward to");
        }
        elseif ($command instanceof DF_Web_Action) {
            // We don't need args2 in this case
            if (is_array($args)) {
                $command->set_arguments($args);
            }
            else {
                $command->set_arguments(NULL);
            }
            $action = $command;
        }
        elseif (is_string($command) && is_string($args)) {
            // Both the controller and method are given as strings
            $action = new DF_Web_Action($command, $args, $args2);
        }
        elseif (is_string($command) && (is_array($args) || $args == NULL)) {
            // The command is not given, so we use the current controller to
            // execute the method on
            $orig   = $this->get_current_action();
            $action = new DF_Web_Action(
                $orig->get_controller(),
                $command,
                $args
            );
        }
        else {
            self::$LOGGER->debug("No action to forward to");
            throw new DF_Web_Exception("No action to forward to");
        }

        return $action;
    }


    protected function get_current_action() {
        $action = $this->stack[count($this->stack)-1];
        return $action;
    }


    public function register_action_class($class) {
        $obj = new $class();
        $obj->initialize($this);
        $name = $obj->get_name();
        $this->action_classes[$name] = $obj;
    }


    public function ACTION_CLASS($name) {
        if (!is_string($name)) {
            throw new DF_Error_InvalidArgumentException('name', $name, 'string');
        }
        
        $handler = NULL;
        if (isset($this->action_classes[$name])) {
            $handler = $this->action_classes[$name];
        }
        else {
            throw new DF_Web_Exception("No such action class: $name");
        }

        $action = $this->stack[count($this->stack)-1];
        
        return $handler->dispatch($this, $action);
    }


    public function execute_action($action) {
        if (!$action instanceof DF_Web_Action) {
            throw new DF_Error_InvalidArgumentException("action", $action, "DF_Web_Action");
        }

        $class  = $action->get_controller();
        $controller = $this->controller($class);
        
        // Add the action to the stack
        $this->stack[] = $action;

        $ret = $action->execute($controller, $this);
        
        // Removing the last action from the stack after it returns
        array_pop($this->stack);
        
        return $ret;
    }


    public function setup_base_path($path = NULL) {
        if ($path) {
            # FIXME is this ever called? do we need it or should we always depend on the environment?
            $this->base_path = $path;
        }
        else {
            $env                = $this->environment;
            $this->base_path    = $env->base_path;
        }
    }


    /**
     * Adds an error/exception to the errors stack.
     *
     * @param Exception $exception
     */
    public function add_error($exception) {
        if (!$exception instanceof Exception) {
            throw new DF_Error_InvalidArgumentException("exception", $exception, Exception);
        }
        
        $this->errors[] = $exception;
    }


    private function setup_session_handler() {
        $sh = new DF_Web_SessionHandler();
        session_set_save_handler(
            array($sh, "open"),
            array($sh, "close"),
            array($sh, "read"),
            array($sh, "write"),
            array($sh, "destroy"),
            array($sh, "gc")
        );
    }


    private function setup_session($base_path) {
        if ($this->environment->environment == DF_Web_Environment::$ENV_TESTS) {
            return false;
        }

        $this->setup_session_handler();

        session_set_cookie_params(0, "$base_path");

        session_start();

        // Storing the session id in the session, for easy reference
        $_SESSION['session_id'] = session_id();

        $this->session =& $_SESSION;
    }


    # TODO move to an HTTP Engine Request Factory
    private function setup_request() {
        $env        = DF_Web_Environment::singleton();
        $request    = new DF_Web_HTTP_Request();

        $base_path  = $this->base_path;

        $req_path   = $_SERVER['REQUEST_URI'];
        $req_method = $_SERVER['REQUEST_METHOD'];
        $hostname   = $_SERVER['HTTP_HOST'];
        $port       = $_SERVER['SERVER_PORT'];

        if ($req_path && !DF_Web_HTTP_Path::has_base_path($req_path, $base_path)) {
            throw new DF_Web_Exception("Requested path does not contain base path: $req_path");
        }

        $req_path   = DF_Web_HTTP_Path::strip_base_path($req_path, $base_path);
        $req_path   = DF_Web_HTTP_Path::strip_query_params($req_path);

        $request->set_path($req_path);
        $request->set_base_path($base_path);

        list($path, $_jimbo) = DF_Web_HTTP_Path::split_params($req_path);
        $arguments  = DF_Web_HTTP_Path::split_parts($path);
        $request->set_arguments($arguments);

        $request->set_hostname($hostname);
        $request->set_port($port);

        $request->set_method(strtolower($req_method));

        if (get_magic_quotes_gpc()) {
            DF_Web_HTTP_Request_MagicQuotes::stripslash_request();
        }

        if ($_POST) {
            $request->set_body_parameters($_POST);
        }

        if ($_COOKIE) {
            $request->set_cookies($_COOKIE);
        }

        if ($_GET) {
            $request->set_query_parameters($_GET);
        }

        if ($_FILES) {
            $request->set_uploads($_FILES);
        }

        
        $proxies    = $env->trusted_proxies;
        $ipAddress  = self::extract_client_address($proxies - 1);
        $request->set_address($ipAddress);

        # TODO how do we figure this out? probably not needed anyway
        $request->set_secure(false);


        $request->finalize();

        $this->request = $request;

        return true;
    }


    static private function extract_client_address($proxied_idx = 0) {
        if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip_address = $_SERVER["REMOTE_ADDR"];
        }
        else {
            $ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
            if (strpos($ip_address, ',') !== false) {
                $ip_address = explode(',', $ip_address);
                $ip_address = $ip_address[$proxied_idx];
            }
        }

        return $ip_address;
    }


    protected function render_body() {
        $response = $this->response;

        if ($response->body) {
            if ($this->debug)
            self::$LOGGER->debug(
                "We already have some data to output."
                ." Not rendering templates."
            );
            return;
        }

        $viewname   = $this->config['default_view'];
        if (isset($this->stash['current_view'])
                && $this->stash['current_view']) {
            $viewname = $this->stash['current_view'];
        }

        if ($location = $this->response->redirect()) {
            if ($this->debug)
            self::$LOGGER->debug(
                "Redirecting to $location."
            );
            return;
        }
        #elseif ($this->response->is_error()) {
        #    $status = $this->response->status;
        #    if ($this->debug)
        #    self::$LOGGER->debug(
        #        "Sending an error response status $status."
        #    );
        #    return;
        #}
        elseif ($viewname) {
            if ($this->debug)
            self::$LOGGER->debug("View handler: $viewname");

            $template = $this->stash['template'];

            $this->stash['debug_dump']['stash']     = print_r($this->stash, true);
            $this->stash['debug_dump']['session']   = print_r($this->session, true);

            // Stashing session
            $this->stash['session'] = $this->session;

            $this->stash['javascripts'] = $response->get_javascripts();
            $this->stash['stylesheets'] = $response->get_stylesheets();

            // Setting context object
            $this->stash['c'] = $this;

            $view = $this->view($viewname);

            foreach ($this->stash as $key => $value) {
                $view->assign($key, $value);
            }

            $execution_time = $this->get_execution_time();
            $view->assign('execution_time', $execution_time);
            $view->assign('memory', $this->get_memory_usage());
            $view->assign('server', $this->get_server_info());

            $view->set_template($template);

            $response->body = $view->render();

            if ($view->isError()) {
                $this->response->status(500);
                self::$LOGGER->error("Error rendering template: $template");
            }
        }
        else {
            throw new DF_Web_Exception("No template to render");
        }
    }

    private function get_execution_time() {
        return DF_Web_Time::microtime_float() - $this->start_time;
    }


    public function log_execution_time() {
        $time = $this->get_execution_time();
        $time = round($time, 3);
        self::$LOGGER->debug("Page processing time: $time seconds");
    }


    private function get_server_info() {
        $server = array();

        $server['ip'] = $_SERVER['SERVER_ADDR'];

        return $server;
    }

    private function get_memory_usage() {
        $memory = array();

        $memory['usage']        = memory_get_usage() / 1000000.;
        $memory['peak_usage']   = memory_get_peak_usage() / 1000000.;

        return $memory;
    }


    static public function loadClassFile($class) {
        $filename = preg_replace('|_|', '/', $class).".php";
        include_once "$filename";
        if (!class_exists($class)) {
            throw new DF_Web_Exception("Class does not exist: $class");
        }
    }


    public function create_empty_user() {
        $userclass = $this->config['authentication']['userclass'];

        self::loadClassFile($userclass);
        
        $user = new $userclass();
        
        return $user;
    }


    /**
     * Returns the user object.
     *
     * @return DF_Web_User
     */
    public function get_user() {
        if (!$this->user && !$this->_tried_auth) {
            $this->authenticate_user();
            $this->_tried_auth = true;
        }

        return $this->user;
    }


    public function set_user($user = NULL) {
        if ($user) {
            $this->user = $user;
            $this->session['uid']   = $user->getUid();
            $this->session['user']  = $user->asStruct();
        }
        else {
            $this->unset_user();
        }
    }


    protected function unset_user() {
        self::$LOGGER->info("Unsetting user object, logging out");
        $this->user = NULL;
        unset($this->session['uid']);
        unset($this->session['user']);
    }


    public function authenticate_user($user = NULL) {
        if ($user) {
            if ($user->validate_credentials()) {
                $uid = $user->getUid();
                self::$LOGGER->info("User authenticated: $uid");
                $this->set_user($user);
                return true;
            }
            else {
                self::$LOGGER->info("User failed authentication: $uid");
            }

            return false;
        }

        $session = $this->session;
        if ($uid = $session['uid']) {
            self::$LOGGER->info("Lookin up user by uid from session: $uid");
            $user = $this->create_empty_user();
            $user->findByUid($uid);
            if ($user->exists()) {
                $this->set_user($user);
                return true;
            }
        }

        return false;
    }


    public function finalize() {
        if ($this->errors) {
            $this->finalize_error();
        }

        $this->render_body();

        $this->finalize_headers();

        $this->finalize_body();

        if ($this->debug) {
            $this->log_execution_time();
            self::$LOGGER->debug("   ### Finalized response");
            self::$LOGGER->debug("");
        }
    }


    private function finalize_error() {
        $errors = $this->errors;
        $response = $this->response;

        $response->status = 500;

        $response->body = <<<EOHTML
<html><body>
<pre>
(en) Please come back later
(fr) SVP veuillez revenir plus tard
(de) Bitte versuchen sie es spaeter nocheinmal
(at) Konnten's bitt'schoen spaeter nochmal reinschauen
(no) Vennligst prov igjen senere
(dk) Venligst prov igen senere
(pl) Prosze sprobowac pozniej
</pre>
</body></html>
EOHTML;

        
        $error      = $this->errors[0];
        $class      = get_class($error);
        $message    = $error->getMessage();
        if ($error instanceof NRK_Exception) {
            $message    = $error->getNestedMessage();
        }

        $exceptiondetails = sprintf("exception in %s(%d)", $error->getFile(), $error->getLine());

        $stacktrace = $error->getTraceAsString();
        self::$LOGGER->error("Caught an error ($class): $message");
        self::$LOGGER->debug("Stack trace: $stacktrace");

        if ($this->debug) {
            $stash      = print_r($this->stash, true);
            $request    = print_r($this->request, true);
            $session    = print_r($this->session, true);
            $server     = print_r($_SERVER, true);
            #$message    = $error->getMessage();
            #$stacktrace = $error->getTraceAsString();
            $response->body = <<<EOHTML
<html>
<head>
<title>Error</title>
<style type="text/css">
<!--
.section {
  margin-bottom: 2em;
}

pre {
  margin: 0;
}
-->
</style>
</head>
<body>

<div class="section">
<pre>$class: $message</pre>
<pre>$exceptiondetails</pre>
<pre>$stacktrace</pre>
</div>

<div class="section">
Base path: $this->base_path
</div>

<div class="section">
Stash:
<pre>$stash</pre>
</div>

<div class="section">
Request:
<pre>$request</pre>
</div>

<div class="section">
Session:
<pre>$session</pre>
</div>

<div class="section">
Server:
<pre>$server</pre>
</div>
</body></html>
EOHTML;
        }

        $response->_finalized_error = true;
    }


    private function finalize_headers() {
        $response = $this->response;
        
        if ($response->_finalized_headers) {
            return;
        }

        if ($location = $response->redirect()) {
            if ($this->debug)
            self::$LOGGER->debug("Redirecting to $location");

            $response->add_header('Location', $location);

            if (!$response->body) {
                $location_esc = htmlentities($location);
                $response->body = <<<EOHTML
<html>
    <body>
        <p>This item has moved <a href="$location_esc">here</a>.</p>
    </body>
</html>
EOHTML;
            }
        }

        if (preg_match('#^(1\d\d|[23]04)$#', $response->status)) {
            $response->body = '';
        }

        $status = $response->status;
        $this->response->add_header("HTTP/1.0", $status);

        if ($content_type = $response->content_type) {
            if ($content_encoding = $response->content_encoding) {
                $content_type .= "; charset=$content_encoding";
            }
        }
        else {
            $content_type = "text/html";
        }

        $this->response->add_header("Content-Type", $content_type);

        foreach ($this->response->headers as $header) {
            $key    = $header['name'];
            $value  = $header['value'];
            header("$key: $value");
        }

        $this->finalize_cookies();

        $response->_finalized_headers = true;
    }

    private function finalize_body() {
        $response = $this->response;

        echo $response->body;

        $response->_finalized_body = true;
    }

    private function finalize_cookies() {
        $response = $this->response;

        foreach ($response->cookies as $c) {
            setcookie(
                $c['name'],
                $c['value'],
                $c['expire'],
                $c['path'],
                $c['domain'],
                $c['secure'],
                $c['httponly']
            );
        }

        $response->_finalized_cookies = true;
    }


    static public function path_to() {
        // app_root should already be suffixed with /
        $home = DF_Web_Environment::singleton()->app_root;

        if (!preg_match('#/$#', $home)) {
            $home .= "/";
        }
        
        $args = func_get_args();
        if ($args) {
            $home .= join('/', $args);

            if (is_dir($home)) {
                $home .= '/';
            }
        }
        
        return $home;
    }

}

DF_Web::$LOGGER = DF_Web_Logger::logger('DF_Web');

require_once 'DF/Util/Arrays.php';

