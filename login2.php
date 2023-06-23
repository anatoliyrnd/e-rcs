<?php
ob_start();

include("include/autoload.php");
use mainSRC\dataBase\PDODB;
$main=new \mainSRC\main();
const OLDHASH=true; //предидущая система хширования
const log_path="authorisation";
$DB = PDODB::getInstance();
$is_valid  = false;
$url       = $main->getHostURL() . "/index_disp.php";
$mobileurl = $main->getHostURL(). "/mobile_user.php";
$lastip    = $_SERVER['REMOTE_ADDR'];
$sqltoken  = '';
sleep(1);
session_start();
if (!isset($_SESSION['auth'])) {
$main->logSave("Ошибка авторизации отсутствует сессионный ключ auth",'error',log_path);
  $datajson = ['status' => 'error ', 'message' => 'Ошибка авторизаннции  1'];
  $main->echoJSON($datajson);
}
//limit login tries.
$login_tries=$DB->single("SELECT option_value FROM lift_options WHERE option_name='login_tries'");
if (isset($_SESSION['hit'])) {
  $_SESSION['hit'] += 1;
  if ($_SESSION['hit'] > $login_tries) {
      $main->logSave("Ошибка авторизации превышен лимит попыток ".$_SESSION['hit'],'error',log_path);
    $datajson = ['status' => 'error', 'message' => 'Доступ заблокирован '];
   $main->echoJSON($datajson);
  }
} else {
  //	$_SESSION['hit'] = 0;
}
//авторизация
$entityBody = file_get_contents('php://input');
$data       = get_object_vars(json_decode($entityBody));
$pattern = '/\s+|[^\w\s]/';
if (isset($data['webGL'])){$webgl=preg_replace($pattern, '',$data['webGL'] );}else{$webgl='undefined';}
if (!isset($data['name']) and !isset($data['token'])) {
  $datajson = ['status' => 'error', 'message' => "Ошибка данных"];
  $main->echoJSON($datajson);

}

$user_login = trim($data['name']);

if ((iconv_strlen($user_login) < 4) and !isset($data['id'])) {
  $datajson = ['status' => 'error', 'message' => "Логин короткий"];
    $main->echoJSON($datajson);
}
if (iconv_strlen($data['token']) == 32 && $webgl) {
  // если есть токен авторизации из локалного хранилища браузера  и данные webGL то проверим его на валидноть

  $query  = "select user_login, webgl_info, user_token, user_name, user_level,user_localadmin, user_block from lift_users WHERE user_id=:ID limit 1;";
    $rez=$DB->row($query,array("ID"=>$data['id']));

/*if ($rez['webgl_info']=="undefined" || strlen($rez['webgl_info']<=3)){
    $datajson = ['status' => 'error', 'message' => "ошибка браузера"];
    header('Content-type: application/json');
    echo json_encode($datajson);
    $txt = "Не верный токен  для login_id" . $data['id'] . "token sql -" . $rez['user_token'] . " token post -" . $data['token'];
    savelog($txt);
    exit();
}*/
  if ($rez['user_token'] === $data['token'] && $webgl===$rez['webgl_info'] ) {
    // автологин по токену успешно
    
    $is_valid   = true; //флаг успешности авторизации
    $user_login = $rez['user_login'];
    $checkusing = "user_login"; // для правельной авторизации по логину
  } else {
    $datajson = ['status' => 'error', 'message' => "не верный токен"];

    $txt = "Не верный токен  для login_id" . $data['id'] . "token sql -" . $rez['user_token'] . " token post -" . $data['token'];
      $main->logSave($txt,'token',log_path);
      $main->echoJSON($datajson);
  }
}
if (isset($data['pname'])) {
  $user_password = trim($data['pname']);

  $pos           = strrpos($user_login, "@");
  if ($pos === false) { // note: three equal signs 
    $checkusing = "user_login";
  } else {
    $checkusing = "user_email";
  }
    $is_valid      = checkpwd($user_password, $user_login,$checkusing);
} else {
  if (!$is_valid) {
    $datajson = ['status' => 'error', 'message' => "Введите пароль"];
    header('Content-type: application/json');
    echo json_encode($datajson);
    exit();
  }
}



if (!$is_valid) {
  //ошибка авторизации
  $_SESSION['hit'] += 1;
  $datajson        = ['status' => 'error', 'message' => 'не верное имя/пароль'];
  header('Content-type: application/json');
  echo json_encode($datajson);
  savelog("не верный пароль или логин " . " - " . $user_login);
  exit();
}


$query = "select user_id,user_name,user_level,user_localadmin, user_block from lift_users WHERE $checkusing = :user_login limit 1;";
$row = $DB->row($query,array("user_login"=>$user_login));
if ($row['user_block']) {
  $datajson = ['status' => 'error', 'message' => 'Пользователь заблокирован'];
  header('Content-type: application/json');
  echo json_encode($datajson);
  exit();
}
$user_id         = $row['user_id'];
$user_name       = $row['user_name'];
$user_level      = $row['user_level'];
$user_localadmin = $row['user_localadmin'];
//var_dump ($row);
if ($user_level == 0 || $user_localadmin) {
  $_SESSION['admin'] = 1;
} else {
  $_SESSION['user'] = 1;
}

$_SESSION['user_id']    = $user_id;
$_SESSION['user_name']  = $user_name;
$_SESSION['user_level'] = $user_level;

$_SESSION['hit'] = 0;
$link_cals = 'index_disp.php';
//$last_login = mktime($dateTime->format("n/j/y g:i a"));
$last_login = date(time());
//echo $dateTime->format("Y-m-d h:i:s");

if ($rez['user_level'] == '2' AND $data['mobile']!==Null) {
  // пользователь в группе ответсвенный
}


if (iconv_strlen($data['token']) != 32) {
  // ели аторизовались не по токену то меняем его

  if (isset($data['autoLogin']) and $data['autoLogin']) {
    $token    = $user_password . time();
    $token    = md5($token);
    $sqltoken = ",user_token='$token'";
  }else{
    $token='';
  }
}

$query = "UPDATE lift_users SET last_ip = '$lastip', webgl_info=:webgl , last_login = '$last_login'  $sqltoken WHERE user_id = :id";
$DB->query($query,array('id' => $user_id,'webgl' => $webgl));
$_SESSION['user_nacl'] = $main->nacl($user_id);

$datajson = ['status' => 'ok', 'message' => 'Успешно', 'url' => $url, 'id' => $user_id, 'token' => $token, 'userName' => $user_name];
header('Content-type: application/json');
echo json_encode($datajson);
exit();
function savelog($txt)
{
  global $lastip;
  $txt      = date('m/d/Y h:i:s ', time()) . " - " . $txt . " IP " . $lastip;
  $filename = "logs/loginerror" . date('m_y') . ".txt";
  file_put_contents($filename, $txt . PHP_EOL, FILE_APPEND);

}
function checkpwd($password,$user_login,$checkusing) {
global $DB;
    $hasher = password_hash($password, PASSWORD_DEFAULT);
    $stored_hash = "*";

        //if encryption is ON
        $query="SELECT user_password from lift_users WHERE $checkusing = :name LIMIT 1;";
        $stored_hash = $DB->single($query,array('name'=>$user_login));

        if(OLDHASH){
            include("includes/PasswordHash.php");
            $check = new PasswordHash(8, false);
            return $check->CheckPassword($password, $stored_hash);
        }

        if ($hasher===$stored_hash) {
            return TRUE;
        }else{
            return FALSE;
        }

    }


?>