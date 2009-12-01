<?php
/**
 * This file holds a file for holding request parameters.
 *
 * @package DF_Web
 */


/**
 * This class will hold the response data, such as status and .
 * 
 */
class DF_Web_HTTP_Response {
    public $_finalized_headers  = false;

    public $body                = '';
    public $content_encoding    = 'utf-8';
    public $content_type        = 'text/html';
    public $cookies             = array();

    public $headers             = array();

    public $status              = 200;
    public $location            = NULL;

    public $javascripts         = array();
    public $stylesheets         = array();

    public function redirect($location = NULL, $status = 302) {
        if ($location) {
            $this->location = $location;
            $this->status($status);
        }

        return $this->location;
    }


    public function content_type($type) {
        if (NULL !== $type) {
            $this->content_type = $type;
        }

        return $this->content_type;
    }


    public function status($status) {
        if (!is_int($status)) {
            throw new DF_Web_Exception("HTTP status must be an integer");
        }

        $this->status = $status;
    }


    public function body($body) {
        $this->body = $body;
    }


    public function add_javascript($name, $url) {
        $this->javascripts[] = array(
            'name'      => $name,
            'url'       => $url,
        );

        if ($url === NULL) {
            foreach ($this->javascripts as $script) {
                if ($script['name'] == $name) {
                    unset($this->javascripts[$name]);
                }
            }
        }
    }

    public function add_stylesheet($name, $url) {
        $this->stylesheets[] = array(
            'name'      => $name,
            'url'       => $url,
        );

        if ($url === NULL) {
            foreach ($this->stylesheets as $script) {
                if ($script['name'] == $name) {
                    unset($this->stylehseets[$name]);
                }
            }
        }
    }

    public function get_javascripts() {
        return $this->javascripts;
    }

    public function get_stylesheets() {
        return $this->stylesheets;
    }


    public function add_header($name, $value) {
        $this->headers[] = array(
            'name'  => $name,
            'value' => $value,
        );
    }


    public function add_cookie($name, $value = NULL, $expire = NULL,
            $path = NULL, $domain = NULL, $secure = NULL, $httponly = NULL) {
        $this->cookies[] = array(
            'name'          => $name,
            'value'         => $value,
            'expire'        => $expire,
            'path'          => $path,
            'domain'        => $domain,
            'secure'        => $secure,
            'httponly'      => $httponly,
        );
    }
}

