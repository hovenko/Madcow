<?php


class DF_Web_Routing_ActionChain {

    protected $endpoint = NULL;
    protected $chain    = array();


    public function __construct($endpoint) {
        $this->endpoint = $endpoint;
        $this->chain[]  = $endpoint;
    }


    public function add_to_chain($action) {
        $this->chain[]  = $action;
    }


    public function get_endpoint() {
        return $this->endpoint;
    }


    public function get_chain_list() {
        return array_reverse($this->chain);
    }


    public function toDebugString() {
        $lines = array();
        $indent = "";
        foreach ($this->chain as $action) {
            $lines[] = $indent."\-> ".$action->get_private_path();
            $indent .= "  ";
        }

        return join("\n", $lines);
    }
}
