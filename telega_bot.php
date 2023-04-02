<?php header('Content-Type: text/html; charset=utf-8'); // на всякий случай досообщим PHP, что все в кодировке UTF-8
$time_start = microtime(true);
include("./include/ldisp_config.php");
include("./include/function.php");
include("./include/static_data.php");
require_once("./include/PDO.class.php");
include("./include/telegram.php");
const DEBUG = true;//запись в файл ответов от телеграм в папке с файлом


$DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);
$site_dir = dirname(dirname(__FILE__)) . '/'; // корень сайта
//$bot_token = '5326342855:AAGW8v0uOkYXa2u6oSM-sTTAPlFcdSq5GRE';
$data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
debug($data, "send");
$data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив


// строка ниже для вывода получаемых сообщений в файл для отладки:


/*if (array_key_exists('callback_query', $data)){
    $comand=json_decode($data['callback_query']['data'], true);
    $chat_id=$data['callback_query']['message']['chat']['id'];

    $queri_id=$data['callback_query']['id'];
    answerCallback($queri_id,$chat_id,TOKEN_TELEGRAM);
    $text=$comand['id']."---".$comand['user'];
    message_to_telegram(TOKEN_TELEGRAM, 1401760365, $text);
    deleteMessage($data['callback_query']['message']['message_id'], $chat_id,TOKEN_TELEGRAM);

}*/

if (isset($data['callback_query'])) {
    //получили ответ от кнопки   inline_keyboard

    $user_id = telegram_id_check($DB, $data['callback_query']);
    if (!$user_id) {
        send_to_telegram(TOKEN_TELEGRAM, $data['callback_query']['message']['chat']['id'], "Чет я тебя не знаю , команды незнакомцев не принимаются!");
        exit();
    }
    $temp = '';
    $data_keyboard = json_decode($data['callback_query']['data'], true);
    $query_id = $data['callback_query']['id'];
    $chat_id = $data['callback_query']['message']['chat']['id'];
    $message_id = $data['callback_query']['message']['message_id'];
    $arr_request_keyboard = array('callback_query_id' => $query_id, 'text' => $chat_id);
    $arr_delete_message = array('chat_id' => $chat_id, 'message_id' => "$message_id");
    $address = $data['callback_query']['message']['text'];
    request(1, TOKEN_TELEGRAM, $arr_request_keyboard);
    //request(0, TOKEN_TELEGRAM, $arr_delete_message);
    if (isset($data_keyboard['action'])) {
        $action = $data_keyboard['action'];
        switch ($action) {
            case "note":
                $temp = " $address Отправьте в ответ сообщение с текстом заметки " . $data_keyboard['id'];
                break;
            case "close":
                $temp = " $address Для закрытия заявки, отправьте в ответ сообщение с текстом Решения по заявке " . $data_keyboard['id'];
                break;
            case "more":
                more_call($data,$data_keyboard['id'],$arr_delete_message);
                break;
            default:
                $temp = "Какaя то не понятная команда :(";

        }

    }
    send_to_telegram(TOKEN_TELEGRAM, $data['callback_query']['message']['chat']['id'], $temp . "-" . $user_id);

} else if (!empty($data['message']['text'])) {
    // если просто текстовое сообщение

    $text = trim($data['message']['text']);
    $text_array = explode(" ", $text);
    $text_command = mb_strtolower($text, 'UTF-8');
    $text_command = preg_replace('/\s/', '', $text_command);
    //send_to_telegram(TOKEN_TELEGRAM, $data['message']['chat']['id'], $text_command);

    switch ($text_command) {
        case "/start":
            start_command($data);
            break;
        case "/stop":
            unsubscrbe($data);
            break;
        case "/del":
            unsubscrbe($data);
            break;

        case 'help':
            send_help($data);
            break;
        case 'открытыезаявки':
            open_calls($data);
            break;
        case 'закрытыезаявки':
           // close_calls($data);
            break;


    }

}
(isset($data['message']['contact'])) ? subscribe($data) : null;
function start_command($arrData)
{
    //стартовая инициализация

}

function more_call($arrData,$call_id,  $arr_delete_message=array())
{
    global $DB;
    $user_id = telegram_id_check($DB, $arrData['callback_query']);

    if (!$user_id) {
        send_to_telegram(TOKEN_TELEGRAM, $arrData['callback_query']['message']['chat']['id'], 'Не нашел тебя в нашей базе. Твой ID - ' . $arrData['callback_query']['message']['chat']['id']);
        exit();
    }
    $call_id=(int)$call_id;
    $call_query="SELECT call_date, call_first_name, call_adres, call_details,  call_request, call_staff, call_staff_status FROM lift_calls WHERE call_id=$call_id LIMIT 1";

    $call_info=$DB->row($call_query);

    if (!$call_info){
        send_to_telegram(TOKEN_TELEGRAM, $arrData['callback_query']['message']['chat']['id'], 'Какая-то ошибка базы. Бот не смог получить информацию, попробуйте чуть позже');
        exit();
    }
    if($user_id!=$call_info['call_staff']){
        send_to_telegram(TOKEN_TELEGRAM, $arrData['callback_query']['message']['chat']['id'], 'Возникла какя-то ошибка. Эта заявка не твоя! Возможно диспетчер уже изменила ответсвенного. ');
        exit();
    }

    $request_query="SELECT type_name FROM lift_types WHERE type_id";
    $request_name=$DB->single($request_query);

    if(!$call_info['call_staff_status']){
        // запишим в базу данные о том что ответсвенный уведомлен
        $update_call_query="UPDATE lift_calls SET call_staff_date=".time()." , call_staff_status=2 WHERE call_id=$call_id";

        $DB->query($update_call_query);
        //logsave($update_call_query,"_staff_telega_read);
    }
    $date_call=date('d-m-y # H:i', $call_info['call_date']);

    $text_message="$date_call ".$call_info['call_first_name']."\n создал(а) заявку с № $call_id по адресу: \n".$call_info['call_adres']."\n Детали заявки: \n ".$call_info['call_details']."\n Уровен заявки - $request_name";
   $answer=send_to_telegram(TOKEN_TELEGRAM,$arrData['callback_query']['message']['chat'] ['id'],$text_message);
   if (isset($answer['ok']) AND $answer['ok']==1) {
       request(0, TOKEN_TELEGRAM, $arr_delete_message);
   }
}

function unsubscrbe($arrData)
{
//отписка от получения сообщений в телеграмм
    global $DB;
    $telegram_id = $arrData['message']['chat'] ['id'];
// подписка на новые заявки в телеграмм
    $user_id = telegram_id_check($arrData);
    if (!$user_id) {
        send_to_telegram(TOKEN_TELEGRAM, $arrData['message']['chat']['id'], 'Вы не подписаны на новые заявки');
        return false;
    }
    $my_query = "UPDATE lift_users SET user_telegram = 0 WHERE  user_id = '$user_id';";
    $res = $DB->query($my_query);
    if ($res) {
        send_to_telegram(TOKEN_TELEGRAM, $arrData['message']['chat']['id'], 'Ваш Телеграм ID удален из нашей базы. Вы больше не сможите получать уведомления. Для повторной подписки отправьте свой телефон через меню чата (... -> "Отправить свой телефон"');
        return true;
    }
}

function subscribe($arrData)
{
    global $DB;
    $telegram_id = $arrData['message']['chat'] ['id'];

// подписка на новые заявки в телеграмм
    if (telegram_id_check($DB, $arrData)) {
        send_to_telegram(TOKEN_TELEGRAM, $arrData['message']['chat']['id'], 'Ваш ID уже внесен в нашу базу для отписки от сообщений о новых заявках отправьте команду /del или /stop');
        return false;
    }
    isset($arrData[message][contact][phone_number]) ? $telegram_phone = (int)$arrData[message][contact][phone_number] : $telegram_phone = false;
    if ($telegram_phone) {
        $query_phone = "SELECT user_id FROM lift_users WHERE user_phone=$telegram_phone LIMIT 1;";
        $user_id = $DB->single($query_phone); //;
        if ($user_id) {
            $myquery = "UPDATE lift_users SET user_telegram = ? WHERE  user_id = $user_id;";
            $res = $DB->query($myquery, array($telegram_id));
            $res ? $text_return = "Ваш ID добавлен в базу,и активированна функция отправки сообщений в телеграмм" : $text_return = "Произошла ошибка при записи в базу";
            send_to_telegram(TOKEN_TELEGRAM, $telegram_id, $text_return);
        } else {
            $text_return = "Вош номер телефона не найден в базе  ";
            send_to_telegram(TOKEN_TELEGRAM, $telegram_id, $text_return);
        }
    }
}


function answerCallback($queri_id, $chat_id, $bot_api_key)
{
    $data = array(
        'callback_query_id' => $queri_id,
        'text' => $chat_id
    );
    request('answerCallbackQuery', $bot_api_key, $data);

}


function debug($text, $type = 'not')
{

    if (!DEBUG) {
        return;
    }
    $text = date('Y-m-d H:m:s') . " - " . $_SERVER['REMOTE_ADDR'] . " - " . $text;
    $file = date('Y-m-d') . $type;
    file_put_contents("./" . $file . "_telega.txt", $text . PHP_EOL);
}


function send_help($arrData)
{
    $text = "Приветсвтую тебя \n Для того что бы отписать свой телеграм от получения новых заявок подай команду /stop \n Чтобы зарегестрировать твой телеграм отправь контактные данные \n Три точки в правом верхнем углу и выбрать  'Отправить свой телефон' затем нажать кнопочку - Поделиться контактом \n Для управления открытми заявками нажми ниже кнопочку 'Открытые заявки'\n(в разработке) Под каждой открытой заявкой кнопочки Закрыть и Заметка ( позваляют собственно выполнить все то что на них написано)  \n Не забывая что врежиме ЗАКРЫТЬ или ЗАМЕТКА любое сообщение отправленное боту будет как соответсвующее сообщение по команде (закрытие  - как решение по заявке, Заметке - как добавление текстовой заметке к заявке) Если команду выполнять не надо, нажми кнопочку ОТМЕНА\n Удачи! \n  (C) Zamotaev A.N. https://e-rcs.ru";
    send_to_telegram(TOKEN_TELEGRAM, $arrData['message']['chat']['id'], $text);

}

function open_calls($arrData)
{
    global $DB;
    $text_return = '';

    $user_id = telegram_id_check($DB, $arrData);

    if (!$user_id) {
        send_to_telegram(TOKEN_TELEGRAM, $arrData['message']['chat']['id'], "Я тебя не знаю! Поэтому твое первое задание пройти идентификацию! Отправь номер телефона через меню - три точки в левом правом углу");
    }
    $myquery = "SELECT call_id, call_date, call_adres, call_details, call_request, call_staff_status from lift_calls WHERE (call_status = 0) AND call_staff =$user_id ;";
    $lift_calls = $DB->query($myquery);
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
            $DB->query($update_status);
            $history_date = strtotime(date('Y-m-d H:i:s '));
            $set_history = "Заявка по адресу - " . $call_address . " Отмечена прочитанной. Прочитана в Телеграм. "; //запись в журнал
            $DB->query("INSERT INTO lift_history (history_date,history_info, call_id) VALUES( $history_date, \"$set_history\",  $call_id );");
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
        send_to_telegram(TOKEN_TELEGRAM, $arrData['message']['chat']['id'], $text_return, $replay);
    }
}
