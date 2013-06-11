<?php

class Folder extends File {
    public $favicon = false;

    public function __construct($path) {
        parent::__construct($path);
        if(file_exists($path . '/favicon.ico')) {
            $this->favicon = basename($path) . '/favicon.ico';
        }
    }
}
