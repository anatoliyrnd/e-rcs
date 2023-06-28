<?php
require_once("./include/autoload.php");
$main=new \mainSRC\main();
$main->checkSession();
if (!$main->checkUser()){
    echo "Ошибка авторизации";
    $main->logSave("setting authorisation error id-".$main->getUserId()." name -".$main->getUserName(),"settings","setting");
    exit();
}
$nacl=$main->nacl();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Журнал по учету заявок на ремонт лифтов.Настройки</title>
<meta name="description" content="Электронный журнал заявок по ремнту лифтов -> Настройки" />
<meta name="author" content="Zamotaev Anatoliy" />
<script type="text/javascript" src=""></script>
<link rel="stylesheet" href="" />
</head>
<body>


</body>
</html>

