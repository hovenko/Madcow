<?php


interface DF_Transaction_PlatformManager {
    
    /**
     * Commits the given transaction.
     *
     * @param DF_Transactional $tx
     */
    public function commit($tx);

    
    /**
     * Rolls back the given transaction.
     *
     * @param DF_Transactional $tx
     */
    public function rollback($tx);


    /**
     * Returns the ccurrently active transaction
     * or creates a new one.
     *
     * @return DF_Transctional
     */
    public function getTransaction();
}
