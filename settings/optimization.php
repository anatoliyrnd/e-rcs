<?php
include("../include/session.php");
include("../include/checksession.php");
include("../include/ldisp_config.php");
include("../include/function.php");
include("../include/static_data.php");
require_once("../include/PDO.class.php");

if (isset($user_id)) {
    $nacl    = nacl($user_id);
    $user_id = (int) $user_id;
    if ($nacl != $user_nacl) {
        $log = " (nacl)$nacl - (user-nacl) " . $user_nacl . " ";
        logsave($log, "optimization_php_error");
        $response['status']  = 'error';
        $response['message'] .= "ошибка авторизации";
        echojson($response);
    }
} else {
    $log = " user_id error ";
    logsave($log, "optimization_php_error");
    $response['status']  = 'error';
    $response['message'] .= "Не получен User ID";
    echojson($response);
}
$DB                  = new PDODB(db_host, DBPort, db_name, db_user, db_password);
$optimization_report='';    
$child_arr=["city"=>"street","street"=>"home","home"=>"object"];

foreach ($child_arr as $key => $value) {
    optimization($key,$value);
}
function optimization ($type,$child)
{
global $DB,$optimization_report; 
    
   
$query="SELECT `id` FROM `lift_".$type."` WHERE `vis_".$type."`=0"; //пролучим список  у которых стоит флаг (показывать)
$list_vis=$DB->column($query);
foreach ($list_vis as $value) {
    $child_check_query="SELECT `id` FROM `lift_".$child."` WHERE `".$type."_id`= $value  AND `vis_".$child."`=0 LIMIT 1"; //запрос на проверку есть ли хоть один дочерний объект не скрытый
if (!$DB->single($child_check_query)){
    //если нет не скрытых элементов то скрываем дочерний элемент
    $vis_not_query   = "UPDATE `lift_".$type."` SET `vis_".$type."`=1 WHERE `id`=$value";
        $result_vis = $DB->query($vis_not_query); 
        if ($result_vis) {
            $optimization_report .= "скрыт $type - $value / ";
        } else {
            $optimization_report .= "err $type не скрыт-$value / ";
        }
}

}
}

$response['status'] = 'ok';
$response['message']=$optimization_report;
echojson($response);