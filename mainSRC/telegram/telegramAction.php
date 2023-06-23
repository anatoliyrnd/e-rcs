<?php

namespace mainSRC\telegram;
const query_check_userId = "SELECT  call_staff FROM lift_calls WHERE call_id=:Id LIMIT 1";
use mainSRC\logSave;
use mainSRC\main;
class telegramAction extends telegram {
    private $time;
    private $action_text;
    public function __construct(){
     parent::__construct();
        $this->mainLog=new logSave();
        $this->time=$this->getParameterConfig('waiting_time')/60;
    }
    public function actin_keyboard(){

    }

    protected function actionSend(){
        $call_id=$this->call_back_id;
        $staff_id = $this->DB->single(query_check_userId,array("Id"=>$call_id));
        if ($staff_id != $this->user_id) {
            $this->mainLog->logSave("staff - $staff_id != user" . $this->user_id, "action_call","telegram");
            $this->message_telegram_send='Произошла непредвиденная ошибка. Id пользователя не привязан к данной заявке';
            $this->sendToTelegram();
            exit();
        }
        $current_timestamp = strtotime("now");
        $query_search_telegram_action = "SELECT id FROM lift_telegram WHERE user_id=$this->user_id ";
        //проверим есть ли для пользователя ожидания сообщения
        $check = $this->DB->single($query_search_telegram_action);

        if ($check) {
            // если есть то изменим на текущую
            $query_telegram_expectation = "UPDATE lift_telegram SET call_id=:callId, time=$current_timestamp, action='$this->call_back_action' WHERE id=$check";
        } else {
            $query_telegram_expectation = "INSERT INTO `lift_telegram`  SET user_id=$this->user_id, call_id=:callId, time=$current_timestamp, action='$this->call_back_action' ";
        }

        $address = $this->message_text;
        $add_telegram_action = $this->DB->query($query_telegram_expectation, array("callId" => $call_id));
        if ($add_telegram_action) {
            $temp = $address . $this->action_text;
        } else {
            $this->mainLog->logSave("SQL Error $query_telegram_expectation , callId => $call_id", "action_call","telegram");
            $temp = "Произошла ошибка попробуйте повторить запрос позднее";
        }
        $arr_delete_message = array('chat_id' => $this->chat_id, 'message_id' => "$this->message_id");
        $this->setMessageTelegramSend($temp);
        $answer = $this->sendToTelegram();
        //$this->debug(json_encode($answer), "answer");
        if (isset($answer['ok']) and $answer['ok'] == 1) {
            $this->request(0, $arr_delete_message);
        }
    }
    public function action_call()
    {

        /**
         * Кнопка действия по заявке
         *method deleteMessage - удалить сообщение (array('chat_id' => $chat_id,'message_id' => "$message_id")
         */
      $this->request(0,array('chat_id' => $this->chat_id,'message_id' => "$this->message_id"));
      switch ($this->call_back_action) {
           case "note":
               $this->action_text = "Отправьте в ответ сообщение с текстом заметки или фотографию в течении $this->time мин.";
               $this->actionSend();
               break;
           case "close":
               $this->action_text = "Для закрытия заявки отправьте в ответ текстовое сообщение, с текстом решения по заявке в течении $this->time мин.";
               $this->actionSend();
               break;
           case "more":
               $this->more_call($this->call_back_id);
               break;
           default:
               $this->setMessageTelegramSend("Какaя-то не понятная команда :(. \n Наверное ты пытаешся меня обмануть :)");
               $this->sendToTelegram();
               exit();

       }
        return true;
    }
    public function more_call($call_id)
    {

        //подробнее о заявке
        $arr_delete_message = array('chat_id' => $this->chat_id, 'message_id' => "$this->message_id");
        $call_id = (int)$call_id;
        $call_query = "SELECT call_date, call_first_name, call_adres, call_details,  call_request, call_staff, call_staff_status FROM lift_calls WHERE call_id=$call_id LIMIT 1";
        $call_info = $this->DB->row($call_query);
        if (!$call_info) {
            $this->setMessageTelegramSend('Какая-то ошибка базы. Бот не смог получить информацию, попробуйте чуть позже');
            $this->sendToTelegram();
            $this->mainLog->logSave("callId=$call_id, query = $call_query err=1", "more_call","telegram");
            return false;
        }
        if ($this->user_id != $call_info['call_staff']) {
            $this->setMessageTelegramSend('Возникла какя-то ошибка. Эта заявка не твоя! Возможно диспетчер уже изменила ответсвенного. ');
            $this->sendToTelegram();
            $this->mainLog->logSave("userid=" . $this->user_id . ", callId=$call_id, queryresult = " . json_encode($call_info) . " err=2", "more_call","telegram");
            return false;
        }
        $request_query = "SELECT type_name FROM lift_types WHERE type_id=" . $call_info['call_request'];
        $request_name = $this->DB->single($request_query);
        if (!$call_info['call_staff_status']) {
            // запишим в базу данные о том что ответсвенный уведомлен
            $update_call_query = "UPDATE lift_calls SET call_staff_date=" . time() . " , call_staff_status=2 WHERE call_id=$call_id";
            $this->DB->query($update_call_query);
        }
        $date_call = date('d-m-y # H:i', $call_info['call_date']);
        $text_message = "$date_call " . $call_info['call_first_name'] . "\n создал(а) заявку с № $call_id по адресу: \n" . $call_info['call_adres'] . "\n Детали заявки: \n " . $call_info['call_details'] . "\n Уровен заявки - $request_name";
        $this->setMessageTelegramSend($text_message);
        $answer = $this->sendToTelegram();
        if ($answer['ok']) {
            $this->request(0, array('chat_id' => $this->chat_id, 'message_id' => $this->message_id));
        }
    }


}