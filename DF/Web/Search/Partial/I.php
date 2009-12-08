<?php


require_once 'DF/Web/Search/I.php';


interface DF_Web_Search_Partial_I extends DF_Web_Search_I {
    public function getKey();
    public function getValue();
}
