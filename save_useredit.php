<?php
//обработка полученных данных по пользователям v2.0
//(С) Замотаев Анатолйи Николаевич
include ("include/session.php");
include ("include/checksession.php");
include ("include/ldisp_config.php" );
include ("include/function.php");
$inputJSON = file_get_contents('php://input');
$input= json_decode( $inputJSON, TRUE ); 
function errorecho($txt){
    //возврат фронту ошибки ввиде json
    header('Content-type: application/json');
    echo '{"status":"'.$txt.'"}';
    exit();  
}
if((!isset($input['user_name'])) || (!isset($input['nacl'])) )
{
  errorecho("ошибка 5-1");
 
}
if ($user_name != $input['user_name']){
    errorecho("ошибка 5-2");
  }

if ($input['nacl']!=nacl($user_id)){
    errorecho("ошибка 5-3");


} 

try {
    $dbh = new PDO(db_PDO, db_user, db_password);
    $dbh->exec("set names utf8");
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage();
    die();
  }
  function checklogin($login){
    //проврека наличия логина в базе
    global $dbh;
    $sth = $dbh->prepare("SELECT user_login FROM `lift_users` WHERE `user_login`=:login");
    $sth->bindParam(':login', $login);
    $sth->execute();
    $count=$sth->fetchColumn(0);
 
    if($count){
        errorecho("$login  уже есть в базе данных ");
    }
  }
$user_id=(int)$user_id;
$q="SELECT `user_edit_user`, `user_localadmin`,`user_level`  FROM `lift_users` WHERE `user_id`=$user_id ";
  $user=$dbh->query($q);  
  $userdata=$user->fetch(PDO::FETCH_ASSOC);
if ($userdata['user_localadmin'] || !$userdata['user_level']){$useradmin=true;}
if (!$userdata['user_localadmin'] && !$userdata['user_edit_user']){
    errorecho("Вам запрещен доступ к реактированию пользователей");
    

}
$user=null;
if (isset($input['type']))
{
  if($input['type']=="new"){
  //если новый пользователь то  
  $params=[];
   // INSERT INTO `lift_users` SET `user_login`='123', `user_password`='123', `user_name`='123', `user_phone`='123',  `user_localadmin`='0', `user_edit_obj`='0', `user_edit_user`='0', `user_disppermission`='0', `user_add_call`='0', `user_read_all_calls`='0'
   $q="INSERT INTO `lift_users` SET ";
   if(isset($input['name'])){
     $len=strlen($input['name']);
         if ($len<=6){errorecho("Длина ФИО $len - это менее 7 символов");}
   }else{
     errorecho("Вы наверное забыли ввести ФИО"); 
   }
   $q.="`user_name`=:name, ";
   $params['name']=$input['name'];
   if(isset($input['login'])){
    $rez= preg_replace("/[^A-Za-z\d]/",'', $input['login']);
    checklogin($rez);
    $len=strlen($rez);
        if ($len<=4){errorecho("Длина Login $rez -  $len - это менее 5 символов");}
  }else{
    errorecho("Вы наверное забыли ввести Login"); 
  }
  $q.="`user_login`=:login, ";
  $params['login'] =$rez;

  if(isset($input['pass'])){
    $len=strlen($input['pass']);
        if ($len<=6){errorecho("Длина пароля $len - это менее 7 символов");}
  }else{
    errorecho("Вы наверное забыли ввести пароль"); 
  } 
 $q.=" `user_password`=:pass, ";
if (ENCRYPTED_PASSWORD) {
        //if encryption is ON
        $params['pass'] = password_hash($input['pass'], PASSWORD_DEFAULT);

      if(OLDHASH){
        include("includes/PasswordHash.php");
        $hasher = "*";
		$hasher = new PasswordHash(8, false);
		$params['pass'] = $hasher->HashPassword($input['pass']);
         
      }
    }else{
         $params['pass']=$input['pass'];
    }
    if (isset($input['phone'])){
        $q.="`user_phone`=:phone, ";
        $params['phone']=str_replace(array('-', '(', ')', ' '), '', $input['phone']);
    }
    $adm="`user_localadmin`=0, ";

    if ($input['admin'] && $useradmin){    
            $adm="`user_localadmin`=1, ";
    }
    $q.=$adm;
    if ($input['edit_obj']){$q.="`user_edit_obj`=1, ";}else {$q.="`user_edit_obj`=0, ";}
    if ($input['edit_user']){$q.="`user_edit_user`=1, ";}else {$q.="`user_edit_user`=0, ";}
    if ($input['disppermission']){$q.="`user_disppermission`=1, ";}else {$q.="`user_disppermission`=0, ";}
    if ($input['add_call']){$q.="`user_add_call`=1, ";}else {$q.="`user_add_call`=0, ";}
    if ($input['read_all_calls']){$q.="`user_read_all_calls`=1, ";}else {$q.="`user_read_all_calls`=0, ";}
    $userlevel=(int)$input['user_level'];
    if($userlevel){$q.="`user_level`=$userlevel ";}else{$q.="`user_level`=1 ";}
    $stmt = $dbh->prepare($q);
    $rez=$stmt->execute($params);
    $stmt=null;
//конец блока добавления нового пользователя
  }else {
    //остальное считаем что редактирование существующего пользователя
    //и сразу проверим прислали ли id пользователя
    if (!isset($input['user_edit_id'])){
        errorecho("Ошибка! Не передан идентификатор пользователя");
     
    }
  $usereditid=(int)$input['user_edit_id'];
  $q="SELECT `user_protect_edit`  FROM `lift_users` WHERE `user_id`=$usereditid";

  $edituser=$dbh->query($q); 
  if ($edituser->fetchColumn()){errorecho("Запрещено редактированние данного пользователя");}
  $q="UPDATE `lift_users` SET ";
  $params=[];

if (isset($input['name'])){
    $len=strlen($input['name']);
    if ($len<=6){errorecho("Длина ФИО $len - это менее 7 символов");}
 $q.="`user_name`=:name, ";
$params['name']=$input['name'];
}
if (isset($input['login'])){
    $rez= preg_replace("/[^a-z\d]/",'', $input['login']);
    checklogin($rez);
    $len=strlen($rez);
        if ($len<=4){errorecho("Длина Login $rez -  $len - это менее 5 символов");}
    $q.="`user_login`=:login, ";
    $params['login']=$rez;
}
if (isset($input['pass'])){
    $len=strlen($input['pass']);
    if ($len<=6){errorecho("Длина пароля $len - это менее 7 символов");}
    $q.="`user_password`=:pass, ";
    if (ENCRYPTED_PASSWORD) {
        //if encryption is ON
        $params['pass'] = password_hash($input['pass'], PASSWORD_DEFAULT);

      if(OLDHASH){
        include("includes/PasswordHash.php");
        $hasher = "*";
		$hasher = new PasswordHash(8, false);
		$params['pass'] = $hasher->HashPassword($input['pass']);
         
      }
    }else{
         $params['pass']=$input['pass'];
    }
}
if (isset($input['phone'])){
    $q.="`user_phone`=:phone, ";
    $params['phone']=str_replace(array('-', '(', ')', ' '), '', $input['phone']);
}
$adm="`user_localadmin`=0, ";

if ($input['admin'] && $useradmin){    
        $adm="`user_localadmin`=1, ";
}
$q.=$adm;
if ($input['edit_obj']){$q.="`user_edit_obj`=1, ";}else {$q.="`user_edit_obj`=0, ";}
if ($input['edit_user']){$q.="`user_edit_user`=1, ";}else {$q.="`user_edit_user`=0, ";}
if ($input['disppermission']){$q.="`user_disppermission`=1, ";}else {$q.="`user_disppermission`=0, ";}
if ($input['add_call']){$q.="`user_add_call`=1, ";}else {$q.="`user_add_call`=0, ";}
if ($input['read_all_calls']){$q.="`user_read_all_calls`=1, ";}else {$q.="`user_read_all_calls`=0, ";}
if ($input['user_block']){$q.="`user_block`=1, ";}else {$q.="`user_block`=0, ";}

$userlevel=(int)$input['user_level'];
if($userlevel){$q.="`user_level`=$userlevel ";}else{$q.="`user_level`=1 ";}
  $q.=" WHERE `user_id` = $usereditid";
  //конец блока изменения пользователя
 
    $stmt = $dbh->prepare($q);
    $rez=$stmt->execute($params);
    $stmt=null;
 }

  }else{
    errorecho ("Не передана информация");
    
}

if ($rez)
{
    header('Content-type: application/json');
    echo '{"status":"ok"}';
}

?>

