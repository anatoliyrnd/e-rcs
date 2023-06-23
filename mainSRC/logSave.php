<?php

namespace mainSRC;

class logSave
{

    private $path;
    private $root_path;

    public function logSave($text, $type = "default_log", $path = null)
    {
        if ($path) {
            $this->path = $this->root_path . $path . DIRECTORY_SEPARATOR;
        } else {
            $this->path = $this->root_path . 'default' . DIRECTORY_SEPARATOR;
            if (!is_dir($this->path)) {
                mkdir($this->path, 0750, true);
            }
        }
        $text = date('Y-m-d H:m:s') . " - " . $_SERVER['REMOTE_ADDR'] . " - " . $text;
        $file = date('Y-m-d') . $type;
        file_put_contents($this->path . $file . ".txt", $text . PHP_EOL, FILE_APPEND);
    }
}