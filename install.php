<?php


 include_once("./DBConfig.php");
 
    $dsn = 'mysql:';
    $dsn .= 'host=' . DBHost . ';';
    $dsn .= 'port=' .DBPort . ';';
    if (!empty(DBName)) {
        $dsn .= 'dbname=' . DBName . ';';
    }
    $dsn .= 'charset=utf8;';
   $pdo = new PDO($dsn,
        DBUser,
        DBPassword,
        array(
            //For PHP 5.3.6 or lower
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_EMULATE_PREPARES => false,
            //PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::MYSQL_ATTR_FOUND_ROWS => true
        )
    );



  
   $sql = file_get_contents('new.sql');
 
   $pdo->exec($sql);
  $qr=$pdo->exec("DELETE FROM `lift_history` WHERE `lift_history`.`history_id` = 1;");
    if($qr){
     echo "импорт базы данных успешен <br>Для входа используйте логин <b>admin</b> пароль <b>12345</b>";
  ?> 
  <script>
    setTimeout(() => {
        window.location.href = 'index.php';
    }, 10000);
  </script>
  <?php 
   }else{
     echo "При импорте произошла ошибка <br>";
     var_dump($qr);
   }



