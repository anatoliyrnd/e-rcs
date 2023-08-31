<?php
namespace mainSRC\calls;
use mainSRC\main;
const log_path="calls";
class newCall extends main
{
    private $status_text;
    private $repair_index;
    private $inputData;
    private $add_call_allow;//разрешено добавлять заявки
    public function __construct($data)
    {
        parent::__construct();
$this->inputData=$data;
$this->repair_time_index=(int)$data['repair_time'];
        $this->checkSession();
        $this->add_call_allow=$this->getUserPermission()[5];
    }
    public function newCallAdd(){
        if(!$this->checkDataIntegrity())$this->echoJSON(array("status"=>'error','message'=>$this->status_text));
        if(!$this->checkUserAlow())$this->echoJSON(array("status"=>'error','message'=>$this->status_text));
        $this->inputData['repair_time'] = $this->repairTimeUnix();// по индексу получим метку времени
$details=$this->magicLower($this->inputData['details']);
$this->inputData['details']=$details; //уберем капслок
        $this->inputData['user_name']=$this->getUserName();
        $add_call=new addCall();
        $add_call->setAllowSaveCall(true);
        $result=$add_call->addCall($this->inputData);
       if ($result){
           $message['status']="ok";
           $message['message']=$result;
       }else{
           $message['status']="error";
           $message['message']='Произошла ошибка';
       }
       $this->echoJSON($message);

    }
    private function  checkDataIntegrity(){
        $data_key = ["city", "street", "home", "object", "fullAdress", "group", "request", "repair_time", "department", "details"];
        foreach ($data_key as $value) {
            if (!isset($this->inputData[$value])) {
                $log = " error new call  no " . $value . " -".print_r($this->inputData,true);
                $this->logSave($log, "addCall",log_path);
                $this->status_text="Нарушена целостность данных ";
               return false;
            }
        }
        return true;

    }
    private function checkUserAlow(){
        if($this->add_call_allow)return true;
        $log = "deny user add call ".$this->getUserId();
        $this->logSave($log, "addCall",log_path);
        $this->status_text="Не достаточно прав для данного действия ";
        return false;
    }

}