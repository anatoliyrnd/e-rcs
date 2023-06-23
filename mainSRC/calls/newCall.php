<?php
namespace mainSRC\calls;
use mainSRC\main;
const log_path="calls";
class addCall extends main
{
    private $status_text;
    private $data;
    public function __construct()
    {
        parent::__construct();

    }
    private function  checkDataIntegrity(){
        $data_key = ["city", "street", "home", "object", "fullAdress", "group", "request", "repair_time", "department", "details"];
        foreach ($data_key as $key => $value) {
            if (!isset($this->data[$value])) {
                $log = " error new call  no " . $value . " -".print_r($this->data,true);
                $this->logSave($log, "addCall",log_path);
                $this->status_text="Нарушена целостность данных ";
               return false;
            }
        }
        return true;

    }

}