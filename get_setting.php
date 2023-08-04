<?php
require_once("./include/autoload.php");
$action=false;
$inputJSON = file_get_contents('php://input');
$input     = json_decode($inputJSON, TRUE);
$user_id=$input['userId']??0;
$nacl=$input['nacl']??0;
$type=$input['type']??false;
$main = new mainSRC\main();
$address_list=new \mainSRC\loadStartData();
$main->checkSession();
if (!$main->checkUser($user_id,$nacl)){
    $message=array("status"=>"error","message"=>"ошибка авторизации");
    $main->echoJSON($message);
    exit();
}
$test=array();
sleep(1);
switch ($type) {
    case "setting":

        $test=  EchoSettingParameters();
        break;
    case "address":
        $test = $address_list->loadAddress(true);
        break;
    case "users":
        $test=EchoUsers();
        break;
    case "logs":
        echo '';
        break;
    case "report":
        echo ' ';
        break;
    default:
        echo "    ";
}



$main->echoJSON(array("status"=>"ok","message"=>$test));
/**
 * @return array
 */
function EchoUsers(){
    global $main,$user_id;
    $permission= $main->getUserPermission($user_id);
    if(!$permission[6]){
        $result[]=array("error"=>true,"value" => "недостаточно прав", "name" => "Ошибка");
        return $result;
    }
    $users['data']=list_users();
    $users['descriptions']=users_row_description();
    return $users;
}

/**
 * @return array
 */
function users_row_description(){
    global $main;
    $query_name = "SELECT name_row, type,text,description,classification,editable,display_order FROM users_description_row where 1 ORDER BY display_order";
    $name_result=$main->DB->query($query_name);

    foreach ( $name_result as $value){
        $name=$value['name_row'];
        unset($value['name_row']);
        $result[$name]=$value;
    }
    return $result;
}
function list_users(){
    global $main;
    $query_name = "SELECT  name_row FROM users_description_row where 1";
    if(!$result_query_name = $main->DB->column($query_name)) return false;
    //$result_query_name['user_password'] =array_diff($result_query_name,array('user_password'));
    $select=implode(",",$result_query_name).",user_id ";
    $query_users_info="SELECT $select FROM lift_users WHERE 1";
    if(!$result_query_users = $main->DB->query( $query_users_info)) return false;
    foreach ($result_query_users as $key=>$value){
        $result_query_users[$key]['user_password']='';
    }
    return $result_query_users ;
}
function EchoSettingParameters(){
    global $main,$user_id;
   $permission= $main->getUserPermission($user_id);
   if (!$permission[8]) {

       }
     $query="SELECT * FROM lift_options WHERE change_allowed=1";
    $result_query=$main->DB->query($query);
    foreach ($result_query as $params){
        $params=$main->replaceArrayKey($params,"comment","description");
        $result[]=$main->replaceArrayKey($params,"option_value","value");
    }
   return $result;
   }
