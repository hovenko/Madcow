<?php


interface DF_Transactional {
    public function commit();
    public function rollback();
    public function in_transaction();
}
