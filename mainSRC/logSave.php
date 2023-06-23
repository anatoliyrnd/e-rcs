<?php

namespace mainSRC;

class logSave
{
 private $root_path;
public function __construct(){
    $this->root_path = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
}
    public function logSave($text, $type = "default_log", $path = null)
    {
        if ($path) {
            $path = $this->root_path . $path . DIRECTORY_SEPARATOR;
              } else {
            $path = $this->root_path . 'default' . DIRECTORY_SEPARATOR;

        }
        if (!is_dir($path)) {
        mkdir($path, 0750, true);
    }
        $text = date('Y-m-d H:m:s') . " - " . $_SERVER['REMOTE_ADDR'] . " - " . $text;
        $file = date('Y-m-d') . $type;
        file_put_contents($path . $file . ".txt", $text . PHP_EOL, FILE_APPEND);
    }
}