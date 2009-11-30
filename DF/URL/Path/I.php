<?php
/**
 * @package DF_URL
 */



/**
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
interface DF_URL_Path_I {
    public function is_absolute();
    public function has_trailing_slash();
}
