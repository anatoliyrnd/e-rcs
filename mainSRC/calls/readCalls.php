<?php

namespace mainSRC\calls;
use mainSRC\dataBase\PDODB;
use mainSRC\main;
class readCalls extends main
{
    protected $query_calls;
    protected $type_calls;
    private $limit_user_id;
    /**
     * @var boolean
     */
    private $read_all;
protected $return_data_calss;
    public function __construct()
    {
        parent::__construct();
        $this->checkSession();
        $this->read_all = $this->getUserPermission()[1];
        $this->limit_user_id = $this->getUserPermission()[0];
    }

    private function readQueryCalls()
    {

        $calls_DB = $this->DB->query($this->query_calls);
        $data = [];
        foreach ($calls_DB as $key => $value) {
            //date - дата открытия
            $call = [
                'open_name' => $value['call_first_name'],
                'date' => date("d.m.Y@H:i", $value['call_date']),
                'close_name' => $value['call_last_name'],
                'close_date' => date("d.m.Y@H:i", $value['call_date2']),
                'id' => $value['call_id'],
                'adress' => $value['call_adres'],
                'repair_time' => date("d.m.Y@H:i", $value['expected_repair_time']),
                'details' => $value['call_details'],
                'staff_id' => $value['call_staff'],
                'department_id' => $value['call_department'],
                'group_id' => $value['call_group'],
                'request_id' => $value['call_request'],
            ];

            $staff_status_type = $this->key_staff_status_type[$value['call_staff_status']??0];
            if ($value['call_staff_date']) {
                $call['staff_date'] = date("d.m.Y@H:i", $value['call_staff_date']) . " - " . $staff_status_type;
            } else {
                $call['staff_date'] = "Не уведомлен";
            }

            if ($this->type_calls == "open") {
                $call['type'] = "open";
                $call['staff_status'] = (bool)$value['call_staff_status']??false;
            }
            if ($this->type_calls == "close") {
                $call['type'] = "close";
                $call['solution'] = $value['call_solution'];

            }
            if ($this->limit_user_id == $value['call_staff']) {
                $call['closureallowed'] = 1;
            }
            $queryDepartment = "SELECT type_name FROM lift_types WHERE type_id=" . $value['call_department'] . " LIMIT 1"; // название отдела
            $queryGroup = "SELECT type_name FROM lift_types WHERE type_id=" . $value['call_group'] . " LIMIT 1";
            $queryRequest = "SELECT type_name FROM lift_types WHERE type_id=" . $value['call_request'] . " LIMIT 1"; // название уровня заявки
            $queryNotes = "SELECT * from lift_notes WHERE (note_relation =" . $value['call_id'] . ");";
            if ($value['call_staff']) {
                $queryUser = "SELECT user_name FROM lift_users WHERE user_id=" . $value['call_staff']; //получим имя ответсвенного по его id
                $userName = $this->DB->single($queryUser);
                $call['staff'] = $userName;
            } else {
                $call['staff'] = 'Не назначен';
            }
            $call['group'] = $this->DB->single($queryGroup);
            $call['department'] = $this->DB->single($queryDepartment);

            $call['request'] = $this->DB->single($queryRequest);
            if ($this->type_calls == "close") {
                $call['full_history'] = $value['call_fullhistory'];
            }
            $noteList = $this->DB->query($queryNotes);
            $num = 0;
            foreach ($noteList as $key => $note_query) {
                $query_user_note = "SELECT user_name FROM lift_users WHERE user_id=" . $note_query['note_post_user']; //получим имя ответсвенного по его id
                $user_name_note = $this->DB->single($query_user_note);
                $note = ['user' => $user_name_note, 'body' => $note_query['note_body'], 'date' =>
                    date("d.m.Y H:i", ($note_query['note_post_date'])), 'type' => $note_query['note_type'], "id" => $note_query['note_id']];
                $num++;
                $call['note'][$num] = $note;
            }
            $call['note_num'] = $num;
            if ($num) {
                $call['other'] = "Есть заметки ($num)";
            }
            array_push($data, $call);
        }
        $this->return_data_calss= $data;
        return true;
    }

    private function openCalls()
    {
        ($this->read_all) ? $this->query_calls = "SELECT *  from lift_calls  WHERE (call_status = 0)  order by call_id desc;" : $this->query_calss = "SELECT *  from lift_calls  WHERE (call_status = 0 AND call_staff = $this->limit_user_id)  order by call_id desc;";
    }

    private function closeCals($date_close)
    {
        ($this->read_all) ? $all_viewer = "" : $all_viewer = " AND (call_staff = $this->limit_user_id)";
        $this->query_calls = "SELECT *  from lift_calls  WHERE (call_status = 1) AND (call_date2 >=$date_close) $all_viewer  order by call_date2 desc;";

    }

    public function getCalls($action, $period_close_calls = null)
    {
        $date_close = time() - 86400;
        if (isset($period_close_calls)) {
            if ((int)$period_close_calls >= 604800) {
                $date_close = time() - 604800;
            } else {
                $date_close = time() - (int)$period_close_calls;
            }
        }

        if ($action === 'open') {
            $this->type_calls = "open";
            $this->openCalls();
        } elseif ($action === 'close') {
            $this->type_calls = "close";
            $this->closeCals($date_close);
        } else {
            $this->echoJSON(array('status' => 'error', 'message' => 'Не вернрый запрос по заявкам'));
            $this->logSave->logSave("error action read calls - " . str_replace(['.', '?', '<', '>'], '', trim($action)), 'read_calls', 'calls');
        }
        return $this->readQueryCalls()?$this->return_data_calss: false;

    }

}