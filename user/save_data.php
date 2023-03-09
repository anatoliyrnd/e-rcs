<?php
include("../include/session.php");
include("../include/checksession.php");
include("../include/ldisp_config.php");
include("../include/function.php");
// gполучение данных из мобильньй версии интерфейса тветственного v.2.0
//Zamotaev A.N.
sleep(6);
if (!isset($_REQUEST['nacl']) or !isset($user_id)) {
    $data = ['status' => 'Ошибка авторизации  1 '];
    header('Content-type: application/json');
    echo json_encode($data);
    exit();
}
if ($_REQUEST['nacl'] != nacl($user_id)) {

    $data = ['status' => 'Ошибка авторизации 3'];
    header('Content-type: application/json');
    echo json_encode($data);
    exit;

}
if (!isset($_REQUEST['action'])) {
    $data = ['status' => 'Ошибка данных  2 '];
    header('Content-type: application/json');
    echo json_encode($data);
    exit();
}

if (isset($_REQUEST['call_id'])) {
    $call_id = $_REQUEST['call_id'];
} else {
    $data = ['status' => 'Ошибка данных  3 '];
    header('Content-type: application/json');
    echo json_encode($data);
    exit();
}
$action      = $_REQUEST['action'];
$curent_time = time();
$ip          = $_SERVER['REMOTE_ADDR'];
switch ($action) {
    case 'read': {
            DB::$dsn  = db_PDO;
            DB::$user = db_user;
            DB::$pass = db_password;
            $read     = DB::set("UPDATE lift_calls SET call_staff_status=2, call_staff_date=$curent_time WHERE call_id=:ID", array('ID' => $call_id));
            if ($read) {
                $data = ['status' => 'ok'];
                header('Content-type: application/json');
                echo json_encode($data);
                exit();
            }
        }
        break; // end read
    case 'addNote':
        if (isset($_REQUEST["note"])) {
            $note = $_REQUEST["note"];
            if (iconv_strlen($note) <= 5) {
                $data = ['status' => 'Заметка короткая!'];
                header('Content-type: application/json');
                echo json_encode($data);
                exit();
            } else {
                DB::$dsn   = db_PDO;
                DB::$user  = db_user;
                DB::$pass  = db_password;
                $notequery = DB::set("INSERT INTO `lift_notes` SET `note_body`=:note, `note_relation`=:ID, `note_type`=1, `note_post_date`=$curent_time, `note_post_ip`='$ip', `note_post_user`=:userid", array('ID' => $call_id, 'note' => $note, 'userid' => (int) $user_id));
                if ($notequery) {
                    $data = ['status' => 'ok'];
                    header('Content-type: application/json');
                    echo json_encode($data);
                    exit();
                } else {
                    $data = ['status' => 'Ошибка базы'];
                    header('Content-type: application/json');
                    echo json_encode($data);
                    exit();
                }
            }
        }
        break;

    case 'callClose':
        $solution = $_REQUEST["solution"];
        if (iconv_strlen($solution) <= 5) {
            $data = ['status' => 'Длина менее 5 символов!'];
            header('Content-type: application/json');
            echo json_encode($data);
            exit();
        } else {

            DB::$dsn    = db_PDO;
            DB::$user   = db_user;
            DB::$pass   = db_password;

            $callstatus = DB::getValue("SELECT call_status FROM lift_calls WHERE call_id=:ID LIMIT 1", array("ID" => $call_id));

            if ($callstatus == "1") {
                $data = ['status' => 'Заявка уже закрыта'];
                header('Content-type: application/json');
                echo json_encode($data);
                exit();
            }
            $callquery = DB::set("UPDATE `lift_calls` SET call_date2=$curent_time, call_solution=:solution, call_last_name=:username, call_status=1 WHERE call_id=:ID", array('ID' => $call_id, 'solution' => $solution, 'username' => $user_name));
            if ($callquery) {
                $data = ['status' => 'ok'];
                header('Content-type: application/json');
                echo json_encode($data);
                //если все ок и заявка закрыта , то перенесем все историю по заявке в ячейку в таблице
                $query     = "SELECT history_date,history_info FROM lift_history WHERE call_id=:ID";
                $sthistory = DB::getAll($query, array('ID' => $call_id));

                $text      = '';
                if (is_array($sthistory)) {
                    foreach ($sthistory as $value) {
                        $datehistory = date("d.m.Y@H:i", $value['history_date']);
                        $text .= "Дата изменений:$datehistory -" . $value['history_info'] . "<br>";
                    }
                    $update = DB::set("UPDATE lift_calls SET call_fullhistory=:text WHERE call_id=:ID", array('ID' => $call_id, 'text'=>$text));
                    if ($update) {
                        //если переписали всю историю по закрытой заявке в таблицу к заявкам, то можно смело удалять из таблицы истории
                        DB::set("DELETE FROM `lift_history` WHERE  `call_id`=:ID", array('ID' => $call_id));
                    }
                }

                exit();
            } else {
                $data = ['status' => 'Ошибка базы'];
                header('Content-type: application/json');
                echo json_encode($data);
                exit();
            }
        }

    default:

        break;
}