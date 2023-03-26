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
        logsave($log, "editObjControl_php_error");
        $response['status']  = 'error';
        $response['message'] .= "ошибка авторизации";
        echojson($response);
    }
} else {
    $log = " user_id error ";
    logsave($log, "editObjControl_php_error");
    $response['status']  = 'error';
    $response['message'] .= "Не получен User ID";
    echojson($response);
}
$DB              = new PDODB(db_host, DBPort, db_name, db_user, db_password);
$queryuser       = $DB->row("SELECT user_level, user_name, user_localadmin FROM lift_users WHERE user_id=$user_id LIMIT 1");
$user_level      = (int) $queryuser['user_level'];
$user_localadmin = (int) $queryuser['user_localadmin'];
if ($user_level and !$user_localadmin) {
    $log = " user_id доступ запрещен id $user_id  level $user_level admin $user_localadmin имя " . $queryuser['user_name'];
    logsave($log, "editObjControl_php_error");
    $response['status']  = 'error';
    $response['message'] .= "Не достаточный уровень доступа";
    echojson($response);
}

const type = ["object_delete", "home_delete", "street_delete", "city_delete"];
$inputJSON = file_get_contents('php://input');
$input     = json_decode($inputJSON, TRUE);
if (isset($input['action'])) {
    switch ($input['action']) {
        case 'object_delete':
            object_echo();
            break;
        case 'home_delete':
            home_echo();
            break;
        case 'street_delete':
            street_echo();
            break;
        case 'city_delete':
            city_echo();
            break;
        case 'delete':
            delete();
            break;
        case 'adressEdit':
            adressEdit();
            break;
        case 'visableObject':
            visableObject();
            break;
        case 'saveObject':
            saveObject();
            break;
        default:
            $log = " $user_id  - uncknow action -" . $input['action'];
            logsave($log, "editObjControl_php_error");
            $response['status'] = 'error';
            $response['message'] = "Не известный идентификатор данных";
            echojson($response);
            break;
    }
} else {
    $log = " $user_id  - not action -";
    logsave($log, "editObjControl_php_error");
    $response['status']  = 'error';
    $response['message'] = "Не получен идентификатор данных ";
    echojson($response);
}
// проверка прав на выполняемую операци. для пользователя. 
function delete()
{
    global $input, $DB;
    sleep(1);

    if (!isset($input['type']) and !isset($input['id'])) {
        $log = " delete data error ";
        logsave($log, "editObjControl_php_error");
        $response['status']  = 'error';
        $response['message'] .= "Недостаточно данных";
        echojson($response);
    }
    $table        = ["object" => "lift_object", "home" => "lift_home", "street" => "lift_street", "city" => "lift_city"];
    $idtable      = $input['type'];
    $id           = (int) $input['id'];
    $query_delete = "DELETE FROM " . $table[$idtable] . " WHERE id=$id";

    $res = $DB->query($query_delete);

    if ($res) {
        $response['status']  = 'ok';
        $response['message'] = "sucess";
        echojson($response);
    } else {
        $response['status']  = 'error';
        $response['message'] = "Ошибка запроса к базе данных" . $res;
        echojson($response);
    }
}
function city_echo()
{
    global $DB;
    $list_delete_possible   = array(); //массив объектов которые можно удалить
    $list_delete_impossible = array(); //массив объектов которые удалить нельзя (есть ссылки)
    $list_object            = $DB->query("SELECT `id`, `city_name` FROM `lift_city` WHERE `vis_city`=1;");

    foreach ($list_object as $value) {

        $check_street = $DB->column("SELECT `id` FROM `lift_street` WHERE `city_id`=" . $value['id'] . ";");
        if ($check_street) {
            $list_delete_impossible[$value['id']]['name']   = $value['city_name'];
            $list_delete_impossible[$value['id']]['reason'] .= "имеются ссылки на город с Улиц с  ID:" . implode(",", $check_street);
        } else {
            $list_delete_possible[$value['id']] = $value['city_name'];
        }
    }
    $data['type']       = "city";
    $data['type_name']  = " Города ";
    $data["status"]     = "ok";
    $data["possible"]   = $list_delete_possible;
    $data["impossible"] = $list_delete_impossible;
    echojson($data);
    exit();
}
function street_echo()
{
    global $DB;
    $list_delete_possible   = array(); //массив объектов которые можно удалить
    $list_delete_impossible = array(); //массив объектов которые удалить нельзя (есть ссылки)
    $list_object            = $DB->query("SELECT `id`, `street_name` FROM `lift_street` WHERE `vis_street`=1;");

    foreach ($list_object as $value) {

        $check_home = $DB->column("SELECT `id` FROM `lift_home` WHERE `street_id`=" . $value['id'] . ";");
        if ($check_home) {
            $list_delete_impossible[$value['id']]['name']   = $value['street_name'];
            $list_delete_impossible[$value['id']]['reason'] .= "имеются ссылки на улицу от домов с  ID:" . implode(",", $check_home);
        } else {
            $list_delete_possible[$value['id']] = $value['street_name'];
        }
    }
    $data['type']       = "street";
    $data['type_name']  = " Улицы ";
    $data["status"]     = "ok";
    $data["possible"]   = $list_delete_possible;
    $data["impossible"] = $list_delete_impossible;
    echojson($data);
    exit();
}
function home_echo()
{
    global $DB;
    $list_delete_possible   = array(); //массив объектов которые можно удалить
    $list_delete_impossible = array(); //массив объектов которые удалить нельзя (есть ссылки)
    $list_object            = $DB->query("SELECT `id`, `home_name` FROM `lift_home` WHERE `vis_home`=1;");

    foreach ($list_object as $value) {
        $check_id   = $DB->column("SELECT `call_id` FROM `lift_calls` WHERE `address_home`=" . $value['id'] . ";");
        $check_lift = $DB->column("SELECT `id` FROM `lift_object` WHERE `home_id`=" . $value['id'] . ";");
        if ($check_id or $check_lift) {
            $list_delete_impossible[$value['id']]['name'] = $value['home_name'];
            ($check_id) ? $list_delete_impossible[$value['id']]['reason'] .= "имеются ссылки на дом по заявкам с №:" . implode(",", $check_id) . "<br>" : null;
            ($check_lift) ? $list_delete_impossible[$value['id']]['reason'] .= "имеются ссылки на дом лифты с ID:" . implode(",", $check_lift) : null;
        } else {
            $list_delete_possible[$value['id']] = $value['home_name'];
        }
    }
    $data['type']       = "home";
    $data['type_name']  = " Дома ";
    $data["status"]     = "ok";
    $data["possible"]   = $list_delete_possible;
    $data["impossible"] = $list_delete_impossible;
    echojson($data);
    exit();
}
function object_echo()
{
    global $DB;
    $list_delete_possible   = array(); //массив объектов которые можно удалить
    $list_delete_impossible = array(); //массив объектов которые удалить нельзя (есть ссылки)
    $list_object            = $DB->query("SELECT `id`, `object_name` FROM `lift_object` WHERE `vis_object`=1;");

    foreach ($list_object as $value) {
        $check_id = $DB->column("SELECT `call_id` FROM `lift_calls` WHERE `address_lift`=" . $value['id'] . ";");
        if ($check_id) {
            $list_delete_impossible[$value['id']]['name']   = $value['object_name'];
            $list_delete_impossible[$value['id']]['reason'] = "имеются ссылки на объект по заявкам с №:" . implode(",", $check_id);
        } else {
            $list_delete_possible[$value['id']] = $value['object_name'];
        }
    }
    $data['type']       = "object";
    $data['type_name']  = " лифт ";
    $data["status"]     = "ok";
    $data["possible"]   = $list_delete_possible;
    $data["impossible"] = $list_delete_impossible;
    echojson($data);
    exit();

}
function adressEdit()
{

    global $DB;
    $city             = $DB->query("SELECT id, city_name,vis_city  FROM lift_city  ORDER BY city_name ");
    $street           = $DB->query("SELECT id,street_name,city_id,vis_street FROM lift_street   ORDER BY street_name  ");
    $home             = $DB->query("SELECT id,home_name,street_id, vis_home FROM lift_home   ORDER BY  home_name ");
    $lift             = $DB->query("SELECT id,object_name,home_id,vis_object FROM lift_object  ORDER BY object_name  ");
    $result['city']   = $city;
    $result['street'] = $street;
    $result["home"]   = $home;
    $result['object'] = $lift;
    //SELECT `id``city_name``vis_city` FROM `lift_city` WHERE 1
    //SELECT `id``street_name``city_id``vis_street` FROM `lift_street` WHERE 1
    //SELECT `id``home_name``street_id``vis_home` FROM `lift_home` WHERE 1
    //SELECT `id``object_name``home_id``vis_object` FROM `lift_object` WHERE 1
    $data['status']  = 'ok';
    $data['message'] = $result;
    echojson($data);
    exit();
}
function visableObject()
{
    sleep(1);
    global $DB;
    global $input;
    if (isset($input['type']) and isset($input['id']) and isset($input['hidden'])) {
        $type = "lift_" . typeCheck($input['type']);
        $id   = (int) $input['id'];
        ((int) $input['hidden'] === 1) ? $hidden = 0 : $hidden = 1;
        $vis_type = "vis_" . typeCheck($input['type']) . "=$hidden";
        $query    = "UPDATE $type SET $vis_type WHERE id=$id ";
        $update   = $DB->query($query);
        if ($update) {
            $data["status"] = "ok";
            $data['hidden'] = $hidden;

            echojson($data);
            exit();
        } else {
            $data["status"]  = "error";
            $data['message'] = "Ошибка базы данных";
            echojson($data);
            exit();
        }
    } else {
        $data["status"]  = "error";
        $data['message'] = "Получены не все данные";
        echojson($data);
        exit();
    }
}
function saveObject()
{

    global $DB, $input;
    $sql_parent = '';
    $count_new  = 0;
    $count_edit = 0;
    $count_duplicate=0;
    $parent_id  = '';
    if (isset($input['type']) and isset($input['edit']) and isset($input['new'])) {
        $table_name = "lift_" . typeCheck($input['type']);
        $colum_name = typeCheck($input['type']) . "_name";

        if ($input['type'] !== "city") {
            if (isset($input['parentid'])) {
                // если не город то обязательно наличие ID родительского объекта
                $parent_id  = (int) $input['parentid'];
                $sql_parent = " , " . parentArr($input['type']) . "=" . $parent_id;
            } else {
                $data["status"]  = "error";
                $data['message'] = "Не получен ParentID";
                echojson($data);
                exit();
            }
        }
        if (count($input['new'])) {
            foreach ($input['new'] as $value) {
                //проверим по названию
                $check_text  = str_replace('.', '', $value); //уберем точки из поискового запроса что бы они не влияли на результат
                $check_text  = str_replace(' ', '', $check_text);
                $check_name=$DB->single("SELECT id FROM $table_name WHERE (REPLACE(REPLACE(`$colum_name`, ' ', ''),'.',''))=? $sql_parent", array($check_text));
                if ($check_name){
                    $count_duplicate++;
                    continue;
                }
                $new_add_result = $DB->query("INSERT INTO $table_name SET $colum_name=? $sql_parent", array($value));
                ($new_add_result) ? $count_new++ : null;
            }
        }
        if (count($input['edit'])) {
            foreach ($input['edit'] as $value) {
                $check_text  = str_replace('.', '', $value['value']); //уберем точки из поискового запроса что бы они не влияли на результат
                $check_text  = str_replace(' ', '', $check_text);
                $check_name=$DB->single("SELECT id FROM $table_name WHERE (REPLACE(REPLACE(`$colum_name`, ' ', ''),'.',''))=? $sql_parent", array($check_text));
                if ($check_name){
                    $count_duplicate++;
                    continue;
                }
                $edit_result = $DB->query("UPDATE $table_name SET $colum_name=?  WHERE id=?", array($value['value'], $value['id']));
                ($edit_result) ? $count_edit++ : null;
            }

        }
        $mes             = "error - $count_duplicate - новых -$count_new edit - $count_edit-" . count($input['new']) . "-" . count($input['edit']);
        $data["status"]  = "ok";
        $data['message'] = $mes;
        echojson($data);
        exit();

    } else {
        $data["status"]  = "error";
        $data['message'] = "Получены не все данные";
        echojson($data);
        exit();
    }
}
function typeCheck($type)
{
    switch ($type) {
        case 'city':
            return "city";
        case 'street':
            return "street";
        case 'home':
            return "home";
        case 'object':
            return "object";
        default:
            $data["status"] = "error";
            $data['message'] = "Не известный тип данных";
            echojson($data);
            exit();

    }
}
function parentArr($type)
{
    switch ($type) {
        case 'object':
            return "home_id";
        case 'home':
            return "street_id";
        case 'street':
            return "city_id";

        default:
            # code...
            break;
    }
}