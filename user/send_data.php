<?php
include("../include/session.php");
include("../include/checksession.php");
include("../include/ldisp_config.php");
include("../include/function.php");
// файл формирования данных для мобильной версии ответвенного v.2.0
//Zamotaev A.N.
 if(!(isset($user_id)) || (!isset($user_nacl)) )
{
  $data = [ 'status' => 'Ошибка авторизации  1 '];
header('Content-type: application/json');
echo json_encode( $data );
 exit();
}



DB::$dsn = db_PDO;
DB::$user = db_user;
DB::$pass = db_password;

$opencalls = DB::getAll("SELECT call_id, call_date, call_adres, call_staff_status, call_details FROM lift_calls WHERE call_staff=:ID AND call_status=0", array('ID' => $user_id));
$date = strtotime("-30 day");
$closecalls = DB::getAll("SELECT call_id, call_date, call_date2, call_adres, call_solution, call_details FROM lift_calls WHERE call_staff=:ID AND call_status!=0 AND call_date2 >= $date ORDER BY call_date2 DESC", array('ID' => $user_id));
//var_dump($opencalls);
$notebody = '';
$data["status"] = "ok";
if (is_array($opencalls)) {
  foreach ($opencalls as $value) {
    $notebody = '';
    $idcalls = $value['call_id'];
    $notes = DB::getAll("SELECT `note_body`,`note_type` FROM `lift_notes` WHERE `note_relation`=?", $value['call_id']);

    if (is_array($notes)) {
      foreach ($notes as $value_note) {

        if ($value_note["note_type"] == "1") {
          $notebody .= $value_note["note_body"] . "<hr>";

        }
      
      }
    }

    $data["notes"][] = [
      "call_id" => $value['call_id'],
      "notes" => $notebody
    ];
  }
}


$data["openCalls"] = $opencalls;
$data["closeCalls"] = $closecalls;
echo json_encode($data);
//var_dump($items);
?>