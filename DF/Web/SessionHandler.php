<?php
/**
 * This file contains a class for handling sessions when using SSI.
 *
 * @package DF_Web
 */


/**
 * This class handles sessions.
 * It is very much same as the regular PHP session handles, but it allows
 * multiple threads/processed to open the same session files.
 * 
 * This is not thread safe, but is needed for Smarty plugin function ssi
 * to work. The first process to open the session object is the one who gets
 * access to update the session as well.
 * 
 * @author Knut-Olav Hoven <knutolav@gmail.com>
 */
class DF_Web_SessionHandler {
    
    /**
     * Holds the path to the sessions data.
     *
     * @var string
     */
    private $save_path  = NULL;
    
    
    /**
     * Constructor.
     * 
     * @return DF_Web_SessionHandler
     */
    public function __construct() {
        
    }
    
    
    /**
     * This method returns the file of the session data storage for a given
     * session ID.
     *
     * @param string $session_id
     * @return string
     */
    private function get_file($session_id) {
        $file   = "$this->save_path/sess_$session_id";
        return $file;
    }
    
    
    /**
     * This method sets the save path of session data to prepare the session
     * handling.
     *
     * @param string $save_path
     * @param string $session_name
     * @return boolean
     */
    public function open($save_path, $session_name) {
        $this->save_path = $save_path;
        
        return TRUE;
    }
    
    
    /**
     * This method does nothing.
     *
     * @return boolean
     */
    public function close() {
        return TRUE;
    }
    
    
    /**
     * This method reads the session data from the session data storage.
     *
     * @param string $session_id
     * @return string
     */
    public function read($session_id) {
        $file   = $this->get_file($session_id);
        
        if (file_exists($file)) {
            return (string) file_get_contents($file);
        }
        
        return '';
    }
    
    
    /**
     * This method writes session data to the session data storage.
     *
     * @param string $session_id
     * @param string $session_data
     * @return integer number of bytes written
     */
    public function write($session_id, $session_data) {
        $file   = $this->get_file($session_id);
        
        if ($fp = @fopen($file, "w")) {
            $return = fwrite($fp, $session_data);
            fclose($fp);
            return $return;
        }
        else {
            return FALSE;
        }
    }
    
    
    /**
     * This method destroys a session. It deletes the session data storage file.
     *
     * @param string $session_id
     * @return boolean
     */
    public function destroy($session_id) {
        $file   = $this->get_file($session_id);
        return @unlink($file);
    }
    
    
    /**
     * This method destroys all sessions that have expired.
     * All used sessions gets updated with modified time. Session files that
     * have not been modified within $max_expire_time will be deleted.
     *
     * @param integer $max_expire_time
     * @return boolean allways true
     */
    public function gc($max_expire_time) {
        foreach (glob("$this->save_path/sess_*") as $filename) {
            if (filemtime($filename) + $max_expire_time < time()) {
                @unlink($filename);
            }
        }
        
        return TRUE;
    }
}

