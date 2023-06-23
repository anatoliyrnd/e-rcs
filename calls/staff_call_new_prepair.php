<?php

namespace includes;

class staff_call_new_prepair
{



    /**
     * @param $user_id int ид ответственного
     */
    public function __construct()
    {
  $this->mainConfig=new mainConfig()  ;
  $this->logSave=new logSave();
    }

    /**
     * @param $id integer ид ответсвенного
     * @param $call_id integer ид заявки
     * @return false|mixed
     */
    public function staff_call_new($id,$call_id)
    {
$id=(int)$id;
$call_id=(int)$call_id;
//проверим есть ли у пользователя телеграм
        $query_user = "SELECT user_telegram FROM lift_users WHERE user_id =:ID LIMIT 1";
        $user_telegram = $this->mainConfig->getDB()->single($query_user, array('ID' => $id));
        if (!$user_telegram) {
            return false;
        }
        $query_call = "SELECT call_adres,call_request FROM lift_calls WHERE call_id=$call_id";
        $call_info = $this->mainConfig->getDB()->row($query_call);
        ($call_info['call_request'] === 1) ? $alarm = "\xF0\x9F\x9A\xA8 \n" : $alarm = "\xF0\x9F\x8F\xA2\n";
        $add_date = date("d-m-Y H:i ");
        $text = "По адресу: " . $call_info['call_adres'];
        $text_telegram = "$add_date Новая заявка! $alarm \n $text";
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
        return [$user_telegram, $text_telegram, $replay];
        //$response_telegram_api = $this->telegram->send_to_telegram($user_telegram, $text_telegram, $replay);
        $result = json_decode($response_telegram_api, true);
        if (!$result['ok']) {
            $error = "error send to telegram info call" . date("Y-m-d H:i:s ") . "-" . $text_telegram . "-" . $response_telegram_api;
            $this->logSave->logSave($error, "error_telegram_call_info");
        }
        return $result['ok'];
    }
}