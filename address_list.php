<?php



require_once("./include/autoload.php");
use mainSRC\loadStartData;
$action=false;
$inputJSON = file_get_contents('php://input');
$input     = json_decode($inputJSON, TRUE);
$user_id=$input['userId']??0;
$nacl=$input['nacl']??0;
$type=$input['type']??false;
$main = new loadStartData();

if (!$main->checkUser($user_id,$nacl)){
    $message=array("status"=>"error","message"=>"ошибка авторизации");
    $main->echoJSON($message);
    exit();
}
if ($input['type']==="address_list"){

}
function generateAddressList($city_id){
    global $main;

}