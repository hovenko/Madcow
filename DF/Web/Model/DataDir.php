<?php

define('DEFAULT_DATA_DIR', DF_Web::path_to('data'));

class DF_Web_Model_DataDir extends DF_Web_Model {
    private $_cache = NULL;

    protected $config = array(
        'dataDir'       => DEFAULT_DATA_DIR,
    );

    public function getDataDir() {
        return $this->config['dataDir'];
    }

    public function getFileContent($name) {
        $dir = $this->getDataDir();
        $content = file_get_contents($dir.$name);

        return $content;
    }

    public function putFileContent($name, $content) {
        $dir = $this->getDataDir();
        file_put_contents($dir.$file, $content);
    }

}
