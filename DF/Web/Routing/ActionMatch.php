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
        if (NULL === $score) {
            throw new InvalidArgumentException("Not set: score");
        }

        if (!is_int($score)) {
            throw new InvalidArgumentException("Not an integer: score");
        }

        if (NULL === $path) {
            throw new InvalidArgumentException("Not set: path");
        }

        if (!$path instanceof DF_Web_Path) {
            throw new InvalidArgumentException("Not of type DF_Web_Path: path");
        }

        if (NULL === $actions) {
            throw new InvalidArgumentException("Not set: actions");
        }

        if (!is_array($actions)) {
            throw new InvalidArgumentException("Not an array: actions");
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

        if ($this->sccore < $other->get_score()) {
            return true;
        }

        return false;
    }


    public function __toString() {
        $path = $this->path;
        $score = $this->score;
        $str = "$path (score: $score)";
        return $str;
    }
}
