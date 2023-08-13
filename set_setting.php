<?php
require_once("./include/autoload.php");

$action=false;
$inputJSON = file_get_contents('php://input');
$input     = json_decode($inputJSON, TRUE);
$user_id=$input['userId']??0;
$nacl=$input['nacl']??0;
$action=$input['action']??false;
$main = new mainSRC\main();
$users= new mainSRC\setting\users();
$address=new mainSRC\setting\address();

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
        if (!$main->getUserPermission()[6])$main->echoJSON(array('status'=>'error','message'=>'Недостаточно прав in users'));
       $users->editUser($input);
               break;
    case "addUser":
        if (!$main->getUserPermission()[6])$main->echoJSON(array('status'=>'error','message'=>'Недостаточно прав in users'));
        $users->addUser($input);
        break;
    case "editAddress":
        if (!$main->getUserPermission()[7])$main->echoJSON(array('status'=>'error','message'=>'Недостаточно прав in address'));
        $address->editAddress($input);
        break;
    case "addAddress":
        if (!$main->getUserPermission()[7])$main->echoJSON(array('status'=>'error','message'=>'Недостаточно прав in address'));
        $address->addAddress($input);
        break;
    case "setSettings":
        $permission=$main->getUserPermission($user_id);
        if(!$permission[8]) $main->echoJSON(array('status'=>'error','message'=>'Недостаточно прав in params'));
        $result=setSettings();
        break;
    default:
        echo "    ";
}


$main->echoJSON($result);
//$main->echoJSON(array("status"=>"ok","message"=>$result));

/**
 * @return string[]
 */
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
