<?php header('Content-Type: text/html; charset=utf-8'); // на всякий случай досообщим PHP, что все в кодировке UTF-8
$time_start = microtime(true);
include_once  ("./include/autoload.php");

$data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
$data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив

if (isset($data['callback_query'])) {
    $telegram=new \mainSRC\telegram\telegramAction();
    if(!$telegram->setData($data['callback_query'])){exit();}

    $user_id = $telegram->telegram_id_check();
    if (!$user_id) {
        $telegram->setMessageTelegramSend("Представьтесь!Отправьте контактные данные. Ваш номер телефона не будет доступен третьим лицам.");
        $telegram->sendToTelegram();
        exit();
    }
    $temp = '';
    //$data_keyboard = json_decode($data['callback_query']['data'], true);
    //request(1, TOKEN_TELEGRAM, $arr_request_keyboard);

    if (isset($data['callback_query']['data'])) {
        $telegram->action_call();
        // $action = $data_keyboard['action'];
        /*switch ($action) {
             case "note":
                 $telegram->action_Call( $data_keyboard['id'], 'add_note');
                 break;
             case "close":
                 $telegram->action_Call( $data_keyboard['id'], 'close_call');
                 break;
             case "more":
                  $telegram->more_call( $data_keyboard['id'] );
                 break;
             default:
                 $temp = "Какaя-то не понятная команда :(";
         }*/
    }
} else if (!empty($data['message']['text']) || !empty($data['message']['photo']) || !empty($data['message']['document'])) {

    // если просто текстовое сообщение или фото
    $telegram=new \mainSRC\telegram\callCommands();
    if(!$telegram->setData($data)){exit();}
    $text = trim($data['message']['text']);
    $text_array = explode(" ", $text);
    $text_command = mb_strtolower($text, 'UTF-8');
    $text_command = preg_replace('/\s/', '', $text_command);
    if(strlen($text_command)<4)$text_command='non';
    switch ($text_command) {
        case "/start":
            //    start_command($data);
            break;
        case "/del":
        case "/stop":
            //   ($telegram->unsubscribe())?exit():null; //если отписались выходим
            break;
        case 'help':
            $telegram->send_help();
            break;
        case 'открытыезаявки':

            $telegram->readOpenCalls();
            break;
        case 'закрытыезаявки':
            // close_calls($data);
            break;
        default:
            $telegram->check_action();
    }
}
if(isset($data['message']['contact'])) {
    if(!$telegram->setData($data)){$telegram->send_to_telegram( $data['message']['chat']['id'], "Ошибка данных телеграм чата");exit();}
    $telegram->subscribe();
}

function start_command($arrData)
{
    //стартовая инициализация

}

