<?php

namespace includes;

class logSave
{

    private $path;

    public function __construct($path=false)
    {
        if ($path) {
        $this->path = $path;
    }else{
            $this->path = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR;
            if (!is_dir($this->path)) {
                mkdir($this->path, 0750, true);
            }
        }
    }

    public function logSave($text, $type = "defaultlog")
    {
        $text = date('Y-m-d H:m:s') . " - " . $_SERVER['REMOTE_ADDR'] . " - " . $text;
        $file = date('Y-m-d') . $type;
        file_put_contents($this->path . $file . ".txt", $text . PHP_EOL, FILE_APPEND);
    }
}