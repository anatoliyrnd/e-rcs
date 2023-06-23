<?php
namespace includes;
use telegram\telegram;

class addCalls{
private $DB;
private $mainControl;
private $save_call_data;

    public function __construct($data)
    {
      $this->mainControl=new mainControl()  ;
      $this->DB=$this->mainControl->getDB();
      $this->save_call_data=$data;
      $this->telegram=new telegram($this->mainControl->getTelegramToken());
      $this->staff_call_new=new staff_call_new_prepair();
    }
    public  function addNewCall(){
        $this->mainControl->checkUser();
        $telegram_send_flag=false;
        $staff_id=0;
        $call_id=0;
        $data_key = ["city", "street", "home", "object", "fullAdress", "group", "request", "repair_time", "department", "details"];
        foreach ($data_key as $key => $value) {
            if (!isset($this->save_call_data[$value])) {
                $log = " error new call  no " . $value . " -".print_r($this->save_call_data,true);
                $this->mainControl->logSave($log, "add_new_call");
                $response['status']  = 'error';
                $response['message'] = "Нарушена целостность данных  ";
                $this->mainControl->echoJSON($response);
            }
        }
        $date_call = strtotime(date('Y-m-d H:i:s '));

        if (isset($this->save_call_data['staff'])) {
            // если выбрали ответсвенного
            $staff_id          = (int) $this->save_call_data['staff'];
            $query_add_call = "call_staff=$staff_id,";
            if (isset($this->save_call_data['staff_status']) and ((bool) $this->save_call_data['staff_status'])) {
                //если его статус уведомлен
                $query_add_call .= "call_staff_status=1, call_staff_date=$date_call,";
            } else {
                $telegram_send_flag = true;
            }
        }
        $call_department = (int) $this->save_call_data['department'];
        //$user_name       = $queryuser['user_name'];
        $call_request    = (int) $this->save_call_data['request'];
        $call_group      = (int) $this->save_call_data['group'];
        $city            = (int) $this->save_call_data['city'];
        $street          = (int) $this->save_call_data['street'];
        $home            = (int) $this->save_call_data['home'];
        $lift            = (int) $this->save_call_data['object'];
       $repair_time_unix=$this->mainControl->getRepairTimeUnix();
        isset($repair_time_unix[$this->save_call_data['repair_time']])?$repair_time_save = $repair_time_unix[$this->save_call_data['repair_time']]:$repair_time_save=0;
        $bind_array       = array(
            "adress" => $this->save_call_data['fullAdress'],
            "details" => $this->mainControl>magicLower($this->save_call_data['details']),
        );
        $query_add_call .= "expected_repair_time=$repair_time_save, call_date=$date_call, call_status=0, call_solution=' ',call_first_name='$this->user_name', call_department=$call_department, call_request=$call_request, call_group=$call_group, call_adres=:adress, call_details=:details, address_city=$city, address_street=$street, address_home=$home, address_lift=$lift";
        $query_add_call  = "INSERT INTO lift_calls SET " . $query_add_call;
        $add_sql_result  = $this->DB->query($query_add_call, $bind_array);
        if ($add_sql_result) {
            $call_id = $this->DB->lastInsertId();

            if ($telegram_send_flag) {
                $message=$this->staff_call_new->staff_call_new($staff_id,$call_id);
                $result = json_decode($this->telegram->send_to_telegram($message[0],$message[1],$message[2]));
                ($result['ok'])?$telegram_send="Сообщение в телеграмм отправлено ":$telegram_send='';

            } //если есть ответсвенный, то  киним сообщение в телегу
            $response['status']  = 'ok';
            $response['message'] = "Заявка сохранена  $telegram_send";
        } else {
            $log = " error new call  sql add error " . $add_sql_result . " -" . $query_add_call;
            $this->mainControl->logSave($log, "save_add_new_call");
            $response['status']  = 'error';
            $response['message'] = "Фатальная ошибка - заявка не добавлена ";
        }
        return $response;
    }
}