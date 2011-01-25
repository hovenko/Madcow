<?php


class DF_Web_Routing_ActionChain {

    /**
     * @var DF_Web_Routing_Action_Chained
     */
    protected $endpoint = NULL;
    
    protected $chain    = array();


    /**
     * 
     * @param DF_Web_Routing_Action_Chained $endpoint
     */
    public function __construct($endpoint) {
        if (!$endpoint instanceof DF_Web_Routing_Action_Chained) {
            throw new DF_Error_InvalidArgumentException("endpoint", $endpoint, DF_Web_Routing_Action_Chained);
        }
        if (!$endpoint->is_endpoint()) {
            throw new DF_Web_Exception("Not an endpoint: $endpoint");
        }
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


    public function get_path_match() {
        $actions = $this->get_chain_list();
        $final = NULL;
        foreach ($actions as $action) {
            $path = $action->get_path_match();
            if (NULL === $final) {
                $final = $path;
            }
            else {
                $final = $final->append_path($path);
            }
        }

        return $final;
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
