<?php

namespace mainSRC\calls;
const query_call_close = "UPDATE lift_calls SET call_status=1, call_date2=:date_now, call_solution=:solution, call_last_name=:user_name WHERE call_id = :call_id";
const log_name="closure_call";
const log_path="calls";


use mainSRC\dataBase\PDODB;
use mainSRC\logSave;
use mainSRC\main;

class closureCall {
    protected $call_id;
    protected $staff_id;
    protected $solution;
    protected $closed_user_name;
    protected bool $approval_closure;
    protected PDODB $DB;

    private $log;

    public function __construct()
    {
        $this->DB=PDODB::getInstance();
        $this->log=new logSave();
    }
    public function closureCall()
    {
        if (empty($this->approval_closure)) {
            $this->log->logSave("не установлено разрешение на закрите $this->approval_closure",log_name,log_path);
            return false;
        }
        if (empty($this->call_id)) {
            $this->log->logSave("не установлено call_id $this->call_id",log_name,log_path);
            return false;
        }
        if (empty($this->closed_user_name)){
            $this->log->logSave("не установлено  имя закрывающего заявку $this->closed_user_name",log_name,log_path);
            return false;
        }
        if (empty($this->solution)){
            $this->log->logSave("не установлено решение по заявке $this->solution",log_name,log_path);
            return false;
        }
        $querydata = array('date_now' => strtotime(date('Y-m-d H:i:s ')), 'solution' => $this->solution, 'user_name' => $this->closed_user_name, 'call_id' => $this->call_id);
        $result = $this->DB->query(query_call_close, $querydata);
        if (!$result) {
            $this->log->logSave("$result " . print_r($querydata, true), log_name, log_path);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return false|void
     */
    public function addNoteArchive(){
        if (!$this->checkCallClosed()){
            $this->log->logSave("попытка переноса в архив заметок по не закрытой заявке $this->call_id",log_name,log_path);
            return false;
        }
        $query_note="SELECT * FROM lift_notes WHERE note_relation=:id";
        $note=json_encode($this->DB->query($query_note,array("id"=>$this->call_id)));
        $query_note_history="UPDATE lift_calls SET call_full_note_history=:note WHERE call_id=:id";
        return $this->DB->query($query_note_history,array("id"=>$this->call_id,"note"=>$note));

    }

    /**
     * @return bool
     */
    public function addHistoryArchive(){

        if (!$this->checkCallClosed()){
            $this->log->logSave("попытка переноса в архив истории по не закрытой заявке $this->call_id",log_name,log_path);
            return false;
        }

        //если все ок и заявка закрыта , то перенесем все историю по заявке в ячейку в таблице
        $query     = "SELECT `history_date`,`history_info` FROM `lift_history` WHERE `call_id`=:id";
        $sthistory = $this->DB->query($query,array("id"=>$this->call_id));
        $num       = 0;
        $text      = '';
        foreach ($sthistory as $value) {
            $datehistory = date("d.m.Y@H:i", $value['history_date']);
            $num++;
            $text .= "Дата изменений:$datehistory -" . $value['history_info'] . "<hr>";
        }

        if ($num) {
            $query  = "UPDATE lift_calls SET call_fullhistory=:text WHERE call_id=:id ";
            $update = $this->DB->query($query, array("text" => $text,"id"=>$this->call_id));
            if ($update) {
                return true;

            } else {
                $log = " error sql history update  $update";
                $this->log->logSave($log,log_name,log_path);
                return false;
            }

        } else {
            return true;
        }

    }

    /**
     * @param int $call_id
     */
    public function setCallId(int $call_id)
    {
        $this->call_id = $call_id;
    }

    /**
     * @param bool $approval_closure
     */
    public function setApprovalClosure(bool $approval_closure)
    {
        $this->approval_closure = $approval_closure;
    }

    public function checkStaff($staff_id){
        $staff_id=(int)$staff_id;
        $staff=(int)$this->DB->single("SELECT call_staff FROM lift_calls WHERE call_id=:id",array("id"=>$this->call_id));
        return $staff===$staff_id;
    }

    /**
     * @return bool
     */
    public function checkCallClosed(){
        if(empty($this->call_id)){return true;}
        return (boolean)$this->DB->single("SELECT call_status FROM lift_calls WHERE call_id=:id",array("id"=>$this->call_id));
    }
    public function callClosedInfo(){
        if (empty($this->call_id)){return "Не передан Id заявки";}
        $close_info=$this->DB->row("SELECT call_solution, call_date2, call_last_name, call_status FROM lift_calls WHERE call_id=:id",array("id"=>$this->call_id)) ;
        $date                = date("d-m-Y H:m", $close_info['call_date2']);
        return "Заявка с № $this->call_id была закрыта  $date  " . $close_info['call_last_name'] . " решение - " . $close_info['call_solution'];
    }

    /**
     * @param string $solution
     */
    public function setSolution($solution)
    {
        $this->solution = $solution;
    }

    /**
     * @param string $closed_user_name
     */
    public function setClosedUserName($closed_user_name)
    {
        $this->closed_user_name = $closed_user_name;
    }

    /**
     * @return mixed
     */
    public function getCallId()
    {
        return $this->call_id;
    }

    /**
     * @return mixed
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * @return mixed
     */
    public function getClosedUserName()
    {
        return $this->closed_user_name;
    }





}