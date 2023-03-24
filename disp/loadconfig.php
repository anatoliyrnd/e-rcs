<?php
include("../include/session.php");
include("../include/checksession.php");
include("../include/ldisp_config.php");
include("../include/function.php");
include("../include/static_data.php");
require_once("../include/PDO.class.php");


if (isset($user_id)) {
    $nacl = nacl($user_id);
    if ($nacl != $user_nacl) {
        $data['status']  = 'error';
        $data['message'] .= "ошибка авторизации";
        echojson($data);
    }
} else {
    $data['status']  = 'error';
    $data['message'] .= "Не получен User ID";
    echojson($data);
}
$inputJSON = file_get_contents('php://input');
$input     = json_decode($inputJSON, TRUE);

$DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);

if (isset($input['action'])) {

    if ($input['action'] == "loadstartdate") {
        $configstart = loadstartdate();
        echojson($configstart);
    }
    if ($input['action'] == "loadadress") {
        $configstart = loadadress();
        echojson($configstart);
    }
    $data['status']  = 'error';
    $data['message'] = "Данные не распознаны";
    echojson($data);
} else {
    $data['status']  = 'error';
    $data['message'] = "Не достаточно данных . Код ошибки loadconfig1";
    echojson($data);
}

function loadadress()
{
    global $DB;
    $city = $DB->query("SELECT id, city_name  FROM lift_city WHERE vis_city=0");
    
    $street = $DB->query("SELECT id,street_name,city_id FROM lift_street WHERE vis_street=0");

    $home             = $DB->query("SELECT id,home_name,street_id FROM lift_home WHERE vis_home=0");
   
    $lift             = $DB->query("SELECT id,object_name,home_id FROM lift_object WHERE vis_object=0");
    $result['city']   = $city;
    $result['street'] = $street;
    $result["home"]   = $home;
    $result['lift']   = $lift;
    //SELECT `id``city_name``vis_city` FROM `lift_city` WHERE 1
    //SELECT `id``street_name``city_id``vis_street` FROM `lift_street` WHERE 1
    //SELECT `id``home_name``street_id``vis_home` FROM `lift_home` WHERE 1
    //SELECT `id``object_name``home_id``vis_object` FROM `lift_object` WHERE 1

    $data['status']  = 'ok';
    $data['message'] = $result;
    return $data;

}
function loadstartdate()
{
    global $repair_time;
    $res                = array();
    $res['department']  = queryarr("1");
    $res['request']     = queryarr("2");
    $res['group']       = queryarr("3");
    $res['repair_time'] = $repair_time;
    $res['staff']       = queryarr("5");
    $data['status']     = 'ok';
    $data['message']    = $res;
    return $data;



}

