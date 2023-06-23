<?php
namespace mainSRC\calls;

const log_file="editCall";

use mainSRC\main;
use mainSRC\telegram\telegram;

class editCall extends main {
  private $history;
  private $query;
  private $arrayQuery;
  private $flag_allow_telegram_send=false;
private $data_validation=false;
private $call_id=null;
private $staff_id=null;
private $staff_telegram=0;

    public function __construct()
    {
        parent::__construct();
        $this->checkSession();
    }
    public function editedCall(array $data){
        empty($data['call_id'])?$this->errorCallId():$this->call_id=(int)$data['call_id'];
        $this->userAllow();// проверим разрешения на редактирование для текущего пользоватля
        $this->checkingNotClosed();//проверим не закрыта ли заявка
        $this->history.= $this->getUserName() . " - внес(ла) следующие изменения:";
        (empty($data['repair_time_id']))?:$this->repairTimeSave($data['repair_time_id']);
        (empty($data['staff_status']))?:$this->staffStatus($data['staff_status']);
        (empty($data['details']))?:$this->details($data['details']);
        (empty($data['department_id']))?:$this->department($data['department_id']);
        (empty($data['request_id']))?:$this->request($data['request_id']);
        (empty($data['group_id']))?:$this->group($data['group_id']);
        (empty($data['staff_id']))?:$this->staff($data['staff_id']);
        $query = substr($this->query, 1);
        $queryedit   = "UPDATE `lift_calls` SET $query  WHERE call_id=$this->call_id";
        $result_edit = $this->DB->query($queryedit, $this->arrayQuery);
        if (!$result_edit) {
            $this->logSave("eroor edit call sql-$queryedit",log_file,log_path);
            $this->echoJSON(array('status'=>'error','message'=>'Ошибка при сохранении изменений'));
        }
            // запишим событие в журнал истории по заявке
            $history_date = strtotime(date('Y-m-d H:i:s '));
            $historysql   = $this->DB->query("INSERT INTO lift_history (history_date,history_info, call_id) VALUES( $history_date, :sethistory, $this->call_id );", array("sethistory" => $this->history));
            if ($historysql) {
                $history_save = " и история сохранена";
            } else {
                $this->logSave("history error save $historysql",log_file,log_path);
                $history_save = "и история НЕ сохранена";
            }

      $telegram_staff_send='';
        if($this->flag_allow_telegram_send){

        $result=$this->notificationStaff();
        if ($result) $telegram_staff_send=' Сообщение в телеграм ответсвенного отправлено';
        }
        $response['status']  = 'ok';
        $response['message'] = "Заявка с № $this->call_id изменена, $telegram_staff_send $history_save";
        $this->echoJSON($response);

    }
    public  function notificationStaff()

    {
        $info_call=$this->DB->row("SELECT call_date,call_adres,call_request FROM lift_calls WHERE call_id=$this->call_id");
        $call_date=date("d:m:y H:i",$info_call['call_date']);
        $info_call['call_request'] === 1 ? $alarm = "\xF0\x9F\x9A\xA8 \n" : $alarm = "\xF0\x9F\x8F\xA2\n";
        $message=$alarm."Дата создания: $call_date \n"."Адрес: ".$info_call['call_adres']."\n"."Вас назначили ответственным";
        $but = array(
            'inline_keyboard' => array(
                array(
                    array(
                        'text' => '<Подробнее>',
                        'callback_data' => '{"id":"' . $this->call_id . '","action":"more"}',
                    ),

                )
            ),
        );
        $replay = json_encode($but);
        $telegram = new telegram();
        $telegram->setChatId($this->staff_telegram);
        $telegram->setMessageTelegramSend($message);
        $request = $telegram->sendToTelegram($replay);
        $request_arr = json_decode($request, true);
        if ($request_arr['ok']) {
            return true;
        } else {
            $this->logSave($request, "editCallSentTelegram", "calls");
            return false;
        }
    }
    private function staff($id){
           $this->query .= ", call_staff=" . (int) $id;
            $user    = $this->DB->row("SELECT user_telegram,user_name FROM lift_users WHERE user_id=" . (int) $id); //получим имя ответсвенного по его id);
            $this->history .= " <b>Назначен ответственный</b> -" . $user['user_name'];
            $this->staff_telegram=$user['user_telegram'];
            if($this->staff_telegram)$this->flag_allow_telegram_send=true;
             $this->staff_id=(int)$id;
             }
    private function group($id){
        $this->query .= ", call_group=" . (int) $id;
        $name    = $this->DB->single("SELECT type_name FROM lift_types WHERE type_id=" . (int) $id);
        $this->history .= " <b>Группа</b> -" . $name;
    }
    private function request($id){
        $this->query .= ", call_request=" . (int) $id;
        $name    = $this->DB->single("SELECT type_name FROM lift_types WHERE type_id=" . (int) $id);
        $this->history .= " <b>Уровень </b>-" . $name;
    }
    private function department($id){
            $this->query .= ", call_department=" . (int) $id;
            $name    = $this->DB->single("SELECT type_name FROM lift_types WHERE type_id=" . (int) $id);
            $this->history .= "<b> Отдел</b> -" . $name;
    }
    private function details($value){
           $details               = $this->magicLower($value);
            $this->query.= ", call_details=:details";
            $this->arrayQuery['details'] = $details;
            $this->history .= " <b>Описание заявки</b> -" . $details;

    }
    private function staffStatus($status){

            $this->query .= ", call_staff_status=" . (int) $status;
            // изменим дату уведомления ответственного
            if ((bool) $status) {
                $this->history.= " Ответственный уведомлен. " . strtotime(date('Y-m-d H:i:s '));
                $this->query.= " , call_staff_date=" . strtotime(date('Y-m-d H:i:s '));
            } else {
                $this->history.= " Ответственный НЕ уведомлен. ";
                $this->query.= " , call_staff_date=0";
            }

    }
    private function repairTimeSave($index){
        $index_time       = (int) $index;
        $repair_time_unix = $this->repairTimeUnix($index_time);// получим значение метки времени юникс
        $repair_time=$this->repairTime($index_time);//получем значение срока предполагаемого ремонта
        $this->history .= " <b>Cрок предполагаемого ремонта</b> - " . $repair_time;
        $this->query.= ", expected_repair_time=$repair_time_unix";
    }
   private function checkingNotClosed(){
      $info_call= $this->DB->single("SELECT `call_status` FROM `lift_calls` WHERE`call_id`=$this->call_id");
      if($info_call){
          $user="id-".$this->getUserId().", name- ".$this->getUserName();
          $this->logSave("Попытка редактирования закрытой заявки $this->call_id, user - $user");
          $this->echoJSON(array('status'=>'error','message'=>'Невозможно отредактировать закрытую заявку'));
      }
   }
    private function userAllow(){
        if(!$this->getUserPermission()[2]){
            $user_id=$this->getUserId();
            $this->logSave("error user allow id ($user_id) ",log_file,log_path);
            $this->echoJSON(array('status'=>'error','message'=>'Недостаточно прав для редактирования этой заявки'));
        }
    }

}