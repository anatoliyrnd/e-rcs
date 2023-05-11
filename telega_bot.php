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
//echo  $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR,"text");
//$bot_token = '5326342855:AAGW8v0uOkYXa2u6oSM-sTTAPlFcdSq5GRE';
$data = file_get_contents('php://input'); // весь ввод перенаправляем в $data
debug($data, "send");
$data = json_decode($data, true); // декодируем json-закодированные-текстовые данные в PHP-массив
spl_autoload_register(function($class) {
    $root = $_SERVER['DOCUMENT_ROOT'];
    $ds = DIRECTORY_SEPARATOR;
    $filename = $root . $ds . str_replace('\\', $ds, $class) . '.php';
    require($filename);
});
$telegram=new \telegram\telegram_class(TOKEN_TELEGRAM,$DB,true);

if (isset($data['callback_query'])) {
    //получили ответ от кнопки   inline_keyboard
    $call_back_id = $data['callback_query']['id'];
if(!$telegram->setData($data['callback_query'],$call_back_id)){$telegram->send_to_telegram( $data['callback_query']['message']['chat']['id'], "Ошибка данных телеграм чата");exit();}
    $user_id = $telegram->telegram_id_check();
    if (!$user_id) {
        $telegram->send_to_telegram( $data['callback_query']['message']['chat']['id'], "Представьтесь!Отправьте контактные данные. Ваш номер телефона не будет доступен третьим лицам.");
        exit();
    }
    $temp = '';
    $data_keyboard = json_decode($data['callback_query']['data'], true);
    //request(1, TOKEN_TELEGRAM, $arr_request_keyboard);
    if (isset($data_keyboard['action'])) {
        $action = $data_keyboard['action'];
        switch ($action) {
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
        }
    }
} else if (!empty($data['message']['text']) || !empty($data['message']['photo'])) {
    // если просто текстовое сообщение или фото
    if(!$telegram->setData($data)){$telegram->send_to_telegram( $data['message']['chat']['id'], "Ошибка данных телеграм чата");exit();}
    $text = trim($data['message']['text']);
    $text_array = explode(" ", $text);
    $text_command = mb_strtolower($text, 'UTF-8');
    $text_command = preg_replace('/\s/', '', $text_command);
     ($telegram->check_action())?exit():null;//Если действия с заявкой  выполнии выходим
    (strlen($text_command)<3)?exit():null;
        switch ($text_command) {
            case "/start":
                start_command($data);
                break;
            case "/del":
            case "/stop":
                ($telegram->unsubscribe())?exit():null; //если отписались выходим
                break;
            case 'help':
                $telegram->send_help();
                break;
            case 'открытыезаявки':
                $telegram->open_calls();
                break;
            case 'закрытыезаявки':
                // close_calls($data);
                break;
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




function debug($text, $type = 'not')
{

    if (!DEBUG) {
        return;
    }
    $text = date('Y-m-d H:m:s') . " - " . $_SERVER['REMOTE_ADDR'] . " - " . $text;
    $file = date('Y-m-d') . $type;
    file_put_contents("./" . $file . "_telega.txt", $text . PHP_EOL);
}




