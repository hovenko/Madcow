<?php
/**
 * @package DF_Web
 */


/**
 */
class DF_Web_Routing_ActionMatch {
    protected $actions  = NULL;
    protected $score    = NULL;
    protected $path     = NULL;


    /**
     * Constructor.
     * 
     * @param score $numargs
     * @param DF_Web_Path $path
     * @param array $actions
     * @return DF_Web_Routing_ActionMatch
     */
    public function __construct($score, $path, $actions) {
        if (!is_int($score)) {
            throw new DF_Error_InvalidArgumentException("score", $score, "integer");
        }

        if (!$path instanceof DF_Web_Path) {
            throw new DF_Error_InvalidArgumentException("path", $path, DF_Web_Path);
        }

        if (!is_array($actions)) {
            throw new DF_Error_InvalidArgumentException("actions", $actions, "array");
        }

        $this->score    = $score;
        $this->path     = $path;
        $this->actions  = $actions;
    }


    public function get_score() {
        return $this->score;
    }

    public function get_path() {
        return $this->path;
    }

    public function get_actions() {
        return $this->actions;
    }


    public function is_better_than($other) {
        if ($this->score < 0) {
            return false;
        }

        if ($other === NULL) {
            return true;
        }

        if ($this->score == 0) {
            return true;
        }

        if ($this->score < $other->get_score()) {
            return true;
        }

        return false;
    }


    public function __toString() {
        $path = $this->path;
        $score = $this->score;
        $actions = join(", ", $this->actions);
        $str = "$path (score: $score) (actions: $actions)";
        return $str;
    }
}

require_once 'DF/Error/InvalidArgumentException.php';

