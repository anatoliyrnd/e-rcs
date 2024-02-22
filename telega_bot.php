<?php

use mainSRC\main;

require_once("./include/autoload.php");
//exit();
const command_re_send = false;

$telegram_cmd = new mainSRC\telegram\callCommands;
header('Content-Type: text/html; charset=utf-8'); // на всякий случай досообщим PHP, что все в кодировке UTF-8
$time_start = microtime(true);
$data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
//(new main)->debug(print_r($data, true), '_test', 'telegram');exit();
$data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив


if (isset($data['callback_query']))
{
    if (!$telegram_cmd->setData($data['callback_query']))
    {
        exit();
    }


    $temp = '';
    //$data_keyboard = json_decode($data['callback_query']['data'], true);
    //request(1, TOKEN_TELEGRAM, $arr_request_keyboard);

    if (isset($data['callback_query']['data']))
    {

        $telegram_cmd->action_call();

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
}
else if (!empty($data['message']['text']) || !empty($data['message']['photo']) || !empty($data['message']['document']))
{

    // если просто текстовое сообщение или фото
    if (!$telegram_cmd->setData($data))
    {
        exit();
    }


    $text         = trim($data['message']['text']);
    $text_array   = explode(" ", $text);
    $text_command = mb_strtolower($text, 'UTF-8');
    $text_command = preg_replace('/\s/', '', $text_command);
    if (command_re_send)
    {
        $telegram_cmd->setMessageTelegramSend($text_command);
        $telegram_cmd->sendToTelegram();
    }
    $user_id = $telegram_cmd->telegram_id_check();
    if (!$user_id)
    {
        $telegram_cmd->setMessageTelegramSend("Ваш telegramID (" . $telegram_cmd->getChatId() . ")  не известен. Представьтесь!Отправьте контактные данные. Ваш номер телефона не будет доступен третьим лицам.");
        $telegram_cmd->sendToTelegram();
        exit();
    }
    if (strlen($text_command) < 4)
        $text_command = 'non';
    switch ($text_command)
    {
        case "/start":
            //    start_command($data);
            break;
        case "/del":
        case "/stop":
            if (($telegram_cmd->unsubscribe()))
            {
                exit();
            } //если отписались выходим
            break;
        case 'help':
            $telegram_cmd->send_help();
            break;
        case 'открытыезаявки':
            $telegram_cmd->setMessageTelegramSend("Открытые заявки");
            $telegram_cmd->sendToTelegram();
            $telegram_cmd->readOpenCalls();
            break;
        case 'закрытыезаявки':
            // close_calls($data);
            break;
        default:
            $telegram_cmd->check_action();
    }
}
if (isset($data['message']['contact']))
{
    if (!$telegram_cmd->setData($data))
    {
        $telegram_cmd->setMessageTelegramSend("Ошибка данных телеграм чата");
        $telegram_cmd->sendToTelegram();

        exit();
    }
    $telegram_cmd->subscribe();
}

function start_command($arrData)
{
    //стартовая инициализация

}

