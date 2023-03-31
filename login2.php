<?php
ob_start();
include("include/session.php");
include("include/ldisp_config.php");
include("include/function.php");
$is_valid  = false;
$url       = $HOSTURL . "/index_disp.php";
$mobileurl = $HOSTURL . "/mobile_user.php";
$lastip    = $_SERVER['REMOTE_ADDR'];
$sqltoken  = '';
sleep(4);
if (!isset($_SESSION['auth'])) {

  $datajson = ['status' => 'Ошибка авторизации  1 ', 'alerttxt' => ''];
  header('Content-type: application/json');
  echo json_encode($datajson);
  savelog('Ошибка авторизации  1 ');
  exit();
}

//limit login tries.
if (isset($_SESSION['hit'])) {
  $_SESSION['hit'] += 1;
  if ($_SESSION['hit'] > LOGIN_TRIES) {
    $datajson = ['status' => 'error', 'message' => 'Доступ заблокирован '];
    header('Content-type: application/json');
    echo json_encode($datajson);
    savelog('Доступ заблокирован');
    exit();
  }
} else {
  //	$_SESSION['hit'] = 0;
}
//авторизация
$entityBody = file_get_contents('php://input');
$data       = get_object_vars(json_decode($entityBody));


if (!isset($data['name']) and !isset($data['token'])) {
  $datajson = ['status' => 'error', 'message' => "Ошибка данных"];
  header('Content-type: application/json');
  echo json_encode($datajson);
  exit();

}

$user_login = trim($data['name']);

if ((iconv_strlen($user_login) < 4) and !isset($data['id'])) {
  $datajson = ['status' => 'error', 'message' => "Логин короткий"];
  header('Content-type: application/json');
  echo json_encode($datajson);
  exit();
}
if (iconv_strlen($data['token']) == 32) {
  // если есть токен авторизации из локалного хранилища браузера то проверим его на валидноть
  try {
    $dbhtok = new PDO(db_PDO, db_user, db_password);
    $dbhtok->exec("set names utf8");
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage();
    die();
  }
  $query  = "select user_login, user_token, user_name, user_level,user_localadmin, user_block from lift_users WHERE user_id=:ID limit 1;";
  $sthtok = $dbhtok->prepare($query);
  $sthtok->bindParam(':ID', $data['id']);
  $sthtok->execute();
  $rez = $sthtok->fetch(PDO::FETCH_ASSOC);
  if ($rez['user_token'] === $data['token']) {
    // автологин по токену успешно
    
    $is_valid   = true; //флаг успешности авторизации
    $user_login = $rez['user_login'];
    $checkusing = "user_login"; // для правельной авторизации по логину
  } else {
    $datajson = ['status' => 'error', 'message' => "не верный токен"];
    header('Content-type: application/json');
    echo json_encode($datajson);
    $txt = "Не верный токен  для login_id" . $data['id'] . "token sql -" . $rez['user_token'] . " token post -" . $data['token'];
    savelog($txt);
    exit();
  }
}
if (isset($data['pname'])) {
  $user_password = trim($data['pname']);
  $is_valid      = checkpwd($user_password, $user_login);
  $pos           = strrpos($user_login, "@");
  if ($pos === false) { // note: three equal signs 
    $checkusing = "user_login";
  } else {
    $checkusing = "user_email";
  }
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

try {
  $dbh = new PDO(db_PDO, db_user, db_password);
  $dbh->exec("set names utf8");
} catch (PDOException $e) {
  print "Error!: " . $e->getMessage();
  die();
}
$query = "select user_id,user_name,user_level,user_localadmin, user_block from lift_users WHERE $checkusing = :user_login limit 1;";
$stht  = $dbh->prepare($query);
$stht->bindParam(':user_login', $user_login);
$stht->execute();
$row = $stht->fetch(PDO::FETCH_ASSOC);
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
if ($_SESSION['admin'] == 1) {
  //$link_cals='adm_start.php';
} else {
  //$link_cals='user.php';	
}


//$last_login = mktime($dateTime->format("n/j/y g:i a"));
$last_login = date(time());
//echo $dateTime->format("Y-m-d h:i:s");


if ($rez['user_level'] == '2' AND $data['mobile']!==Null) {
  // пользователь в группе ответсвенный
  $url = $mobileurl;
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

$query = "UPDATE lift_users SET last_ip = '$lastip',last_login = '$last_login'  $sqltoken WHERE user_id = :id";

$stht = $dbh->prepare($query);
$stht->execute(array('id' => $user_id));
$_SESSION['user_nacl'] = nacl($user_id);

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

// eold
if (true) {
  echo '<script type="text/javascript"> ';
  echo 'setTimeout(function(){';
  echo ' window.location.href = "' . $link_cals . '";}, 1000);</script> ';
  echo ' ';
} else {
  echo '<a href="index_disp.php">Войти как диспетчер</a><br>';
  echo '<a href="adm_start.php">Войти как админ</a><br>';
  echo '<a href="user.php"> Войти как ответсвенный</a><br>';
}
?>