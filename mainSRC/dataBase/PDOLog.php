<?php
namespace mainSRC\dataBase;
use DateTime;
class PDOLog
{
    private $path ;
    public function __construct()
    {
        $this->path = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR.'dataBase'. DIRECTORY_SEPARATOR;
        if(!is_dir($this->path)) {
           // mkdir($this->path, 0750, true);
        }
    }

    public function write($message, $fileSalt)
    {
        $date = new DateTime();
        $log  = $this->path . $date->format('Y-m-d') . "-" . md5($date->format('Y-m-d') . $fileSalt) . ".txt";
        if (is_dir($this->path)) {
            if (!file_exists($log)) {
                $fh = fopen($log, 'a+') or die("Fatal Error !");
                $logcontent = "Time : " . $date->format('H:i:s') . "\r\n" . $message . "\r\n";
                fwrite($fh, $logcontent);
                fclose($fh);
            } else {
                $this->edit($log, $date, $message);
            }
        } else {
            if (mkdir($this->path, 0770) === true) {
                $this->write($message, $fileSalt);
            }
        }
    }
    private function edit($log, DateTime $date, $message)
    {
        $logcontent = "Time : " . $date->format('H:i:s') . "\r\n" . $message . "\r\n\r\n";
        $logcontent = $logcontent . file_get_contents($log);
        file_put_contents($log, $logcontent);
    }
}