<?php
namespace includes;
class readCalls extends mainControl {
    /**
     * @main class
     */

    protected $type_calls;
    protected $query_calls;



    public function __constructor(){

    }
    public function echoOpenCalls(){
        $read_all=$this->getUserPermission();
        $this->type_calls="open";
        ($read_all[0])?$this->query_calls = "SELECT *  from lift_calls  WHERE (call_status = 0)  order by call_id desc;":$this->query_calls = "SELECT *  from lift_calls  WHERE (call_status = 0 AND call_staff = $this->user_id)  order by call_id desc;";
        $this->echoCalls();
    }
    public function echoCloseCals($date_close=0){
            $this->type_calls="close";
        $read_all=$this->getUserPermission();
        ($date_close)?:$date_close=time() - 86400;
        ($read_all[0])?$all_viewer = "":$all_viewer=" AND (call_staff = $this->user_id)";
        $this->query_calls ="SELECT *  from lift_calls  WHERE (call_status = 1) AND (call_date2 >=$date_close) $all_viewer  order by call_id desc;";
        $this->echoCalls();
    }
    private function echoCalls(){

        $calls_DB = $this->DB->query($this->query_calls);
        $data=[];
        foreach ($calls_DB as $key => $value) {
            //date - дата открытия
            $call = [
                'open_name' => $value['call_first_name'],
                'date' => date("d.m.Y@H:i", $value['call_date']),

                'close_name'=>$value['call_last_name'],
                'close_date'=>date("d.m.Y@H:i", $value['call_date2']),
                'id' => $value['call_id'],
                'adress' => $value['call_adres'],
                'repair_time' => date("d.m.Y@H:i", $value['expected_repair_time']),
                'details' => $value['call_details'],
                'staff_id' => $value['call_staff'],
                'department_id' => $value['call_department'],
                'group_id' => $value['call_group'],
                'request_id' => $value['call_request'],
            ];
            $call_staff_status_arr=$this->getKeyStaffStatusType();
            $staff_status_type=$call_staff_status_arr[$value['call_staff_status']];
            if ($value['call_staff_date']) {
                $call['staff_date'] = date("d.m.Y@H:i", $value['call_staff_date'])." - ".$staff_status_type;
            } else {
                $call['staff_date'] = "Не уведомлен";
            }

            if ($this->type_calls == "open") {
                $call['type']="open";
                $call['staff_status'] = (bool) $value['call_staff_status'];
            }
            if ($this->type_calls == "close") {
                $call['type']="close";
                $call['solution']=$value['call_solution'];

            }
            if ($this->user_id == $value['call_staff']) {
                $call['closureallowed'] = 1;
            }
            $queryDepartment = "SELECT type_name FROM lift_types WHERE type_id=" . $value['call_department']." LIMIT 1"; // название отдела
            $queryGroup      = "SELECT type_name FROM lift_types WHERE type_id=" . $value['call_group']." LIMIT 1";
            $queryRequest    = "SELECT type_name FROM lift_types WHERE type_id=" . $value['call_request']." LIMIT 1"; // название уровня заявки
            $queryNotes      = "SELECT * from lift_notes WHERE (note_relation =" . $value['call_id'] . ");";
            if ($value['call_staff']) {
                $queryUser     = "SELECT user_name FROM lift_users WHERE user_id=" . $value['call_staff']; //получим имя ответсвенного по его id
                $userName      = $this->getDB()->single($queryUser);
                $call['staff'] = $userName;
            } else {
                $call['staff'] = 'Не назначен';
            }
            $call['group']      = $this->getDB()->single($queryGroup);
            $call['department'] = $this->getDB()->single($queryDepartment);

            $call['request']    = $this->getDB()->single($queryRequest);
            if ($this->type_calls=="close"){
                $call['full_history']=$value['call_fullhistory'];
            }
            $noteList           = $this->getDB()->query($queryNotes);
            $num = 0;
            foreach ($noteList as $key => $note_query) {
                $query_user_note = "SELECT user_name FROM lift_users WHERE user_id=" . $note_query['note_post_user']; //получим имя ответсвенного по его id
                $user_name_note  = $this->getDB()->single($query_user_note);
                $note            = ['user' => $user_name_note, 'body' => $note_query['note_body'], 'date' =>
                    date("d.m.Y H:i", ($note_query['note_post_date'])),'type'=>$note_query['note_type'],"id"=>$note_query['note_id']];
                $num++;
                $call['note'][$num] = $note;
            }
            $call['note_num'] = $num;
            if ($num){$call['other']="Есть заметки ($num)";}
            array_push($data, $call);
        }
        $this->echoJSON($data);
    }
   
}