<?php
require_once("./include/autoload.php");
$action=false;
$inputJSON = file_get_contents('php://input');
$input     = json_decode($inputJSON, TRUE);
$user_id=$input['userId']??0;
$nacl=$input['nacl']??0;
$type=$input['type']??false;
$main = new mainSRC\main();

$main->checkSession();
if (!$main->checkUser($user_id,$nacl)){
    $message=array("status"=>"error","message"=>"ошибка авторизации");
    $main->echoJSON($message);
    exit();
}
$test=array();
sleep(1);
if ($type==="setting"){
    $test=  EchoSettingParameters();

}elseif ($type==="address") {
 $test=echoCity();
}elseif ($type==="users") {
    $test[]=array("action"=>"users","user_id"=>"1","user_name"=>"uname1","user_admin"=>true) ;
    $test[]=array("action"=>"users","user_id"=>"2","user_name"=>"uname2","block"=>true) ;
}

$main->echoJSON(array("status"=>"ok","message"=>$test));
function EchoSettingParameters(){
    global $main,$user_id;
   $permission= $main->getUserPermission($user_id);
   if (!$permission[8]) {
       $result[]=array("error"=>true,"value" => "недостаточно прав", "name" => "Ошибка");
       return $result;
       }
     $query="SELECT * FROM lift_options WHERE change_allowed=1";
    $result_query=$main->DB->query($query);
    foreach ($result_query as $params){
        $params=$main->replaceArrayKey($params,"comment","name");
        $result[]=$main->replaceArrayKey($params,"option_value","value");
    }
   return $result;
   }
function echoCity(){
   $address_list=new \mainSRC\loadStartData();
    $address_list->loadAddress(true);

}