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
    case "setSettings":
        $result=setSettings();
        break;
    default:
        echo "    ";
}


$main->echoJSON($result);
//$main->echoJSON(array("status"=>"ok","message"=>$result));

function setSettings(){
    global $main,$input;
    $message='';
    $key_setting=array('login_tries'=>'number','waiting_time'=>"number",'min_length_text'=>'number', 'authorizationKey'=>'text','telegram_token'=>'text');
    foreach ($key_setting as $index => $value) {
        $value==='number'?$data=(int)$input[$index]:$data=(string)$input[$index];
        $result = $main->DB->update("lift_options",array('option_value'=>$data),array("option_name"=>$index));
        $result?$message.="$data изменен <br>":$message.="ОШИБКА при изменении $data<br> ";
   }
  return array("status"=>"ok","message"=>$message);


    
}
