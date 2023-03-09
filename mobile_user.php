<?php
include ("include/session.php");
include ("include/checksession.php");
include ("include/ldisp_config.php" );
include ("include/function.php");
// файл пользовательского интерфейса ответсвенного лица для мобильных устройств v.2.0
//Zamotaev A.N.
 



if (isset($user_id)){
  $nacl=nacl($user_id);

  if ($nacl!=$user_nacl){echo "Ошибка авторизации";
    exit;
  }
}else{
  echo "invalid id";
  exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<!-- <?php echo $nacl." - ".$user_id. "name ".$user_name." - ".$user_level;  ?> -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="token" content="fz39nNuPLyVKzX2hFqOZOQp69sksn2UWrQsBgqmR">
    <title>Мои заявки.</title>
    <meta name="description"  content="Электронный журнал заявок по ремнту лифтов мобильный пользовательский интерфейс?php echo $user_name; ?>">
    <meta name="author" content="Zamotaev Anatoliy">
    <meta name="robots" contents="noindex">
    <link rel="icon" type="image/png" href="favicon.ico">
    <script type="text/javascript" src="user/user.js"></script>
    <link href="/user/user.css" rel="stylesheet">
   </head>
   <body>


<div id="container">
    <div id="header">
        <!-- Header start -->
        заголовок
        <!-- Header end -->
    </div>
    <div id="body" class="fadeIn">
        Получение данных...
        <!-- Body start -->
        <!-- card end -->

        <!-- Body end -->
    </div>
    <div id="footer">
        <!-- Footer start -->
        <div class="phone">
            <input type="radio" name="s" id="s1">
            <input type="radio" name="s" id="s2" checked="checked">
            <input type="radio" name="s" id="s3">
            <label for="s1"><img src="user/img/archive.svg" alt=""></label>
            <label for="s2"><img src="user/img/tool.svg" alt=""></label>
            <label for="s3"><img src="user/img/settings.svg" alt=""></label>
            <div class="circle"></div>
            <div class="phone_content">
                <div class="phone_bottom">
                    <span class="indicator">dfddf</span>
                </div>
            </div>
        </div>   <!-- Footer end -->
    </div>
</div>
<script>
const nacl="<?php echo $nacl; ?>";
const userId="<?php echo $user_id; ?>";
let nodeInput='';
let closeInput='';
const data = {
    start:true,
    openCalls: [
        {
            call_id: 0,
            call_staff_status: "0",
            call_adres: "Получаем данные",
            call_details: "получаем данные",
            call_date: "",
           
        }
    ],
    closeCalls: [
        {
            call_id: 0,
            new: false,
            call_adres: "Получаем данные",
            call_details: "получаем данные",
            call_relation: "Решение по заявке",
            call_date: "",
            call_date2: ""
        }
    ],
    notes:[{
        id:0,
        notes: "Получаем данные"
    }]
}
</script>
   </bode>
   </html>


