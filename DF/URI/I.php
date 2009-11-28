<?php
/**
 * @package DF_URI
 */



/**
 *
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
interface DF_URI_I {
    public function get_scheme();
    public function get_hierarchical();
    public function get_query();
    public function get_fragment();
}
