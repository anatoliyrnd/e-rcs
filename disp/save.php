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
        logsave($log, "save_php_error");
        $response['status']  = 'error';
        $response['message'] .= "ошибка авторизации";
        echojson($response);
    }
} else {
    $log = " user_id error ";
    logsave($log, "save_php_error");
    $response['status']  = 'error';
    $response['message'] .= "Не получен User ID";
    echojson($response);
}



$inputJSON = file_get_contents('php://input');
$input     = json_decode($inputJSON, TRUE);

if (isset($input['action'])) {
    switch ($input['action']) {
        case 'callclose':
            Call_close($input);
            break;
        case 'calledit':
            Call_edit($input);
            break;
        case 'callnew':
            Call_new($input);
            break;
        case 'callnote':
            Call_note($input);
            break;
        default:
            $log = " $user_id  - uncknow action -" . $input['action'];
            logsave($log, "save_php_error");
            $response['status'] = 'error';
            $response['message'] .= "Не известный идентификатор данных";
            echojson($response);
            break;
    }

} else {
    $log = " $user_id  - not action -";
    logsave($log, "save_php_error");
    $response['status']  = 'error';
    $response['message'] .= "Не получен идентификатор данных ";
    echojson($response);

}
// проверка прав на выполняемую операци. для пользователя. 

function Call_close($data)
{
    global $user_name;
    $call_id = Chek_callId($data);
    //обработка команды закрытия заявки
    $DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);
    //Запрос на проверку закрыта или нет заявка перед закрытием $exit = "Заявка уже была закрыта сотрудником -call_last_name - date(call_date2) с решением : call_solution";
    $query_check_close = "SELECT call_solution, call_date2, call_last_name, call_status from lift_calls  WHERE (call_status = 1 AND call_id = $call_id)  LIMIT 1;";
    //Запрос на закрытие заявки
    $call_date2       = strtotime(date('Y-m-d H:i:s '));
    $query_call_close = "UPDATE lift_calls SET call_status=1, call_date2=$call_date2, call_solution=:solution, call_last_name=:user_name WHERE call_id = :call_id;";
    $result           = $DB->query($query_check_close);
    if (count($result)) {
        $date                = date("d-m-Y H:m", $result['call_date2']);
        $response['status']  = 'error';
        $response['message'] = "Заявка с № $call_id была закрыта  $date  " . $result[0]['call_last_name'] . " решение - " . $result[0]['call_solution'];
        echojson($response);

    }
    if (isset($data['call_close'])) {
        $solution = $data['call_close'];
    } else {
        $response['status']  = 'error';
        $response['message'] = "Не передано решение по заявке";
        echojson($response);
    }
    $querydata = array('solution' => $solution, 'user_name' => $user_name, 'call_id' => $call_id);
    $result    = $DB->query($query_call_close, $querydata);
    if ($result) {
        $DB->closeConnection();
        $resultnote = note_to_arhiv_close($call_id);
        if ($resultnote) {
            $response['status']  = 'ok';
            $response['message'] = "Заявка закрыта, вся история по заявке сохранена";
            echojson($response);
        } else {
            $log = " error sql history $resultnote ";
            logsave($log, "save_php_error");
            $response['status']  = 'error';
            $response['message'] = "Заявка закрыта, но при записи в базу данных истории произошла ошибка номер SC-2";
            echojson($response);
        }
    } else {
        $log = " SC-1 call close $result ";
        logsave($log, "save_php");
        $response['status']  = 'error';
        $response['message'] = "При записи в базу данных произошла ошибка номер SC-1";
        echojson($response);
    }

}
function Call_edit($data)
{ //
    /* 
    call_department,call_request,call_group -> id
    call_details - txt
    call_staff->id
    call_staff_status-> 0-false 1 true
    call_staff_date ->unix time
    expected_repair_time ->unix time дата предполагаемого ремонта
    "call_id"  "details" "repair_time_id"  "department_id"  "request_id" "group_id"   "staff_id"   "staff_status"  "action" 
    */
    

    $call_id = Chek_callId($data);
    //обработка изменения заявки
    global  $repair_time_unix, $repair_time, $user_id;
    //проверка на возможность редактировать заявки
    $DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);
    $queryuser = $DB->row("SELECT user_level, user_name, user_disppermission FROM lift_users WHERE user_id=$user_id LIMIT 1");
    $user_name=$queryuser['user_name'];
    if ($queryuser['user_disppermission'] != "1" && $queryuser['user_level'] !== "3") {
        $log = " error edit permisssion   user id $user_id - " . print_r($queryuser);
        logsave($log, "save_php_error");
        $response['status']  = 'error';
        $response['message'] = "Недостаточно прав для данного действия Edit-1  ";
        echojson($response);
    }
    $check_call_status=$DB->single("SELECT `call_status` FROM `lift_calls` WHERE`call_id`=$call_id");
    if ((bool)$check_call_status){
        $log = " error - edit close call   user id $user_id - ";
        logsave($log, "save_php_error");
        $response['status']  = 'error';
        $response['message'] = "Попытка изменить уже закрытую заявку ";
        echojson($response);
    }
    $query      = '';
    $arrayquery = [];
    $history    = $user_name . " - внес(ла) следующие изменения:";

    

    foreach ($data as $key => $value) {
        if ($key === "repair_time_id") {

            $index_time       = (int) $value;
            $repair_time_save = $repair_time_unix[$index_time];
            $history .= " <b>Cрок предполагаемого ремонта</b> - " . $repair_time[$index_time];
            //echo $index." - " . $index_time. "-"
            $query .= ", expected_repair_time=$repair_time_save";
            continue;
        }
        if ($key === "staff_status") {
            $query .= ", call_staff_status=" . (int) $value;
            // изменим дату уведомления ответственного
            if ((bool) $value) {
                $history .= " Ответственный уведомлен. " . strtotime(date('Y-m-d H:i:s '));
                $query .= " , call_staff_date=" . strtotime(date('Y-m-d H:i:s '));
            } else {
                $history .= " Ответственный НЕ уведомлен. ";
                $query .= " , call_staff_date=0";
            }

            continue;
        }
        if ($key === "details") {
            $details               = magicLower($value);
            $query .= ", call_details=:details";
            $arrayquery['details'] = $details;
            $history .= " <b>Описание заявки</b> -" . $details;
            continue;
        }
        if ($key === "department_id") {
            $query .= ", call_department=" . (int) $value;
            $name    = $DB->single("SELECT type_name FROM lift_types WHERE type_id=" . (int) $value);
            $history .= "<b> Отдел</b> -" . $name;
            continue;
        }
        if ($key === "request_id") {
            $query .= ", call_request=" . (int) $value;
            $name    = $DB->single("SELECT type_name FROM lift_types WHERE type_id=" . (int) $value);
            $history .= " <b>Уровень </b>-" . $name;
            continue;
        }
        if ($key === "group_id") {
            $query .= ", call_group=" . (int) $value;
            $name    = $DB->single("SELECT type_name FROM lift_types WHERE type_id=" . (int) $value);
            $history .= " <b>Группа</b> -" . $name;
            continue;
        }
        if ($key === "staff_id") {
            $query .= ", call_staff=" . (int) $value;
            $name    = $DB->single("SELECT user_name FROM lift_users WHERE user_id=" . (int) $value); //получим имя ответсвенного по его id);
            $history .= " <b>Назначен ответственный</b> -" . $name;
            $sendtelega=true;
            $staff=(int) $value;
            continue;
        }
    }
    $query       = substr($query, 1);
    $queryedit   = "UPDATE `lift_calls` SET $query  WHERE call_id=$call_id";
    $result_edit = $DB->query($queryedit, $arrayquery);
    if ($result_edit) {
        // запишим событие в журнал истории по заявке
        $history_date = strtotime(date('Y-m-d H:i:s '));
        $historysql   = $DB->query("INSERT INTO lift_history (history_date,history_info, call_id) VALUES( $history_date, :sethistory, $call_id );", array("sethistory" => $history));
        if ($historysql) {
            $history_save = " и история сохранена";
        } else {
            $log = " history save error $historysql ";
            logsave($log, "save_php");
            $history_save = "и история НЕ сохранена";
        }
        if ($sendtelega){
            //если изменили ответвенного, то сообщим ему в телеграмм о новой заявке
            sendtelega($staff,$call_id);
        }
        $response['status']  = 'ok';
        $response['message'] = "Заявка с №$call_id изменена $history_save";
        echojson($response);
    } else {
        $log = " SE-1 edit call $result_edit sql - $queryedit ";
        logsave($log, "save_php");
        $response['status']  = 'error';
        $response['message'] = "При записи в базу данных произошла ошибка номер SE-1";
        echojson($response);
    }
}

function Call_new($data)
{
    $sendtelega = false;
    $DB         = new PDODB(db_host, DBPort, db_name, db_user, db_password);
    //обработка создания новой заявки
    /*
    
    */
    global $user_id;
    $queryuser = $DB->row("SELECT user_add_call, user_level, user_name FROM lift_users WHERE user_id=$user_id LIMIT 1");
    if ($queryuser['user_add_call'] != "1" and $queryuser['user_level'] !== "3") {
        $log = " error new permisssion   user id $user_id - " . print_r($queryuser);
        logsave($log, "save_php_error");
        $response['status']  = 'error';
        $response['message'] = "Недостаточно прав для данного действия  ";
        echojson($response);
    }
    $data_key = ["city", "street", "home", "object", "fullAdress", "group", "request", "repair_time", "department", "details"];
    foreach ($data_key as $key => $value) {
        if (!isset($data[$value])) {
            $log = " error new call  no " . $value . " -" . print_r($data);
            logsave($log, "save_php_error");
            $response['status']  = 'error';
            $response['message'] = "Нарушена целостность данных  ";
            echojson($response);
        }
    }
    $date_call = strtotime(date('Y-m-d H:i:s '));
    if (isset($data['staff'])) {
        // если выбрали ответсвенного 
        $staff          = (int) $data['staff'];
        $query_add_call = "call_staff=$staff,";
        if (isset($data['staff_status']) and ((bool) $data['staff_status'])) {
            //если его статус уведомлен
            $query_add_call .= "call_staff_status=1, call_staff_date=$date_call,";
        } else {
            $sendtelega = true;
        }
    }


    $call_department = (int) $data['department'];
    $user_name       = $queryuser['user_name'];
    $call_request    = (int) $data['request'];
    $call_group      = (int) $data['group'];
    $city            = (int) $data['city'];
    $street          = (int) $data['street'];
    $home            = (int) $data['home'];
    $lift            = (int) $data['object'];
    $bindarray       = array(
        "adress" => $data['fullAdress'],
        "details" => $data['details'],
    );
    $query_add_call .= "call_date=$date_call, call_status=0, call_solution=' ',call_first_name='$user_name', call_department=$call_department, call_request=$call_request, call_group=$call_group, call_adres=:adress, call_details=:details, address_city=$city, address_street=$street, address_home=$home, address_lift=$lift";
    $query_add_call  = "INSERT INTO lift_calls SET " . $query_add_call;
    $add_sql_result  = $DB->query($query_add_call, $bindarray);
    if ($add_sql_result) {
        $call_id = $DB->lastInsertId();
        $DB->closeConnection();
        if ($sendtelega) {
            sendtelega($staff, $call_id);
        } //если есть ответсвенный, то  киним сообщение в телегу
        $response['status']  = 'ok';
        $response['message'] = "Заявка сохранена  ";
        echojson($response);
    } else {
        $log = " error new call  sql add error " . $add_sql_result . " -" . $query_add_call;
        logsave($log, "save_php_error");
        $response['status']  = 'error';
        $response['message'] = "Фатальная ошибка - заявка не добавлена ";
        echojson($response);
    }
}
function Call_note($data)
{
    global $user_id;
    $call_id = Chek_callId($data);
    //обработка добавления заметки к заявке
    $DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);
    if (!isset($data['note']) && (mb_strlen($data['note'], 'UTF-8')<5)){
 $log = " error note body no  " ;
        logsave($log, "save_php_error");
        $response['status']  = 'error';
        $response['message'] = "Не получена заметка или ее длина менее 5 символов ";
        echojson($response);  
    }  
    $note_body=magicLower($data['note']);//проверим на капслок
    $note_post_date = strtotime(date('Y-m-d H:i:s '));
    $bind_array=[
        'note_type'=>"1",
         'note_title'=>"Заметка",
         'note_body'=>$note_body,
         'note_relation'=>$call_id,
         'note_post_date'=>$note_post_date,
         'note_post_ip'=>$_SERVER['REMOTE_ADDR'],
         'note_post_user'=>$user_id
      ];

    $query_note="INSERT INTO lift_notes(note_type,note_title,note_body,note_relation,note_post_date,note_post_ip,note_post_user) VALUES( :note_type,:note_title,:note_body,:note_relation,:note_post_date,:note_post_ip,:note_post_user);";
    $note_result=$DB->query($query_note,$bind_array);
    if($note_result){
        $response['status']  = 'ok';
        $response['message'] = "Заметка добавлена ";
        echojson($response);  
    }else{
        $log = " error note body sql  ".$note_result ;
        logsave($log, "save_php_error");
        $response['status']  = 'error';
        $response['message'] = "Не получена заметка или ее длина менее 5 символов ";
        echojson($response);  
    }

}

function sendtelega($staff, $call_id)
{
    $DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);
    //$query.="call_staff=$staff,";//ответсвенный по заявке
    $queryuser    = "SELECT user_name FROM lift_users WHERE user_id=" . $staff; //получим имя ответсвенного по его id
    $name         = $DB->single($queryuser);
    $text         = $call_id . $name;
    $md5call      = md5($text); // создадим токен из ид заявки и имени ответсвенного
    $query_token  = " UPDATE lift_calls SET read_md5='$md5call' WHERE call_id=$call_id";
    $token_result = $DB->query($query_token);
    if ($token_result) {
        global $HOSTURL;
        staff_call_new($staff, $HOSTURL . "/viewcalluser.php?callid=" . $call_id . "&token=" . $md5call);
    }

}


function Chek_callId($data)
{
    //проверим передали ли нам id заявки
    if (isset($data['call_id'])) {
        return (int) $data['call_id'];
    } else {
        $log = " error Chek_callId  " . $data['call_id'];
        logsave($log, "save_php_error");
        $response['status']  = 'error';
        $response['message'] .= "Не получен номер заявки ";
        echojson($response);
    }
}
function echojson($data)
{
    header('Content-type: application/json');
    echo json_encode($data);
    exit();

}

function note_to_arhiv_close($call_id)
{
    $DB = new PDODB(db_host, DBPort, db_name, db_user, db_password);
    //если все ок и заявка закрыта , то перенесем все историю по заявке в ячейку в таблице
    $query     = "SELECT `history_date`,`history_info` FROM `lift_history` WHERE `call_id`=$call_id";
    $sthistory = $DB->query($query);
    $num       = 0;
    $text      = '';
    foreach ($sthistory as $value) {
        $datehistory = date("d.m.Y@H:i", $value['history_date']);
        $num++;
        $text .= "Дата изменений:$datehistory -" . $value['history_info'] . "<hr>";
    }

    if ($num) {
        $query  = "UPDATE lift_calls SET call_fullhistory=:text WHERE call_id=$call_id ";
        $update = $DB->query($query, array("text" => $text));
        if ($update) {
            return true;

        } else {
            $log = " error sql history update  $update";
            logsave($log, "save_php_error");
            return false;
        }

    } else {
        return true;
    }


}