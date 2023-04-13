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
    $city = $DB->query("SELECT id, city_name  FROM lift_city WHERE vis_city=0 ORDER BY city_name ");
    
    $street = $DB->query("SELECT id,street_name,city_id FROM lift_street WHERE vis_street=0  ORDER BY street_name ");

    $home             = $DB->query("SELECT id,home_name,street_id FROM lift_home WHERE vis_home=0 ORDER BY  home_name");
   
    $lift             = $DB->query("SELECT id,object_name,home_id FROM lift_object WHERE vis_object=0 ORDER BY object_name");
    $result['city']   = $city;
    $result['street'] = $street;
    $result["home"]   = $home;
    $result['object']   = $lift;
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
    global $user_id, $DB;
$q        = "SELECT `user_add_call`, `user_localadmin`,`user_edit_obj`, `user_edit_user`, `user_level`, `user_disppermission` FROM `lift_users` WHERE `user_id`=:userId LIMIT 1";
$userdata = $DB->row($q,array("userId"=>$user_id));
$read_call=true; //0
$edit_call=false;//1
$close_call=false;//2
$note_call=true;//3
$addcallpermission=false;//4
$edituserlink = false;//5
$editobjlink = false;//6
//если админ или пользователю разрешено редктирование  объектов
if ($userdata['user_localadmin'] || $userdata['user_edit_obj']){$editobjlink = true;}
//если админ или пользователю разрешено Управление пользователями
if ($userdata['user_localadmin'] || $userdata['user_edit_user']){$edituserlink = true;}
//если диспетчер  или пользователю разрешено редктирование заявок
if ($userdata['user_disppermission'] || ($userdata['user_level'] == 3)){
    $edit_call=true;//1
    $close_call=true;//2
}

//если диспетчер  или пользователю разрешено создание заявок
if ($userdata['user_add_call'] || ($userdata['user_level'] == 3)){$addcallpermission = true;}
$stuser = null;
$nav=[$read_call,$edit_call,$close_call,$note_call,$addcallpermission,$edituserlink,$editobjlink];

    global $repair_time;
    $res                = array();
    $res['nav']=$nav;
    $res['department']  = queryarr("1");
    $res['request']     = queryarr("2");
    $res['group']       = queryarr("3");
    $res['repair_time'] = $repair_time;
    $res['staff']       = queryarr("5");
    $data['status']     = 'ok';
    $data['message']    = $res;
    return $data;



}

