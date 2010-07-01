<?php


require_once 'DF/Transaction/PlatformManager.php';


class DF_Transaction_DataSourceManager
        implements DF_Transaction_PlatformManager {


    protected $datasource = NULL;
    

    public function __construct($database) {
        if (!$database instanceof DF_Transaction_Support) {
            throw new DF_Error_InvalidArgumentException("database", $database, DF_Transaction_Support);
        }
        
        $this->datasource = $database;
    }
    
    
    /**
     * Commits the given transaction.
     *
     * @param DF_Transactional $tx
     */
    public function commit($tx) {
        $tx->commit();
    }

    
    /**
     * Rolls back the given transaction.
     *
     * @param DF_Transactional $tx
     */
    public function rollback($tx) {
        $tx->rollback();
    }


    /**
     * Returns the ccurrently active transaction
     * or creates a new one.
     *
     * @return DF_Transctional
     */
    public function getTransaction() {
        $tx = $this->datasource->begin_transaction();
        return $tx;
    }


    protected function getDataSource() {
        return $this->datasource;
    }
}

require_once 'DF/Error/InvalidArgumentException.php';

