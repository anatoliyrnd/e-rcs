<?php
include("include/session.php");
include("include/checksession.php");
include("include/ldisp_config.php");
include("include/function.php");

 if (isset($user_id)) {
  $nacl = nacl($user_id);

  if ($nacl != $user_nacl) {
    echo "Ошибка авторизации <a href='/index.php'>На главную</a>";
   exit;
  }
 } else {
   echo "invalid id";
  exit;
 }


DB::$dsn           = db_PDO;
DB::$user          = db_user;
DB::$pass          = db_password;

$editobjlink       = '';
$edituserlink      = '';
$disppermission    = "false";
$addcallpermission = false;
$setting= false;
if (isset($user_id)) {
  $checkusr = (int) $user_id;
  $q        = "SELECT `user_add_call`, `user_localadmin`,`user_edit_obj`, `user_edit_user`, `user_level`, `user_disppermission` FROM `lift_users` WHERE `user_id`=$checkusr LIMIT 1";

  $userdata = DB::getRow($q);
  
  
  if ($userdata['user_localadmin'] or $userdata['user_edit_obj']) {
    //если админ или пользователю разрешено редктирование 
    $editobjlink = "<li><a href='/editobj.php' target='blank'><span>Управление базой адресов</span></a></li>";
  }
  if ($userdata['user_localadmin'] or $userdata['user_edit_user']) {
    //если админ или пользователю разрешено редктирование 
    $edituserlink = "<li><a href='user_edit.php' target='blank'><span>Управление пользователями</span></a></li>";
  }
  if ($userdata['user_disppermission'] or ($userdata['user_level'] == 3)) {
    //если диспетчер  или пользователю разрешено редктирование 
    $disppermission = "true";
  }
  if ($userdata['user_add_call'] or ($userdata['user_level'] == 3)) {
    //если диспетчер  или пользователю разрешено создание заявок 
    $addcallpermission = true;
  }
  if ($userdata['user_localadmin'] or !$userdata['user_level']) {
    //если админ или пользователю разрешено редктирование 
    $setting= true;
  } 
  $stuser = null;
}
?>
<!DOCTYPE html>
<html lang="ru">
<!-- <?php echo $nacl . " - " . $user_id . "name " . $user_name . " - " . $user_level; ?> -->

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=.8">
  <meta name="csrf-token" content="fz39nNuPLyVKzX2hFqOZOQp69sksn2UWrQsBgqmR">
  <title>Диспетчер. Журнал заявок. - <?php echo $user_name; ?> </title>
  <meta name="description"
    content="Электронный журнал заявок по ремнту лифтов ->Интерфейс диспетчера -><?php echo $user_name; ?>">
  <meta name="author" content="Zamotaev Anatoliy">
  <meta name="robots" contents="noindex">
  <link rel="icon" type="image/png" href="favicon.ico">
  <script type="text/javascript" src="/js/tabulator.js"></script>
  <link href="/css/tabulator.css" rel="stylesheet">
  <link href="/css/disp.css" rel="stylesheet">
  <link href="/css/disp_main.css" rel="stylesheet">
  <script type="text/javascript" src="js/generic/config.js"></script>

  

  <style>
    
  </style>
  <script>
   const nacl = "<?php echo $nacl; ?>";
  const user_name = "<?php echo $user_name; ?>";
  const user_id = "<?php echo $user_id; ?>";
  </script>
</head>

<body data-page='disp' class="sidebar-available ">
<div class="main_container">
    <div class="head">
      <div name="nav">
        <div id="toggle_head"><span></span></div>
        <div id="menu"><span id="title">Управление</span>
          <ul>
            <li name="clickMenu-newCall"><a href="#" class="new_call" ><span >Новая заявка-></span></a></li>
            <li name="clickMenu-openCalls"><a href="#"><span>Открытые заявки</span></a></li>
            <li name="clickMenu-closeCalls"><a href="#"><span>Недавно закрытые заявки</span></a></li>
            <hr>
           <?php echo  $editobjlink;
           echo $edituserlink; ?>
            <hr>
            <?php  if ($setting){echo "<li><a href='./setting.php'><span>Настройки</span></a></li>";} ?>
               <li><a href="#"><span>Помощь</span></a></li>
            <hr>
            <li><a href="index.php?e=loggeout"><span>Выйти</span></a></li>
          </ul>
        </div>
      </div>
      <div class="head_mesage" ><span id="head_mesage" >head</span>     <div class="loader-head" id="loader_head">
        <div class="circle-head"></div>
        <div class="circle-head"></div>
        <div class="circle-head"></div>
        <div class="shadow-head"></div>
        <div class="shadow-head"></div>
        <div class="shadow-head"></div>
        
    </div></div>
    </div>
    <div id="main_body" class="main_body">
      <div id="open_calls_table" name="open" class="mainchild" hidden></div>
      <div id="close_calls_table" name="close" class="mainchild"  hidden></div>
    </div>
  </div>

  <dialog class="confirm">
    <div class='content_dialog'>
      <section class="top-nav" id="menu_madal">
        <div class='title_dialog' id="title_dialog"></div>
        <input id="menu_dialog-modal" type="checkbox" />
        <label class='menu_dialog-button-container' for="menu_dialog-modal">
          <div class='menu_dialog-button'></div>
        </label>

      </section>
      <div class='body_dialog ' id="body_dialog"></div>
    </div><div class="modal_but">
    <button id='close' data-type='close'>Закрыть</button>
    <button id='save' data-type='confirm' disabled>Сохранить </button></div>
  </dialog>
  <script type="text/javascript">
   document.getElementById('toggle_head').addEventListener("click", function () {
      this.classList.toggle("open");
      document.getElementById("menu").classList.toggle("opened")
    })

  </script>


<script type="module" src="/js/disp.js"></script>

</body>

</html>
<!-- <a target="_blank" href="https://icons8.com/icon/523/о-нас">О нас</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a> -->