<?php

include("../include/ldisp_config.php");
include("../include/function.php");
include("../include/static_data.php");
require_once("../include/PDO.class.php");

$user_id="1";
$DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);
$myquery = "SELECT call_id, call_date, call_adres, call_details, call_request, call_staff_status from lift_calls WHERE (call_status = 0) AND call_staff =$user_id ;";

$lift_calls = $DB->query($myquery);
//print_r($lift_calls);
foreach ($lift_calls as $call) { //начало цикла формирования заявок
//print_r($call);
    $call_id = $call['call_id'];

    $call_staff_status = $call['call_staff_status'];
    $call_details = $call['call_details'];
    $call_address = $call['call_adres'];
    $call_request = $call['call_request']; //уровень заявки
    $call_date = date("Y-m-d H:i", $call['call_date']);
    //echo  "-$call_id-$call_staff_status-";
    if (!$call_staff_status) {// Если отмечена как не переданна, то измененяем состояние на переданна онлайн
        $query_staff_date = "call_staff_date=" . strtotime(date('Y-m-d H:i:s ')) . ',';
        $update_status = "UPDATE lift_calls SET  $query_staff_date call_staff_status=2 WHERE  call_id=$call_id;";
        $DB->query($update_status);
        $history_date = strtotime(date('Y-m-d H:i:s '));
        $set_history = "Заявка по адресу - " . $call_address . " Отмечена прочитанной. Прочитана в Телеграм. "; //запись в журнал
        $DB->query("INSERT INTO lift_history (history_date,history_info, call_id) VALUES( $history_date, \"$set_history\",  $call_id );");
    }
    $text_return.=  "Дата:	$call_date \nАдрес: $call_address \nОписание: $call_details \n------------\n";
}
echo $text_return;
