<?php
include("../../include/session.php");
include("../../include/checksession.php");
include("../../include/ldisp_config.php");
include("../../include/function.php");
//const nav = [true,true, true, true, true,true,true]; //разрешения [0 просмотр, 1 редактирование,2 зыкрыть,3 заметки,4 создание новой заявки, 5 редактировать ползователей, 6 редактировать объекты]
 if (isset($user_id)) {
  $nacl = nacl($user_id);

  if ($nacl != $user_nacl) {
    echo "alert ('Ошибка авторизации. Конфиг не загружен!');";
    echo "const nav = [false,false, false, false, false];";
    exit;
  }
 } else {
   echo "alert('invalid id конфиг не загружен ');";
   echo "const nav = [false,false, false, false, false];";
   exit;
 }
 DB::$dsn           = db_PDO;
 DB::$user          = db_user;
 DB::$pass          = db_password;
 $checkusr = (int) $user_id;
  $q        = "SELECT `user_add_call`, `user_localadmin`,`user_edit_obj`, `user_edit_user`, `user_level`, `user_disppermission` FROM `lift_users` WHERE `user_id`=$checkusr LIMIT 1";
  // $stuser=$dbh->query($q);
  //$userdata=$stuser->fetch(PDO::FETCH_ASSOC);
  $userdata = DB::getRow($q);
  $readcall='true'; //0
  $editcall="false";//1
  $closecall="false";//2
  $notecall="true";//3
  $addcallpermission="false";//4
  $edituserlink = "false";//5
  $editobjlink = "false";//6
 //если админ или пользователю разрешено редктирование  объектов
  if ($userdata['user_localadmin'] || $userdata['user_edit_obj']){$editobjlink = "true";}
//если админ или пользователю разрешено Управление пользователями
  if ($userdata['user_localadmin'] || $userdata['user_edit_user']){$edituserlink = "true";}
  //если диспетчер  или пользователю разрешено редктирование заявок
  if ($userdata['user_disppermission'] || ($userdata['user_level'] == 3)){
  $editcall="true";//1
  $closecall="true";//2
  }
  //если диспетчер  или пользователю разрешено создание заявок   
  if ($userdata['user_add_call'] || ($userdata['user_level'] == 3)){$addcallpermission = "true";}
  $stuser = null;
  echo "const nav=[$readcall,$editcall,$closecall,$notecall,$addcallpermission,$edituserlink,$editobjlink];";
 ?>