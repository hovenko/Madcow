<?php
/**
 * This file holds a file for holding request parameters.
 *
 * @package DF_Web
 */


require_once 'DF/Web/HTTP/Request/QueryParser.php';


/**
 * This class holds the request variables and provides some nice methods
 * to access the reuqest parameters.
 * 
 * This is a work in progress. Only the properties with getter and setters
 * is currently in use. Add new getters and setters (see set_type and get_type
 * for examples) when using new functionality of this class.
 */
class DF_Web_HTTP_Request {
    // to indicate if processing of the request params is finished
    private $_finalized = false;


    /**
     * Returns a list of the arguments passed to the site.
     *
     * For example the path "/spania/bolig/salg", will become this array:
     *  array("spania", "bolig", salg")
     *
     * @var array
     */
    public $arguments          = array();


    /**
     * This keeps a map of all the parameters sent in the body of a POST
     * request. This contains the same as the $_POST array.
     *
     * @var array
     */
    public $body_parameters    = array();


    /**
     * This keeps a map of all the query parameters passed on the URL of the
     * request. This contains the same as the $_GET array.
     * 
     * @var array
     */
    public $query_parameters   = array();


    /**
     * This is a map of all the request cookies sent in the header of the
     * HTTP request. This contains the same as the $_COOKIES array.
     *
     * @var array
     */
    public $cookies            = array();


    /**
     * This is a list of all the request headers sent in the
     * HTTP request.
     *
     * @var array
     */
    public $headers            = array();

    
    /**
     * This is a map of GET and POST parameters.
     * This contains the same as the $_POST and $_GET arrays together.
     * POST body parameters have higher precedence.
     *
     * @var array
     */
    public $parameters         = array();

    
    /**
     * This is a flag that is set to true if the connection is secure, using
     * HTTPS. False otherwise.
     *
     * @var boolean
     */
    public $secure             = FALSE;


    /**
     * This is a map of uploaded files.
     * This contains the same as the $_FILES array.
     *
     * XXX maybe later we create a class representing an uploaded file
     *
     * @var array
     */
    public $uploads            = array();
    
    
    /**
     * The real address of the client. This is either the IP address of the
     * client or the address specified in the X-Forwarded-For header, if any.
     *
     * @var string
     */
    public $address            = NULL;
    

    /**
     * The method used to make the request.
     * Normally this is either "GET" or "POST".
     *
     * @var string
     */
    public $method              = NULL;


    public $hostname            = NULL;


    private $base_path          = NULL;


    /**
     * Holds the path to the current request, without the base path.
     * This is without any schema, hostname and port.
     */
    public $path                = NULL;


    private $port               = NULL;


    public $url                 = NULL;


    
    /**
     * Constructor.
     *
     * Initializes the request parameters for other modules to use.
     *
     * The incoming parameter should be an associative array containing
     * keys named after the private instance variable names above.
     * The values of each key is set directly to the variable.
     *
     * @param array $data
     * @return DF_Web_HTTP_Request
     */
    function __construct($data = array()) {
        foreach ($data as $key => $value)  {
            $this->$key = $value;
        }
    }


    /**
     * After this method is called, no data should be changed
     * (although, this is not enforced).
     */
    function finalize() {
        $this->parameters = array_merge(
            $this->query_parameters,
            $this->body_parameters
        );

        $this->url = $this->assemble_url();

        $this->_finalized = true;
    }


    private function assemble_url() {
        $secure     = $this->is_secure();
        $scheme     = $secure ? "https" : "http";
        $hostname   = $this->get_hostname();
        $port       = $this->get_port();

        $port_str   = "";
        if (($port != 80 && !$secure) || ($port != 443 && $secure)) {
            $port_str = ":$port";
        }

        $base_path  = $this->get_base_path();
        $path       = $this->get_path();
        
        if ($base_path != '/') {
            $path = $base_path.$path;
        }

        $query_str = $this->get_query_string();
        if ($query_str) {
            $query_str = "?$query_str";
        }
        
        $url = "$scheme://$hostname$port_str$path$query_str";
        return $url;
    }


    public function get_query_string() {
        $query_str = "";
        $query_arr = array();
        if ($query = $this->get_query_parameters()) {
            foreach ($query as $key => $val) {
                $query_arr[] = sprintf('%s=%s',
                    urlencode($key),
                    urlencode($val)
                );
            }
            if ($query_arr) {
                $query_str = join('&', $query_arr);
            }
        }

        return $query_str;
    }
    

    /**
     * Returns the request method.
     *
     * @return string
     */
    function get_method() {
        return $this->method;
    }
    
    
    /**
     * Sets the request method.
     *
     * @param string $type
     */
    function set_method($method) {
        $this->method = $method;
    }
    
    
    /**
     * Returns the real address of the client.
     *
     * @return string
     */
    function get_address() {
        return $this->address;
    }
    
    
    /**
     * Sets the address of the client.
     *
     * @param string $address
     */
    function set_address($address) {
        $this->address = $address;
    }


    public function get_hostname() {
        return $this->hostname;
    }


    public function set_hostname($hostname) {
        $this->hostname = $hostname;
    }


    function get_port() {
        return $this->port;
    }

    
    function set_port($port) {
        $this->port = $port;
    }

    
    function set_secure($secure) {
        $this->secure = $secure ? TRUE : FALSE;
    }

    
    function is_secure() {
        return $this->secure ? TRUE : FALSE;
    }


    function set_arguments($arguments) {
        $this->arguments = $arguments;
    }


    function get_arguments() {
        return $this->arguments;
    }
    

    function set_body_parameters($body_params) {
        $this->body_parameters = $body_params;
    }


    function get_body_parameters() {
        return $this->body_parameters;
    }

    
    function get_body_parameters_parsed() {
        if (isset($this->body_parameters_parsed)) {
            return $this->body_parameters_parsed;
        }

        $parsed = DF_Web_HTTP_Request_QueryParser::parse_query_params($this->get_body_parameters());
        $this->body_parameters_parsed = $parsed;

        return $parsed;
    }


    function set_cookies($cookies) {
        $this->cookies = $cookies;
    }


    function get_cookies() {
        return $this->cookies;
    }


    function set_query_parameters($params) {
        $this->query_parameters = $params;
    }

    
    function get_query_parameters() {
        return $this->query_parameters;
    }

    function get_query_parameters_parsed() {
        if (isset($this->query_parameters_parsed)) {
            return $this->query_parameters_parsed;
        }

        $parsed = DF_Web_HTTP_Request_QueryParser::parse_query_params($this->get_query_parameters());
        $this->query_parameters_parsed = $parsed;

        return $parsed;
    }

    function set_uploads($uploads) {
        $this->uploads = $uploads;
    }


    function get_uploads() {
        return $this->uploads;
    }


    public function set_path($path) {
        $this->path = $path;
    }


    function get_path() {
        return $this->path;
    }


    public function set_base_path($base_path) {
        $this->base_path = $base_path;
    }


    public function get_base_path() {
        return $this->base_path;
    }


    public function get_url() {
        return $this->url;
    }


    function is_finalized() {
        return $this->_finalized;
    }
}

