<?php
/**
 * @package DF_URL
 */


require_once 'DF/URI/I.php';


/**
 * 
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
interface DF_URL_I extends DF_URI_I {
    public function get_authority();
    public function get_path();
    public function get_user();
    public function get_host();
    public function get_port();
}
