<?php //build data array

require_once ("../include/autoload.php");
include("../include/ldisp_config.php");
use database\PDODB;
use includes\main;

$DB   = new PDODB(db_host, DBPort, db_name, db_user, db_password);
$main_function= new main($DB);
$main_function->check_session();
//require_once("../include/session.php");
//require_once("../include/checksession.php");

include("../include/function.php");
//require_once("../include/PDO.class.php");


 $main_function->check_user();
 $user_id = (int)$main_function->getUserId();

if (isset($_REQUEST['data'])) {
  $action = $_REQUEST['data'];
} else {
    $main_function->echojson(Array('status' => 'error', 'message' => 'Не передан запрос'));
}
$data = [];
$date_close=time() - 86400;
if ($action == "open") {
  if ($user_data['user_read_all_calls'] || $user_data['user_localadmin'] || $user_data['user_level'] == 3) {
    $callsQuery = "SELECT *  from lift_calls  WHERE (call_status = 0)  order by call_id desc;";
  } else {
    $callsQuery = "SELECT *  from lift_calls  WHERE (call_status = 0 AND call_staff = $user_id)  order by call_id desc;";
  }
} else if ($action == "close") {
  if ($user_data['user_read_all_calls'] || $user_data['user_localadmin'] || $user_data['user_level']==3)
  { $allviever = "";}else{
    $allviever=" AND (call_staff = $user_id)";
  }
  $callsQuery = "SELECT *  from lift_calls  WHERE (call_status = 1) AND (call_date2 >=$date_close) $allviever  order by call_id desc;";
} else {
  echo "ошибка";
  exit();
}

$calls_DB = $DB->query($callsQuery);
//return JSON formatte;
foreach ($calls_DB as $key => $value) {
  //date - дата открытия 
  $call = [
    'open_name' => $value['call_first_name'],
    'date' => date("d.m.Y@H:i", $value['call_date']),

    'close_name'=>$value['call_last_name'],
    'close_date'=>date("d.m.Y@H:i", $value['call_date2']),
    'id' => $value['call_id'],
    'adress' => $value['call_adres'],
    'repair_time' => date("d.m.Y@H:i", $value['expected_repair_time']),
    'details' => $value['call_details'],
    'staff_id' => $value['call_staff'],
    'department_id' => $value['call_department'],
    'group_id' => $value['call_group'],
    'request_id' => $value['call_request'],
    
    
  ];
  $staff_status_type=$text_call_staff_status[$value['call_staff_status']];
  if ($value['call_staff_date']) {
    $call['staff_date'] = date("d.m.Y@H:i", $value['call_staff_date'])." - ".$staff_status_type;
  } else {
    $call['staff_date'] = "Не уведомлен";
  }

  if ($action == "open") {
    $call['type']="open";
    $call['staff_status'] = (bool) $value['call_staff_status'];
  }
  if ($action == "close") {
    $call['type']="close";
    $call['solution']=$value['call_solution'];
   
  }
  if ($user_id == $value['call_staff']) {
    $call['closureallowed'] = 1;
  }
  $queryDepartment = "SELECT type_name FROM lift_types WHERE type_id=" . $value['call_department']." LIMIT 1"; // название отдела
  $queryGroup      = "SELECT type_name FROM lift_types WHERE type_id=" . $value['call_group']." LIMIT 1";
  $queryRequest    = "SELECT type_name FROM lift_types WHERE type_id=" . $value['call_request']." LIMIT 1"; // название уровня заявки
  $queryNotes      = "SELECT * from lift_notes WHERE (note_relation =" . $value['call_id'] . ");";
  if ($value['call_staff']) {
    $queryUser     = "SELECT user_name FROM lift_users WHERE user_id=" . $value['call_staff']; //получим имя ответсвенного по его id
    $userName      = $DB->single($queryUser);
    $call['staff'] = $userName;
  } else {
    $call['staff'] = 'Не назначен';
  }  
  $call['group']      = $DB->single($queryGroup);
  $call['department'] = $DB->single($queryDepartment);

  $call['request']    = $DB->single($queryRequest);
  if ($action=="close"){
    $call['full_history']=$value['call_fullhistory'];
  }
  $noteList           = $DB->query($queryNotes);
  $num = 0;
  foreach ($noteList as $key => $note_query) {
    $query_user_note = "SELECT user_name FROM lift_users WHERE user_id=" . $note_query['note_post_user']; //получим имя ответсвенного по его id
    $user_name_note  = $DB->single($query_user_note);
    $note            = ['user' => $user_name_note, 'body' => $note_query['note_body'], 'date' =>
      date("d.m.Y H:i", ($note_query['note_post_date'])),'type'=>$note_query['note_type'],"id"=>$note_query['note_id']];
    $num++;
    $call['note'][$num] = $note;
  }
  $call['note_num'] = $num;
  if ($num){$call['other']="Есть заметки ($num)";}
  array_push($data, $call);
  }
echo (json_encode($data));
