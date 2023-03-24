<?php

include("../include/ldisp_config.php");
include("../include/function.php");
include("../include/static_data.php");
require_once("../include/PDO.class.php");


$DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);
object_echo();
function object_echo()
{
    global $DB;
    $list_delite_possible   = array(); //массив объектов которые можно удалить
    $list_delite_impossible = array(); //массив объектов которые удалить нельзя (есть ссылки)
    $list_object            = $DB->query("SELECT `id`, `object_name` FROM `lift_object` WHERE `vis_object`=1;");
 
    foreach ($list_object as $value) {
        $check_id= $DB->column("SELECT `call_id` FROM `lift_calls` WHERE `address_lift`=".$value['id'].";");
        if ($check_id){
            array_unshift($check_id , $value['object_name']); 
            $list_delite_impossible[$value['id']]= $check_id;  
        }else{
            $list_delite_possible[$value['id']]=$value['object_name'];
        }
    }
    $data["possible"]=$list_delite_possible;
    $data["impossible"]=$list_delite_impossible;
    echo (json_encode($data));

    

}


