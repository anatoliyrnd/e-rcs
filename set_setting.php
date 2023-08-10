<?php
require_once("./include/autoload.php");

$action=false;
$inputJSON = file_get_contents('php://input');
$input     = json_decode($inputJSON, TRUE);
$user_id=$input['userId']??0;
$nacl=$input['nacl']??0;
$action=$input['action']??false;
$main = new mainSRC\main();
$users= new \mainSRC\setting\users();
$address_list=new \mainSRC\loadStartData();
$main->checkSession();
if (!$main->checkUser($user_id,$nacl)){
    $message=array("status"=>"error","message"=>"ошибка авторизации");
    $main->echoJSON($message);
    exit();
}
$result=array();
sleep(1);
switch ($action) {
    case "editUser":
       $users->editUser($input);
               break;
    case "addUser":
        $users->addUser($input);
        break;
    case "editAddress":
        $result=editAddress();
        break;
    case "addAddres":
        $result=addAddress();
        break;
    case "editSettings":
        echo ' ';
        break;
    default:
        echo "    ";
}


$main->echoJSON($result);
//$main->echoJSON(array("status"=>"ok","message"=>$result));


