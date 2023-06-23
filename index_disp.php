<?php
include("include/autoload.php");

use mainSRC\main;


$main_function = new main();
//$main_function->checkSession();
$main_function->checkUser();
$permission=$main_function->getUserPermission();
/*
 * 0-read call
 * 1-edit_call
 * 2-close_call
 * 3-note_call
 * 4-add_call_permission
 * 5-edit_user_link
 * 6-edit_obj_link
 */

    //если админ или пользователю разрешено редктирование
  ($permission[6])?$edit_obj_link = "<li><a href='/editobj.php' target='blank'><span>Управление базой адресов</span></a></li>":$edit_obj_link ="" ;
    //если админ или пользователю разрешено редктирование
 ($permission[5])?$edituserlink = "<li><a href='user_edit.php' target='blank'><span>Управление пользователями</span></a></li>":$edituserlink="";
    //если диспетчер  или пользователю разрешено создание заявок
($permission[4])?$add_call_link="<li name='clickMenu-newCall'><a href='#' class='new_call' ><span >Новая заявка-></span></a></li>":$add_call_link="";
  //if ($userdata['user_localadmin'] or !$userdata['user_level']) {
    //если админ или пользователю разрешено редктирование 
  //  $setting= true;
 // }
//  //если диспетчер  или пользователю разрешено редктирование
 ////(!$userdata['user_disppermission'] or ($userdata['user_level'] == 3))?:$disppermission = "true";
  $stuser = null;

?>
<!DOCTYPE html>
<html lang="ru">
<!-- <?php //echo $nacl . " - " . $user_id . "name " . $user_name . " - " . $user_level; ?> -->

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=.8">
  <meta name="csrf-token" content="fz39nNuPLyVKzX2hFqOZOQp69sksn2UWrQsBgqmR">
  <title>Диспетчер. Журнал заявок. - <?php echo $main_function->getUserName(); ?> </title>
  <meta name="description"
    content="Электронный журнал заявок по ремнту лифтов ->Интерфейс диспетчера -><?php echo $main_function->getUserName(); ?>">
  <meta name="author" content="Zamotaev Anatoliy">
  <meta name="robots" contents="noindex">
  <link rel="icon" type="image/png" href="favicon.ico">
  <script type="text/javascript" src="/js/tabulator.js"></script>
  <link href="/css/tabulator.css" rel="stylesheet">
  <link href="/css/disp.css?v2-003" rel="stylesheet">
  <link href="/css/disp_main.css?v2-003" rel="stylesheet">
<!--  <script type="text/javascript" src="js/generic/config.js"></script>-->

  

  <style>
    
  </style>
  <script>
      const nacl = "<?php //echo $main_function->getUserNacl(); ?>";
  const user_name = "<?php echo $main_function->getUserName(); ?>";
  const user_id = "<?php echo $main_function->getUserId(); ?>";
  </script>
</head>

<body data-page='disp' class="sidebar-available ">
<div class="main_container">
    <div class="head">
      <div name="nav">
        <div id="toggle_head"><span></span></div>
        <div id="menu"><span id="title">Управление</span>
          <ul>
            <?php echo $add_call_link; ?>
            <li name="clickMenu-openCalls"><a href="#"><span>Открытые заявки</span></a></li>
            <li name="clickMenu-closeCalls"><a href="#"><span>Недавно закрытые заявки</span></a></li>
            <hr>
           <?php echo  $edit_obj_link;
           echo $edituserlink; ?>
            <hr>
            <?php  if ($setting){echo "<li><a href='./setting.php'><span>Настройки</span></a></li>";} ?>
               <li><a href="#" id="help"><span>Помощь</span></a></li>
            <hr>
            <li><a href="index.php?e=loggeout"><span>Выйти</span></a></li>
          </ul>
        </div>
      </div>
      <div class="head_message" ><span id="head_message" >head </span>   <div class="loader-head" id="loader_head">
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
    <button id='close' data-type='close'>Закрыть <label class="button_close" id="closeTimer"></label></button>
    <button id='save' data-type='confirm' disabled>Сохранить </button></div>
  </dialog>
  <script type="text/javascript">
   document.getElementById('toggle_head').addEventListener("click", function () {
      this.classList.toggle("open");
      document.getElementById("menu").classList.toggle("opened")
    })

  </script>

<canvas id="glcanvas" width="0" height="0"></canvas>
<script type="module" src="/js/disp.js?v2-005"></script>
<div id="countdownBar" class="countdownBar hidden"></div>;
</body>

</html>