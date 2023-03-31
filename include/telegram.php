<?php
//include telegramm
function staff_call_new($DB,$userid,$call_id,$text='',$urgent=false)
{

//проверим есть ли у пользователя телеграм
    $queryuser = "SELECT user_telegram FROM lift_users WHERE user_id =:ID LIMIT 1";//получим имя ответсвенного по его id
    $user_telegram = $DB->single($queryuser, array('ID' => $userid));
    $filename = "../logs/telega" . date('m_y') . ".txt";
    //file_put_contents($filename, $user_telegram . PHP_EOL, FILE_APPEND);
$urgent?$alarm="\xF0\x9F\x9A\xA8 \n":$alarm="\xF0\x9F\x8F\xA2\n";
    if (!$user_telegram) {
         $new_str = "not telegam id " . date("Y-m-d H:i:s ") . " user id=" . $userid;
       // file_put_contents($filename, $new_str . PHP_EOL, FILE_APPEND);
        return 0;
    }
    $adjdate = date("d-m-Y H:i ");
    $text_telega = "$adjdate Новая заявка! $alarm \n $text";

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
//запишим в логи
    $result=send_to_telegram(TOKEN_TELEGRAM, $user_telegram, $text_telega, $replay);
    $new_str = "Уведмление ответсвенного отправлено" . date("Y-m-d H:i:s ") . "-" . $text_telega . "-" . $replay;
return $result;
    //file_put_contents($filename, $new_str . PHP_EOL, FILE_APPEND);
}
    function telegram_id_check($DB,$arrData)
{

$telegram_id = $arrData['message']['chat'] ['id'];
$query = "SELECT `user_id` FROM `lift_users` WHERE `user_telegram`=? LIMIT 1;";
$result = $DB->single($query, array($telegram_id));
if ($result) {
return $result;
} else {
return false;
}
}
function send_to_telegram($bot_token, $chat_id, $text, $reply_markup = '')
{
$ch = curl_init();
if ($reply_markup == '') {
$btn[] = ["text" => "Help"];
$btn[] = ["text" => "Открытые заявки"];
$btn[] = ["text" => "Закрытые зявки"];
$reply_markup = json_encode(["keyboard" => [$btn], "resize_keyboard" => true]);
}
$ch_post = [
CURLOPT_URL => 'https://api.telegram.org/bot' . $bot_token . '/sendMessage',
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
curl_setopt_array($ch, $ch_post);
$result = json_decode(curl_exec($ch), true);
//debug(print_r($result, true), '_result_message');
return $result;

}
function request($method_id, $bot_api_key, $data = array())
{
$curl = curl_init();
$method_list = ['deleteMessage', 'answerCallbackQuery'];
$method = $method_list[$method_id];
//method deleteMessage - удалить сообщение (array('chat_id' => $chat_id,'message_id' => "$message_id")
//answerCallbackQuery -подтвердить получение обратного вызова от кнопки (array('callback_query_id'      => $queri_id,'text'     => $chat_id)
curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $bot_api_key . '/' . $method);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$out = json_decode(curl_exec($curl), true);
curl_close($curl);
//debug(print_r($out, true), '_request');
return $out;
}