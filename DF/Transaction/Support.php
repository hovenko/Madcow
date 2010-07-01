<?php


interface DF_Transaction_Support {

    /**
     * @return DF_Transactional
     */
    public function begin_transaction();
}
