<?php

namespace telegram;
/**
 * @property $telegram_token = string
 * @property $DB
 * @property false $user_id
 */
class telegram_class
{
    /**
     * @param $telegram_token
     * @param $DB
     * @param $debug (foo[bool])
     *
     */
    private $user_id;
    private $telegram_token;
    private $DB;
    private $data;
    private $debug = false;
    private $root;
    private $ds;
    private $chat_id;
    private $message_id;
    private $call_back_id;

    public function __construct($token, $DB, $debug)
    {
        $this->telegram_token = $token;
        $this->DB = $DB;
        $this->root = $_SERVER['DOCUMENT_ROOT'];
        $this->ds = DIRECTORY_SEPARATOR;
        $this->debug = $debug;
    }

    public function setData($data, $callBack = false)
    {
        if (!isset($data)) {
            $this->log("Ошибка данных телеграм", "error_data");
            return false;
        }
        $this->data = $data;
        (isset($this->data['message']['chat']['id'])) ? $this->chat_id = $this->data['message']['chat']['id'] : $this->chat_id = 0;
        (isset($this->data['message']['message_id'])) ? $this->message_id = $this->data['message']['message_id'] : $this->message_id = 0;
        $this->user_id = $this->telegram_id_check();
        $this->call_back_id = $callBack;
        return true;

    }

    public function action_Call($call_id, $action)
    {
        /**
         * Кнопка действия по заявке
         *
         */
        $action_text = array("close_call" => " Для закрытия заявки отправьте в ответ текстовое сообщение, с текстом решения по заявке в течении 10 мин.",
            "add_note" => "Отправьте в ответ сообщение с текстом заметки в течении 10 мин.");
        $address = $this->data['message']['text'];
        $arr_delete_message = array('chat_id' => $this->chat_id, 'message_id' => "$this->message_id");
        $query_check_userId = "SELECT  call_staff FROM lift_calls WHERE call_id=$call_id";
        $staff_id = $this->DB->single($query_check_userId);
        if ($staff_id != $this->user_id) {
            $this->log("staff - $staff_id != user" . $this->user_id, "action_call");
            $this->send_to_telegram($this->chat_id, 'Произошла непредвиденная ошибка. Id пользователя не привязан к данной заявке');
        } else {
            $current_timestamp = strtotime("now");
            $query_search_telegram_action = "SELECT id FROM lift_telegram WHERE user_id=$this->user_id ";
            //проверим есть ли для пользователя ожидания сообщения
            $check = $this->DB->single($query_search_telegram_action);
            if ($check) {
                // если есть то изменим на текущую
                $query_telegram_expectation = "UPDATE lift_telegram SET call_id=:callId, time=$current_timestamp, action='$action' WHERE id=$check";
            } else {
                $query_telegram_expectation = "INSERT INTO `lift_telegram`  SET user_id=$this->user_id, call_id=:callId, time=$current_timestamp, action='$action' ";
            }
            $add_telegram_action = $this->DB->query($query_telegram_expectation, array("callId" => $call_id));
            if ($add_telegram_action) {
                $temp = $address . $action_text[$action];
            } else {
                $this->log("SQL Error $query_telegram_expectation , callId => $call_id", "action_call");
                $temp = "Произошла ошибка попробуйте повторить запрос позднее";
            }
            $answer = send_to_telegram($this->telegram_token, $this->chat_id, $temp);
            $this->debug(json_encode($answer), "answer");
            if (isset($answer['ok']) and $answer['ok'] == 1) {
                $this->request(0, $arr_delete_message);
            }
        }
        return true;

    }

    public function check_action()
    {
        if (!$this->user_id) {
            return false;
        }
        $telegram = "t-" . $this->chat_id;
        $current_timestamp = strtotime("now");
        $query_search_telegram_action = "SELECT id,user_id,call_id,time ,action FROM lift_telegram WHERE user_id=$this->user_id ";
        //проверим есть ли для пользователя ожидания сообщения
        $query_telegram_expectation = $this->DB->row($query_search_telegram_action);
        $call_id = $query_telegram_expectation['call_id'];
        $action = $query_telegram_expectation['action'];
        $message = $this->data['message']['text'];
        $id_telegram_expectation = $query_telegram_expectation['id'];
        $delete_action_query = "DELETE FROM `lift_telegram` WHERE id=$id_telegram_expectation";// запрос в базу для удаления записи последовательност команд телеграмм
        if ($query_telegram_expectation) {
            if (($query_telegram_expectation['time'] + 600) < $current_timestamp) {
                $this->DB->query($delete_action_query);
                return false;
            } else {
                //проверим не закрыта ли заявка
                if($this->check_call_close($call_id)){
                    $this->DB->query($delete_action_query);
                    return false;
                }
                $query_check_userId = "SELECT  call_staff FROM lift_calls WHERE call_id=$call_id";
                $staff_id = $this->DB->single($query_check_userId);
                if ($staff_id != $this->user_id) {
                    $this->log("$query_check_userId - query , staff $staff_id != user" . $this->user_id, "check_action");
                    $this->send_to_telegram($this->chat_id, 'Произошла непредвиденная ошибка. Среди Ваших заявок не найдена данная открытая заявка');
                    $this->DB->query($delete_action_query);
                    return false;
                } else {
                    if ($action === 'add_note') {
                        // add call note
                        $query_telegram_action = "INSERT INTO `lift_notes` SET note_post_ip=:telegram, note_post_user=$this->user_id, note_title='Заметка',note_body=:text, note_relation=$call_id, note_type=1,note_post_date=$current_timestamp";
                        $add_note = $this->DB->query($query_telegram_action, array("telegram" => $telegram, "text" => $message));
                        ($add_note) ? $this->send_to_telegram($this->chat_id, 'Заметка добавлена') : $this->send_to_telegram($this->chat_id, 'Произошла ошибка!');
                        $this->DB->query($delete_action_query);
                        return true;
                    } else if ($action === 'close_call') {
                            // call closed


                            if (iconv_strlen($message) < 5) {
                                $this->send_to_telegram($this->chat_id, "Отправьте текст с решением по заявке в ответном сообщении, не менее 5 символов!");
                                return false;
                            }
                            //Запрос на закрытие заявки

                            $user_name = $this->getUserName();
                            $call_date2 = strtotime(date('Y-m-d H:i:s '));
                            $query_call_close = "UPDATE lift_calls SET call_status=1, call_date2=$call_date2, call_solution=:solution, call_last_name=:user_name WHERE call_id = :call_id;";
                            $query_data = array('solution' => $message, 'user_name' => $user_name, 'call_id' => $call_id);

                            $call_close_result = $this->DB->query($query_call_close, $query_data);
                            if (!$call_close_result) {
                                $this->send_to_telegram($this->chat_id, "Произошла ошибка записи в базу информации о закрытии заявки !");
                                $this->log("$call_close_result - call_close_result , $query_call_close - query", "call_close");
                                return false;
                            }
                            $save_archive_note = $this->note_to_archive_close($call_id);
                            $error = '';
                            if (!$save_archive_note) {
                                $error = "С небольшими ошибками";
                            }
                            $this->send_to_telegram($this->chat_id, "Заявка закрыта!" . $error);
                            $this->DB->query($delete_action_query);
                            return true;
                    } else {
                        $this->log("$action - action", "action_nor_message");
                        $this->DB->query($delete_action_query);
                        return false;
                    }

                }
            }


        } else {
            return false;
        }
    }

    public function send_help()
    {
        $text = "Приветсвтую тебя \n Для того что бы отписать свой телеграм от получения новых заявок подай команду /stop \n Чтобы зарегестрировать твой телеграм отправь контактные данные \n Три точки в правом верхнем углу и выбрать  'Отправить свой телефон' затем нажать кнопочку - Поделиться контактом \n Для управления открытми заявками нажми ниже кнопочку 'Открытые заявки'\n(в разработке) Под каждой открытой заявкой кнопочки Закрыть и Заметка ( позваляют собственно выполнить все то что на них написано)  \n Не забывая что врежиме ЗАКРЫТЬ или ЗАМЕТКА любое сообщение отправленное боту будет как соответсвующее сообщение по команде (закрытие  - как решение по заявке, Заметке - как добавление текстовой заметке к заявке) Если команду выполнять не надо, нажми кнопочку ОТМЕНА\n Удачи! \n  (C) Zamotaev A.N. https://e-rcs.ru";
        $this->send_to_telegram($this->data['message']['chat']['id'], $text);

    }

    private function note_to_archive_close($call_id)
    {
        $query = "SELECT `history_date`,`history_info` FROM `lift_history` WHERE `call_id`=$call_id";
        $sthistory = $this->DB->query($query);
        $num = 0;
        $text = '';
        foreach ($sthistory as $value) {
            $date_history = date("d.m.Y@H:i", $value['history_date']);
            $num++;
            $text .= "Дата изменений:$date_history -" . $value['history_info'] . "<hr>";
        }

        if ($num) {
            $query = "UPDATE lift_calls SET call_fullhistory='$text' WHERE call_id=$call_id ";
            $update = $this->DB->query($query);
            if ($update) {
                return true;

            } else {

                $this->log("$query-query, $update - result", "history_save_error");
                return false;
            }

        } else {
            return true;
        }


    }

    public function send_to_telegram($chat_id, $text, $reply_markup = '')
    {
        /**
         * send to telegram message
         * @param $chat_id id telegram user
         * @param $text message text
         * @param $reply_markup json  keyboard
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
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'text' => $text,
                'reply_markup' => $reply_markup,
            ]
        ];

        if ($this->call_back_id) {
            $this->request(1, array('callback_query_id' => $this->call_back_id, 'text' => $chat_id));
        }
        curl_setopt_array($ch, $ch_post);
        $result = json_decode(curl_exec($ch), true);
//debug(print_r($result, true), '_result_message');

        return $result;

    }

    public function more_call($call_id)
    {
        $arr_delete_message = array('chat_id' => $this->chat_id, 'message_id' => "$this->message_id");
        $call_id = (int)$call_id;
        $call_query = "SELECT call_date, call_first_name, call_adres, call_details,  call_request, call_staff, call_staff_status FROM lift_calls WHERE call_id=$call_id LIMIT 1";
        $call_info = $this->DB->row($call_query);
        if (!$call_info) {
            $this->send_to_telegram($this->chat_id, 'Какая-то ошибка базы. Бот не смог получить информацию, попробуйте чуть позже');
            $this->log("callId=$call_id, query = $call_query err=1", "more_call");
            return false;
        }
        if ($this->user_id != $call_info['call_staff']) {
            $this->send_to_telegram($this->chat_id, 'Возникла какя-то ошибка. Эта заявка не твоя! Возможно диспетчер уже изменила ответсвенного. ');
            $this->log("userid=" . $this->user_id . ", callId=$call_id, queryresult = " . json_encode($call_info) . " err=2", "more_call");
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
        $answer = $this->send_to_telegram($this->chat_id, $text_message);
        if ($answer['ok']) {
            $this->request(0, array('chat_id' => $this->chat_id, 'message_id' => $this->message_id));
        }
    }

    /**
     * @param $method_id
     * @param $data
     * @return mixed
     */
    private function request($method_id, $data = array())
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
//debug(print_r($out, true), '_request');
        return $resolve;
    }

    public function telegram_id_check()
    {
        $telegram_id = $this->data['message']['chat'] ['id'];

        $query = "SELECT `user_id` FROM `lift_users` WHERE `user_telegram`=? LIMIT 1;";
        $result = $this->DB->single($query, array($telegram_id));

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function open_calls()
    {

        $text_return = '';

        $user_id = $this->telegram_id_check();

        if (!$user_id) {
            $this->send_to_telegram($this->chat_id, "Я тебя не знаю! Поэтому твое первое задание пройти идентификацию! Отправь номер телефона через меню - три точки в левом правом углу");
            exit();
        }
        $myquery = "SELECT call_id, call_date, call_adres, call_details, call_request, call_staff_status from lift_calls WHERE (call_status = 0) AND call_staff =$user_id ;";
        $lift_calls = $this->DB->query($myquery);
        if(!$lift_calls){
            $this->send_to_telegram($this->chat_id, "У тебя нет открытых заявок! \xF0\x9F\x91\x8D");
        }
        foreach ($lift_calls as $call) { //начало цикла формирования заявок
            $call_id = $call['call_id'];
            $call_staff_status = $call['call_staff_status'];
            $call_details = $call['call_details'];
            $call_address = $call['call_adres'];
            $call_request = (int)$call['call_request']; //уровень заявки
            $call_date = date("Y-m-d H:i", $call['call_date']);
            ($call_request === 1) ? $alarm = "\xF0\x9F\x9A\xA8 \n" : $alarm = "\xF0\x9F\x8F\xA2\n";
            // ( $call_request==3)?$alarm="\xE2\x98\x95 \n":$alarm="\n";
            if (!$call_staff_status) {// Если отмечена как не переданна, то измененяем состояние на переданна онлайн
                $query_staff_date = "call_staff_date=" . strtotime(date('Y-m-d H:i:s ')) . ',';
                $update_status = "UPDATE lift_calls SET  $query_staff_date call_staff_status=2 WHERE  call_id=$call_id;";
                $this->DB->query($update_status);
                $history_date = strtotime(date('Y-m-d H:i:s '));
                $set_history = "Заявка по адресу - " . $call_address . " Отмечена прочитанной. Прочитана в Телеграм. "; //запись в журнал
                $this->DB->query("INSERT INTO lift_history (history_date,history_info, call_id) VALUES( $history_date, '$set_history',  $call_id );");
            }
            $but = array('inline_keyboard' => array(
                array(
                    array(
                        'text' => 'Закрыть',
                        'callback_data' => '{"id":"' . $call_id . '","action":"close"}',
                    ),
                    array(
                        'text' => 'Заметка',
                        'callback_data' => '{"id":"' . $call_id . '","action":"note"}',
                    ),
                ),
            ),
                'one_time_keyboard' => TRUE,
            );
            $replay = json_encode($but);
            $text_return = $alarm . "Дата:	$call_date \nАдрес: $call_address \nОписание: $call_details \n \xF0\x9F\x91\x87 \n";
            $this->send_to_telegram($this->chat_id, $text_return, $replay);
        }
    }

    public function unsubscribe()
    {
        /**
         * отписка от получения сообщений в телеграмм
         */
        $user_id = $this->telegram_id_check();
        if (!$user_id) {
            $this->send_to_telegram($this->chat_id, 'Вы не подписаны на новые заявки');
            return false;
        }
        $my_query = "UPDATE lift_users SET user_telegram = 0 WHERE  user_id = '$user_id';";
        $res = $this->DB->query($my_query);
        if ($res) {
            $this->send_to_telegram($this->chat_id, 'Ваш Телеграм ID удален из нашей базы. Вы больше не сможите получать уведомления. Для повторной подписки отправьте свой телефон через меню чата (... -> "Отправить свой телефон"');
            return true;
        }
    }

    public function subscribe()
    {
        /**
         * подписка на новые заявки в телеграмм
         *
         */

        if ($this->telegram_id_check()) {
            $this->send_to_telegram($this->chat_id, 'Ваш ID уже внесен в нашу базу для отписки от сообщений о новых заявках отправьте команду /del или /stop');
            return false;
        }
        $this->send_to_telegram($this->chat_id, "sub-");
        isset($this->data[message][contact][phone_number]) ? $telegram_phone = (int)$this->data[message][contact][phone_number] : $telegram_phone = false;
        if ($telegram_phone) {
            $query_phone = "SELECT user_id FROM lift_users WHERE user_phone=$telegram_phone LIMIT 1;";
            $user_id = $this->DB->single($query_phone); //;
            if ($user_id) {
                $myquery = "UPDATE lift_users SET user_telegram = ? WHERE  user_id = $user_id;";
                $res = $this->DB->query($myquery, array($this->chat_id));
                $res ? $text_return = "Ваш ID добавлен в базу,и активированна функция отправки сообщений в телеграмм" : $text_return = "Произошла ошибка при записи в базу";
            } else {
                $text_return = "Вош номер телефона не найден в базе  ";
            }
            $this->send_to_telegram($this->chat_id, $text_return);
        }
    }


    private function debug($text, $type)
    {
        $file = 'debug' . $this->ds . date('Y-m-d') . $type;
        $filename = $this->root . $this->ds . str_replace('\\', $this->ds, $file);
        if (!DEBUG) {
            // return;
        }
        $text = date('Y-m-d H:m:s') . " - " . $_SERVER['REMOTE_ADDR'] . " - " . $text;
        file_put_contents($filename . "_telega.txt", $text . PHP_EOL);
    }

    private function getUserName()
    {
        return $this->DB->single("SELECT user_name FROM lift_users WHERE user_id=:id LIMIT 1", array("id" => $this->user_id));
    }

    private function log($text, $type)
    {
        $file = 'logs' . $this->ds . 'telegram' . $this->ds . date('Y-m-d') . $type;
        $filename = $this->root . $this->ds . str_replace('\\', $this->ds, $file);
        $text = date('Y-m-d H:m:s') . " - " . $_SERVER['REMOTE_ADDR'] . " - " . $text;
        file_put_contents($filename . "_telega.txt", $text . PHP_EOL, FILE_APPEND);
    }
    private function check_call_close($call_id){
        //Запрос на проверку закрыта или нет заявка перед закрытием $exit = "Заявка уже была закрыта сотрудником -call_last_name - date(call_date2) с решением : call_solution";
        $query_check_close = "SELECT call_solution, call_date2, call_last_name, call_status from lift_calls  WHERE call_id = $call_id  LIMIT 1;";
        $check_close = $this->DB->row($query_check_close);
        if ($check_close['call_status']) {
            $text = "Произошла ошибка. Заявка уже была закрыта сотрудником " . $check_close['call_last_name'] . " - " . date("Y-m-d H:i:s", $check_close['call_date2']) . " с решением " . $check_close['call_solution'];
            $this->send_to_telegram($this->chat_id, $text);
           return true;
        } else {
            return false;
        }
    }

}
