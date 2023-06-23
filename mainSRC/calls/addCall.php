<?php

namespace mainSRC\calls;

use mainSRC\dataBase\PDODB;
use mainSRC\logSave;
use mainSRC\telegram\telegram;

class addCall
{
    protected $DB;
    protected $log;
    private $staff_telegram;
    private bool $allow_save_call = false;

    public function __construct()
    {
        $this->DB = PDODB::getInstance();
        $this->log = new logSave();
    }

    public function addCall($data)
    {
        $staff = 0;
        if (!$this->allow_save_call) {
            $this->log->logSave("error save new call . Allow_save not set to true ", "saveCall", "calls");
            return false;
        }
        $query_add_call = '';
        $flag_allow_send_telegram = false;
        $date_call = strtotime('now');
        if (isset($data['staff'])) {
            // если выбрали ответсвенного
            $staff = (int)$data['staff'];
            $query_add_call = "call_staff=$staff,";
            if (isset($data['staff_status']) and ((bool)$data['staff_status'])) {
                //если его статус уведомлен
                $query_add_call .= "call_staff_status=1, call_staff_date=$date_call,";
            } else {
                $flag_allow_send_telegram = true;
            }
        }

        $call_department = (int)$data['department'];
        $user_name = $data['user_name'];
        $call_request = (int)$data['request'];
        $call_group = (int)$data['group'];
        $city = (int)$data['city'];
        $street = (int)$data['street'];
        $home = (int)$data['home'];
        $lift = (int)$data['object'];
        $repair_time_save = $data['repair_time'];
        $query_add_call .= "expected_repair_time=$repair_time_save, call_date=$date_call, call_status=0, call_solution=' ',call_first_name='$user_name', call_department=$call_department, call_request=$call_request, call_group=$call_group, call_adres=:address, call_details=:details, address_city=$city, address_street=$street, address_home=$home, address_lift=$lift";
        $query_add_call = "INSERT INTO lift_calls SET " . $query_add_call;
        $result_add = $this->DB->query($query_add_call, array('address' => $data['fullAdress'], "details" => $data['details']));
        if (!$result_add) return false;
        if ($flag_allow_send_telegram) {
            $call_id_insert = $this->DB->lastInsertId();
            $query_user = "SELECT user_telegram FROM lift_users WHERE user_id =$staff LIMIT 1";//получим имя ответсвенного по его id
            $this->staff_telegram = $this->DB->single($query_user);
            if (!$this->staff_telegram) return 'Заявка добавлена';
            $adjdate = date("d-m-Y H:i ");
            $call_request === 1 ? $alarm = "\xF0\x9F\x9A\xA8 \n" : $alarm = "\xF0\x9F\x8F\xA2\n";
            $telegram_message = "$adjdate Новая заявка! $alarm \n По адресу: \n " . $data['fullAdress'];
            $result_send_telegram = $this->notificationStaff($telegram_message, $call_id_insert);
            return ($result_send_telegram) ? "Заявка добтавлена. Сообщение ответственному отправлено" : "Заявка добавлена. При оповещении ответсвенного произошла ошибка!";


        }
    }

    public  function notificationStaff($message, $call_id)
    {
        $but = array(
            'inline_keyboard' => array(
                array(
                    array(
                        'text' => '<Подробнее>',
                        'callback_data' => '{"id":"' . $call_id . '","action":"more"}',
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
            $this->log->logSave($request, "addCallSentTelegram", "calls");
            return false;
        }
    }

    /**
     * @return bool
     */
    public function getAllowSaveCall()
    {
        return $this->allow_save_call;
    }

    /**
     * @param bool $allow_save_call
     */
    public function setAllowSaveCall(bool $allow_save_call)
    {
        $this->allow_save_call = $allow_save_call;
    }
}