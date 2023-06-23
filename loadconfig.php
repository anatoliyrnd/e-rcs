<?php
require_once("../include/autoload.php");
include_once("../include/ldisp_config.php");

use database\PDODB;
use includes\main;

$DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);
$main_function = new main($DB);
$main_function->check_session();
$main_function->check_user();
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);
$user_id = $main_function->getUserId();

if (!isset($input['action'])) {
    $data['status'] = 'error';
    $data['message'] = "Не достаточно данных . Код ошибки loadconfigerror";
    $main_function->echojson($data);
}
if ($input['action'] == "loadstartdate") {
    $configstart = loadstartdata();
    $main_function->echojson($configstart);
}
if ($input['action'] == "loadadress") {
    $configstart = loadadress();
    $main_function->echojson($configstart);
}

$data['status'] = 'error';
$data['message'] = "Данные не распознаны";
$main_function->echojson($data);


function loadadress()
{
    global $DB;
    $city = $DB->query("SELECT id, city_name  FROM lift_city WHERE vis_city=0 ORDER BY city_name ");

    $street = $DB->query("SELECT id,street_name,city_id FROM lift_street WHERE vis_street=0  ORDER BY street_name ");

    $home = $DB->query("SELECT id,home_name,street_id FROM lift_home WHERE vis_home=0 ORDER BY  home_name");

    $lift = $DB->query("SELECT id,object_name,home_id FROM lift_object WHERE vis_object=0 ORDER BY object_name");
    $result['city'] = $city;
    $result['street'] = $street;
    $result["home"] = $home;
    $result['object'] = $lift;
    //SELECT `id``city_name``vis_city` FROM `lift_city` WHERE 1
    //SELECT `id``street_name``city_id``vis_street` FROM `lift_street` WHERE 1
    //SELECT `id``home_name``street_id``vis_home` FROM `lift_home` WHERE 1
    //SELECT `id``object_name``home_id``vis_object` FROM `lift_object` WHERE 1

    $data['status'] = 'ok';
    $data['message'] = $result;
    return $data;

}

function queryarr($type = "0")
{
    global $DB;
    if ($type == "1" or $type == "2" or $type == "3") {
        //получим типы заявок 1-отдел, 2- уровни заявкиб 3- группа заявки
        $query = "select type_id,type_name from lift_types where type=" . $type . " order by type_name;";
        $id = "type_id";
        $name = "type_name";
    } else {
        if ($type == "4") {
            //получаем список городов
            $query = "SELECT city_name, id from lift_city WHERE 1 order by city_name";
            $id = "id";
            $name = "city_name";
        } else {
            //получаем список сотрудников
            $query = "select user_id,user_name from lift_users where (user_level=2 OR user_level=0) AND user_block<>1 order by user_name;";
            $id = "user_id";
            $name = "user_name";

        }
    }
    $item = array();
    $result = $DB->query($query);
    foreach ($result as $element) {
        $item[$element[$id]] = $element[$name];//пересоберем результат в ассоциативный массив ключом которого будет id а значением собственно значение
    }
    return $item;
}

function loadstartdata()
{global $main_function,$repair_time;
    $nav = $main_function->getUserPermission();
    $res = array();
    $res['nav'] = $nav;
    $res['department'] = queryarr("1");
    $res['request'] = queryarr("2");
    $res['group'] = queryarr("3");
    $res['repair_time'] = $repair_time;
    $res['staff'] = queryarr("5");
    $data['status'] = 'ok';
    $data['message'] = $res;
    return $data;


}

