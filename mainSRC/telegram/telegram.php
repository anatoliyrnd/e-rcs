<?php

namespace mainSRC\telegram;

use mainSRC\dataBase\PDODB;
use mainSRC\logSave;
use mainSRC\main;

class telegram
{
    /**
     * @var
     */
    protected $telegram_token;
    protected $call_back_id;
    protected $call_back_action;
    protected $query_id;//ид для подтверждения получения команды от кнопки

protected $user_id;
protected $user_name;
protected $DB;
protected $chat_id;
protected $message_id;
protected $message_text;
protected $message_photo;
protected $photo;
protected $message_telegram_send;
protected $log_save;
    public function __construct()
    {
        $this->log_save=new logSave();
        $this->DB=PDODB::getInstance();
        $this->telegram_token =$this->DB->single("SELECT option_value FROM lift_options WHERE option_name='telegram_token' LIMIT 1");
    }
    protected function getParameterConfig($option_name){
        return $this->DB->single("SELECT option_value FROM lift_options WHERE option_name='$option_name'");
    }
public function getTelegramToken(){
return $this->telegram_token;
}
    public function sendToTelegram($reply_markup = '')
    {
        /**
         * send to telegram message
         *
         * @param array $reply_markup json  keyboard
         */
        $ch = curl_init();
        if ($reply_markup == '') {
            $btn[] = ["text" => "Help"];
            $btn[] = ["text" => "Открытые заявки"];
            $btn[] = ["text" => "Закрытые зявки"];
            $reply_markup = json_encode(["keyboard" => [$btn], "resize_keyboard" => true]);
        }
        $ch_post = [
            CURLOPT_URL => 'https://api.telegram.org/bot' . $this->telegram_token . '/sendMessage',
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => [
                'chat_id' => $this->chat_id,
                'parse_mode' => 'HTML',
                'text' => $this->message_telegram_send,
                'reply_markup' => $reply_markup,
            ]
        ];
        if ($this->call_back_id) {
            $this->request(1, array('callback_query_id' => $this->call_back_id, 'text' => $this->chat_id));
        }
        curl_setopt_array($ch, $ch_post);
        $resolve= curl_exec($ch);
        (new main)->debug(print_r($resolve, true), '_resolve','telegram');
        return $resolve;
    }

    /**
     * @param int  $method_id 0 or 1
     * @param array $data array for delete message
     * @return mixed
     */
    public function request($method_id, $data = array())
    {
        $curl = curl_init();
        $method_list = ['deleteMessage', 'answerCallbackQuery'];
        $method = $method_list[$method_id];
//method deleteMessage - удалить сообщение (array('chat_id' => $chat_id,'message_id' => "$message_id")
//answerCallbackQuery -подтвердить получение обратного вызова от кнопки (array('callback_query_id'      => $queri_id,'text'     => $chat_id)
        curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->telegram_token . '/' . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $resolve = json_decode(curl_exec($curl), true);
        curl_close($curl);
        (new main)->debug(print_r($resolve, true), '_request','telegram');
        return $resolve;
    }
    public function sendHelp()
    {
        $this->message_telegram_send = "Приветсвтую тебя \n Для того что бы отписать свой телеграм от получения новых заявок подай команду /stop \n Чтобы зарегестрировать твой телеграм отправь контактные данные \n Три точки в правом верхнем углу и выбрать  'Отправить свой телефон' затем нажать кнопочку - Поделиться контактом \n Для управления открытми заявками нажми ниже кнопочку 'Открытые заявки'\n(в разработке) Под каждой открытой заявкой кнопочки Закрыть и Заметка ( позваляют собственно выполнить все то что на них написано)  \n Не забывая что врежиме ЗАКРЫТЬ или ЗАМЕТКА любое сообщение отправленное боту будет как соответсвующее сообщение по команде (закрытие  - как решение по заявке, Заметке - как добавление текстовой заметке к заявке) Если команду выполнять не надо, нажми кнопочку ОТМЕНА\n Удачи! \n  (C) Zamotaev A.N. https://e-rcs.ru";
        $this->sendToTelegram();
    }

    /**
     * @return mixed
     */
    public function getChatId()
    {
        return $this->chat_id;
    }

    /**
     * @param mixed $chat_id
     */
    public function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;
    }


    /**
     * @return mixed
     */
    public function getMessageTelegramSend()
    {
        return $this->message_telegram_send;
    }

    /**
     * @param mixed $message_telegram_send
     */
    public function setMessageTelegramSend($message_telegram_send)
    {
        $this->message_telegram_send = $message_telegram_send;
    }
    public function setData($data)
    {

        if (!isset($data)) {
            $this->log_save->logSave("Ошибка данных телеграм пустое значение", "error_data","telegram");
            return false;
        }
        $this->chat_id = isset($data['message']['chat']['id'])?$data['message']['chat']['id']:0;
        $this->message_id=isset ($data['message']['message_id'])?$data['message']['message_id']:0;
        $this->telegram_id_check($this->chat_id);
        $this->message_text=$data['message']['text']??'';
        if (array_key_exists('photo', $data['message'])) {
            $this->photo=true;
            $this->message_photo=$data['message']['photo'];
        }
        if (array_key_exists('document', $data['message'])) {
          $this->setMessageTelegramSend("Воспользуйтесь меню <фото>, вместо <файл>");
          ;$this->sendToTelegram();
          exit();
            $this->photo=true;
            $this->message_photo=$data['message']['document'];
        }
        $call_back=json_decode($data['data'],true);
        $this->call_back_id=$call_back['id']??0;
        $this->call_back_action=$call_back['action']??'';
        $this->query_id=$data['id']??0;
        return true;
    }
    public function telegram_id_check($telegram_id=null)
    {$id=$telegram_id??$this->chat_id;
        $query = "SELECT user_id, user_name FROM `lift_users` WHERE `user_telegram`=? LIMIT 1;";
        $result = $this->DB->row($query, array($id));
        if ($result) {
            $this->user_id=$result['user_id'];
            $this->user_name=$result['user_name'];
            return true;
        } else {
            $this->log_save->logSave("tg id $id Ошибка данных пользователя ".print_r($result,true), "error_data","telegram");
        return false;
        }
    }
    public function getPhotoPath($file_id)
    {
        // получаем объект File
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->telegram_token . '/getFile');
        curl_setopt($ch, CURLOPT_POST, count(['file_id' => $file_id]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['file_id' => $file_id]));
        $result = curl_exec($ch);
        curl_close($ch);
        // возвращаем file_path
        return $result;
    }
}