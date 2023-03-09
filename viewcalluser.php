<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=.8">
  <meta name="csrf-token" content="fz39nNuPLyVKzX2hFqOZOQp69sksn2UWrQsBgqmR">
  <title>Информация по заявке</title>
  <meta name="description" content="Электронный журнал заявок по ремнту лифтов ->Информация по заявке<?php echo $user_name; ?>">
  <meta name="author" content="Zamotaev Anatoliy">
        <meta name="robots" contents="noindex">
        <style>
            body{
                background-color: black;
                color: white;
            }
        </style>
        </head>
        <body>
        
        <?php 
include ("include/ldisp_config.php");
include ("include/function.php");
if (isset($_REQUEST['callid']))
{
    $id=(int)$_REQUEST['callid'];
}
if(isset($_REQUEST['token'])){
    $md5=$_REQUEST['token'];
}else {
    echo "Ошибка токена";
    exit();
}
if($id<=1 OR $id>=1000000000000){
    echo "id error".$id;
    exit();
}
$myquery = "SELECT *  from lift_calls  WHERE (call_id = $id)  LIMIT 1";
try {
    $dbh = new PDO(db_PDO, db_user, db_password);
    $dbh->exec("set names utf8");
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage();
    die();
  }
  $sth = $dbh->query($myquery);
  $call= $sth->fetch(PDO::FETCH_ASSOC);
  if ($call['read_md5']!==$md5)
  {
    echo "Ошибка авторизации! Токен не соответсвует. Возможно Вы уже не являетесь ответственным по данной заявке";
exit();  
}
echo "<b>Адрес по заявке:</b>".$call['call_adres'];
echo "<br><b>Описание заявки:</b> " . $call['call_details'];
echo "<br> <b>Создал :</b>". $call['call_first_name'];
echo "<br> <b>Дата создания заявки: </b>". date("d.m.Y # h:m", $call['call_date']);
if (!$call['call_staff_status']){
    $qurestaffdate="call_staff_date=".strtotime(date('Y-m-d H:i:s ')).',';
    $updatestatus="UPDATE lift_calls SET  $qurestaffdate call_staff_status=2 WHERE  call_id=$id;";
    $stht=$dbh->query($updatestatus);
    echo "<br> Диспетчер уведомлен о том что Вы осведомлены о новой заявке";
}
?>
</body></html>